cmOper = {
	states: {
		C: {
			descr: "Concedido",
			color: "#CC0000"
		},
		D: {
			descr: "Disponible",
			color: "#CC0000"
		}
	},
	statesCondicionConc: {
		P: {
			descr: "Permanente",
			color: "#003265" 
		},
		T: {
			descr: "Temporal",
			color: "#CCCCCC"
		}
	},
	statesNicho: {
		N: {
			descr: "Normal",
			color: "#003265"
		},
		P: {
			descr: "P&aacute;rvulo",
			color: "#CCCCCC" 
		}
	},
	statesTipoMaus: {
		B: {
			descr: "Nicho o B&oacute;veda",
			color: "#006532"
		},
		C: {
			descr: "Capilla",
			color: "#006532"
		},
		R: {
			descr: "Cripta",
			color: "#006532" 
		}
	},
	statesZonaMaus: {
		N: {
			descr: "Normal",
			color: "#006532"
		},
		P: {
			descr: "Preferencial",
			color: "#006532"
		},
      A:{
			descr: "A",
			color: "#006532"
		},
      B: {
			descr: "B",
			color: "#006532"
		},
      C:{
			descr: "C",
			color: "#006532"
		}
	},
	days: 60,
	tipo_hist: [
		{cod: 'CS',label: 'Concesi&oacute;n'},
		{cod: 'CT',label: 'Construcci&oacute;n'},
		{cod: 'AS',label: 'Concesi&oacute;n de Nicho en Vida'},
		{cod: 'TP',label: 'Traspaso'},
		{cod: 'IN',label: 'Inhumaci&oacute;n'},
		{cod: 'TI',label: 'Traslado Interno'},
		{cod: 'TE',label: 'Traslado Externo'},
		{cod: 'CO',label: 'Colocaci&oacute;n'},
		{cod: 'CV',label: 'Conversi&oacute;n'},
		{cod: 'TEO',label: 'Traslado Externo (Desde Otro Cementerio)'},
		{cod: 'CN',label: 'Cambio de Nombre en Recibo de Caja'}
	],
	showDetails: function(p){
		if(p.data.concesion != null)
			cmOper.windowDetailsConc({id: p.data._id.$id,modal: true});
		if(p.data.construccion != null)
			cmOper.windowDetailsCons({id: p.data._id.$id,modal: true});
		if(p.data.ampliacion != null)
			cmOper.windowDetailsAmp({id: p.data._id.$id,modal: true});
		if(p.data.asignacion != null)
			cmOper.windowDetailsAsig({id: p.data._id.$id,modal: true});
		if(p.data.inhumacion != null)
			cmOper.windowDetailsInhu({id: p.data._id.$id,modal: true});
		if(p.data.traslado != null)
			cmOper.windowDetailsTras({id: p.data._id.$id,modal: true});
		if(p.data.traspaso != null)
			cmOper.windowDetailsTrasp({id: p.data._id.$id,modal: true});
		if(p.data.colocacion != null)
			cmOper.windowDetailsColo({id: p.data._id.$id,modal: true});
		if(p.data.adjuntacion != null)
			cmOper.windowDetailsAdju({id: p.data._id.$id,modal: true});
		if(p.data.anular_asignacion != null)
			cmOper.windowDetailsAnulAsig({id: p.data._id.$id,modal: true});
		if(p.data.anular_concesion != null)
			cmOper.windowDetailsAnulConc({id: p.data._id.$id,modal: true});
		if(p.data.conversion != null)
			cmOper.windowDetailsConver({id: p.data._id.$id,modal: true});
		if(p.data.traslado_ext != null)
			cmOper.windowDetailsTrasExt({id: p.data._id.$id,modal: true});
	},
	contextMenuOper: function(p){
		p.$this.contextMenu("conMenCmOper", {
			onShowMenu: function(e, menu) {
				var excep='';
				//$('#conMenCmOper_pro,#conMenCmOper_ocu,#conMenCmOper_editPro,#conMenCmOper_regiOcup',menu).remove();
				//if(K.session.taks["cm.oper.conc"]=="0")$('#conMenCmOper_coc',menu).remove();
				
				if(K.session.tasks["cm.oper.conc"]=="0")excep+='#conMenCmOper_coc';
				else if(K.session.tasks["cm.oper"]=="0")excep+='#conMenCmOper_asi,#conMenCmOper_cos,#conMenCmOper_inh,#conMenCmOper_traExt,#conMenCmOper_traInt,#conMenCmOper_col,#conMenCmOper_trs,#conMenCmOper_anc,#conMenCmOper_ana';
				//$(excep+',#conMenCmOper_pro,#conMenCmOper_ocu,#conMenCmOper_editPro,#conMenCmOper_regiOcup',menu).remove();
				$(excep+',#conMenCmOper_pro,#conMenCmOper_ocu,#conMenCmOper_editPro',menu).remove();
				
				K.tmp = $(e.target).closest('.item');
				return menu;
			},
			bindings: {
				'conMenCmOper_coc': function(t) {
					if(p.data != null)
						cmOper.windowNewConcesion({entidad: p.data.entidad});
					else 
						cmOper.windowNewConcesion();
				},
				'conMenCmOper_asi': function(t) {
					if(p.data != null)
						cmOper.windowNewAsignacion({entidad: p.data.entidad});
					else 
						cmOper.windowNewAsignacion();
				},
				'conMenCmOper_cos': function(t) {
					if(p.data != null)
						cmOper.windowNewConstruccion({entidad: p.data.entidad});
					else 
						cmOper.windowNewConstruccion();
				},
				'conMenCmOper_amp': function(t) {
					if(p.data != null)
						cmOper.windowNewAmpliacion({entidad: p.data.entidad});
					else 
						cmOper.windowNewAmpliacion();
				},
				'conMenCmOper_inh': function(t) {
					if(p.data != null)
						cmOper.windowNewInhumacion({entidad: p.data.entidad});
					else 
						cmOper.windowNewInhumacion();
				},
				'conMenCmOper_traExt': function(t) {
					if(p.data != null)
						cmOper.windowNewTraslado({entidad: p.data.entidad, tipotras: 2});
					else 
						cmOper.windowNewTraslado({tipotras:2});
				},
				'conMenCmOper_traExtExt': function(t) {
					if(p.data != null)
						cmOper.windowNewTrasladoExt({entidad: p.data.entidad});
					else 
						cmOper.windowNewTrasladoExt();
				},
				'conMenCmOper_traInt': function(t) {
					if(p.data != null)
						cmOper.windowNewTraslado({entidad: p.data.entidad, tipotras: 0});
					else 
						cmOper.windowNewTraslado({tipotras:0});
				},
				'conMenCmOper_traCoa': function(t) {
					if(p.data != null)
						cmOper.windowNewTraslado({entidad: p.data.entidad, tipotras: 1});
					else 
						cmOper.windowNewTraslado({tipotras:1});
				},
				'conMenCmOper_col': function(t) {
					if(p.data != null)
						cmOper.windowNewColo({entidad: p.data.entidad});
					else 
						cmOper.windowNewColo();
				},
				'conMenCmOper_trs': function(t){
					if(p.data != null)
						cmOper.windowNewTrasp({entidad: p.data.entidad});
					else 
						cmOper.windowNewTrasp();
				},
				'conMenCmOper_anc': function(t){
					if(p.data != null)
						cmOper.windowAnulCon({entidad: p.data.entidad});
					else 
						cmOper.windowAnulCon();
				},
				'conMenCmOper_ana': function(t){
					if(p.data != null)
						cmOper.windowAnulAsig({entidad: p.data.entidad});
					else 
						cmOper.windowAnulAsig();
				},
				'conMenCmOper_regiOcup': function(t){
					if(p.data != null)
						cmOper.windowRegiOcup({entidad: p.data.entidad});
					else 
						cmOper.windowRegiOcup();
				},
				'conMenCmOper_anu': function(t){
					if(p.data != null)
						cmOper.windowAnuOper({entidad: p.data.entidad});
					else 
						cmOper.windowAnuOper();
				},
				'conMenCmOper_ren': function(t){
					if(p.data != null)
						cmOper.windowReno({entidad: p.data.entidad});
					else 
						cmOper.windowReno();
				},
				'conMenCmOper_con': function(t){
					if(p.data != null)
						cmOper.windowConver({entidad: p.data.entidad});
					else 
						cmOper.windowConver();
				},
				'conMenCmOper_rel': function(t){
					cmOper.windowRelacion();
				}
			}
		});
		p.$this.mouseup(function(){
	    	var top = $(this).offset().top + $(this).height();
	    	var left = $(this).offset().left;
	    	var date = new Date();
	    	p.$this.attr('id','btnTmpOper'+date.getSeconds());
			setTimeout('cmOper.showContext($("#'+p.$this.attr('id')+'"),'+top+','+left+')',10);
		});
	},
	showContext: function($this,top,left){
		$this.trigger('contextmenu');
		$('#jqContextMenu').show().css({
			"top": top+'px',
			"left": left+'px'
		});
		$('#jqContextMenu').next('div').show().css({
			"top": (top+2)+'px',
			"left": (left+2)+'px'
		});
		$('#jqContextMenu').next('div').css('z-index',$.ui.dialog.maxZ);
		$('#jqContextMenu').css('z-index',$.ui.dialog.maxZ+1);
	},
	newOper: function(p){
		new K.Modal({
			id: 'windowOper',
			title: 'Registro de Operaciones',
			contentURL: 'cm/oper',
			width: 550,
			height: 240,
			store: false,
			buttons: {
				'Cancelar': function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowOper');
				p.$w.find('[name=btnConc]').click(function(){
					cmOper.windowNewConcesion({entidad: p.data.propietario,espacio: p.data});
					K.closeWindow(p.$w.attr('id'));
				}).button({icons: {primary: 'ui-icon-bookmark'}});
				p.$w.find('[name=btnAsig]').click(function(){
					cmOper.windowNewAsignacion({entidad: p.data.propietario,espacio_ori: p.data});
					K.closeWindow(p.$w.attr('id'));
				}).button({icons: {primary: 'ui-icon-tag'}});
				p.$w.find('[name=btnCons]').click(function(){
					cmOper.windowNewConstruccion({entidad: p.data.propietario,espacio_ori: p.data});
					K.closeWindow(p.$w.attr('id'));
				}).button({icons: {primary: 'ui-icon-home'}});
				p.$w.find('[name=btnAmpl]').click(function(){
					cmOper.windowNewAmpliacion({entidad: p.data.propietario,espacio_ori: p.data});
					K.closeWindow(p.$w.attr('id'));
				}).button({icons: {primary: 'ui-icon-home'}});
				p.$w.find('[name=btnInhu]').click(function(){
					cmOper.windowNewInhumacion({entidad: p.data.propietario,espacio_ori: p.data});
					K.closeWindow(p.$w.attr('id'));
				}).button({icons: {primary: 'ui-icon-person'}});
				p.$w.find('[name=btnTrin]').click(function(){
					cmOper.windowNewTraslado({tipotras:0,entidad: p.data.propietario,espacio_ori: p.data});
					K.closeWindow(p.$w.attr('id'));
				}).button({icons: {primary: 'ui-icon-transfer-e-w'}});
				p.$w.find('[name=btnTreh]').click(function(){
					cmOper.windowNewTraslado({tipotras:2,entidad: p.data.propietario,espacio_ori: p.data});
					K.closeWindow(p.$w.attr('id'));
				}).button({icons: {primary: 'ui-icon-extlink'}});
				p.$w.find('[name=btnTrec]').click(function(){
					cmOper.windowNewTraslado({tipotras:1,entidad: p.data.propietario,espacio_ori: p.data});
					K.closeWindow(p.$w.attr('id'));
				}).button({icons: {primary: 'ui-icon-extlink'}});
				p.$w.find('[name=btnColo]').click(function(){
					cmOper.windowNewColo({entidad: p.data.propietario,espacio_ori: p.data});
					K.closeWindow(p.$w.attr('id'));
				}).button({icons: {primary: 'ui-icon-copy'}});
				p.$w.find('[name=btnTras]').click(function(){
					cmOper.windowNewTrasp({entidad: p.data.propietario,espacio_ori: p.data});
					K.closeWindow(p.$w.attr('id'));
				}).button({icons: {primary: 'ui-icon-bookmark'}});
				p.$w.find('[name=btnFico]').click(function(){
					cmOper.windowAnulCon({entidad: p.data.propietario,espacio_ori: p.data});
					K.closeWindow(p.$w.attr('id'));
				}).button({icons: {primary: 'ui-icon-cancel'}});
				p.$w.find('[name=btnReas]').click(function(){
					cmOper.windowAnulAsig({entidad: p.data.propietario,espacio_ori: p.data});
					K.closeWindow(p.$w.attr('id'));
				}).button({icons: {primary: 'ui-icon-tag'}});
				p.$w.find('[name=btnRecc]').click(function(){
					cmOper.windowRegiOcup({entidad: p.data.propietario,espacio_ori: p.data});
					K.closeWindow(p.$w.attr('id'));
				}).button({icons: {primary: 'ui-icon-tag'}});
				p.$w.find('[name=btnReno]').click(function(){
					cmOper.windowReno({entidad: p.data.propietario,espacio_ori: p.data});
					K.closeWindow(p.$w.attr('id'));
				}).button({icons: {primary: 'ui-icon-refresh'}});
				p.$w.find('[name=btnAnop]').click(function(){
					cmOper.windowAnuOper({entidad: p.data.propietario,espacio_ori: p.data});
					K.closeWindow(p.$w.attr('id'));
				}).button({icons: {primary: 'ui-icon-circle-close'}});
				p.$w.find('[name=btnConv]').click(function(){
					cmOper.windowConver({entidad: p.data.propietario,espacio_ori: p.data});
					K.closeWindow(p.$w.attr('id'));
				}).button({icons: {primary: 'ui-icon-circle-close'}});
				p.$w.find('[name=btnIngr]').click(function(){
					cmOper.windowRelacion({entidad: p.data.propietario,espacio_ori: p.data});
					K.closeWindow(p.$w.attr('id'));
				}).button({icons: {primary: 'ui-icon-circle-close'}});
				p.$w.find('[name=btnTras]').click(function(){
					cmOper.windowNewTrasp({entidad: p.data.propietario,espacio_ori: p.data});
					K.closeWindow(p.$w.attr('id'));
				}).button({icons: {primary: 'ui-icon-refresh'}});
				K.block({$element: p.$w});
				$.post('mg/enti/get',{_id: p.data.propietario._id.$id},function(data){
					p.data.propietario = data;
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	payment: function(p){
		this.loadData = function(){
			p.loadConc();
		};
		this.getPay = function(){
			var $table,servicio,conceptos,cuotas=0,data,conc=[];
			if(p.$w.find('[name=tabs]').tabs("option","active")==0){
				data = {};
				$table = p.$w.find('[id^=tabsConcPayment]').eq(0);
				servicio = $table.find('[name^=serv]').data('data');
				conceptos = $table.find('[name^=serv]').data('concs');
				if(servicio==null){
					$table.find('[name^=btnServ]').click();
					K.notification({title: 'Servicio no seleccionado',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
					return false;
				}
				if($table.find('[name=fecven_pag]').val()==''){
					$table.find('[name=fecven_pag]').datepicker('show');
					K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha de vencimiento!',type: 'error'});
					return false;
				}
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
							cod: conceptos[i].clasificador.cod/*,
							cuenta: {
								_id: conceptos[i].clasificador.cuenta._id.$id,
								cod: conceptos[i].clasificador.cuenta.cod,
								descr: conceptos[i].clasificador.cuenta.descr
							}*/
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
					conc.push(tmp);
				}
				$.extend(data,{
					servicio: {
						_id: servicio._id.$id,
						nomb: servicio.nomb,
						organizacion: {
							_id: servicio.organizacion._id.$id,
							nomb: servicio.organizacion.nomb
						}
					},
					fecven: $table.find('[name=fecven_pag]').val(),
					moneda: 'S',
					conceptos: conc,
					total: parseFloat($table.find('.item:last').data('total')),
					saldo: parseFloat($table.find('.item:last').data('total'))
				});
			}else if(p.$w.find('[name=tabs]').tabs("option","active")==1){
				data = [];
				$table = p.$w.find('[id^=tabsConcPayment]').eq(1);
				servicio = $table.find('[name^=serv]').data('data');
				conceptos = $table.find('[name^=serv]').data('concs');
				cuotas = parseInt($table.find('[name=cuotas]').val());
				if(servicio==null){
					$table.find('[name^=btnServ]').click();
					K.notification({title: 'Servicio no seleccionado',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
					return false;
				}
				for(var k=0; k<cuotas; k++){
					conc = [];
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
								cod: conceptos[i].clasificador.cod,
								cuenta: {
									_id: conceptos[i].clasificador.cuenta._id.$id,
									cod: conceptos[i].clasificador.cuenta.cod,
									descr: conceptos[i].clasificador.cuenta.descr
								}
							};
						}else{
							tmp.concepto.cuenta = {
								_id: conceptos[i].cuenta._id.$id,
								descr: conceptos[i].cuenta.descr,
								cod: conceptos[i].cuenta.cod
							};
						}
						tmp.monto = parseFloat($table.find('.item').eq(i).data('monto'+k));
						tmp.saldo = tmp.monto;
						conc.push(tmp);
					}
					if($table.find('[name=fec]').eq(k).val()==''){
						$table.find('[name=fec]').eq(k).datepicker('show');
						K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha de vencimiento!',type: 'error'});
						return false;
					}
					data.push({
						servicio: {
							_id: servicio._id.$id,
							nomb: servicio.nomb,
							organizacion: {
								_id: servicio.organizacion._id.$id,
								nomb: servicio.organizacion.nomb
							}
						},
						fecven: $table.find('[name=fec]').eq(k).val(),
						moneda: 'S',
						conceptos: conc,
						total: parseFloat($table.find('.item').eq(conceptos.length).find('li[name='+k+']').eq(1).data('total')),
						saldo: parseFloat($table.find('.item').eq(conceptos.length).find('li[name='+k+']').eq(1).data('total'))
					});
				}
			}else{
				return 10;
			}
			return {cuenta_cobrar: data};
		};
		$.extend(p,{
			loadConc: function(){
				if(p.$w.find('[name=tabs]').tabs("option","active")!=2){
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
							if(espacio.precio_temp!=null) SERV.CM_PREC_TEMP = espacio.precio_temp;
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
					}else
						for(var i=0,j=variables.length; i<j; i++){
							try{
								if(variables[i].valor=='true') eval('var '+variables[i].cod+' = true;');
								else if(variables[i].valor=='false') eval('var '+variables[i].cod+' = false;');
								else eval('var '+variables[i].cod+' = '+variables[i].valor+';');
							}catch(e){
								console.warn('error en carga de variables');
							}
						}
					p.$w.find('[id^=tabsConcPayment] .gridBody').empty();
					if(p.$w.find('[name=tabs]').tabs("option","active")==0){
						$table = p.$w.find('[id^=tabsConcPayment]').eq(0);
						servicio = $table.find('[name^=serv]').data('data');
						conceptos = $table.find('[name^=serv]').data('concs');
						if(servicio==null){
							return K.notification({title: 'Servicio no seleccionado',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
						}
						SERV.FECVEN = 0;
						if($table.find('[name^=fecven]').length>0){
							if($table.find('[name^=fecven]').val()==''){
								$table.find('[name^=fecven]').focus();
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar una fecha de vencimiento!',
									type: 'error'
								});
							}
							SERV.FECVEN = ciHelper.date.diffDays(new Date(),$table.find('[name^=fecven]').datepicker('getDate'));
						}
						if(SERV.FECVEN<0) SERV.FECVEN = 0;
						console.log('FECVEN=>'+SERV.FECVEN);
						p.conceptos = conceptos;
						for(var i=0,j=conceptos.length; i<j; i++){
							var $row = $table.find('.gridReference').clone();
							var monto = eval(conceptos[i].formula);
							eval("var "+conceptos[i].cod+" = "+monto+";");
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
									eval('var '+cod+' = '+monto);
									for(var ii=0,jj=p.conceptos.length; ii<jj; ii++){
										var $row = $table.find('.gridBody .item').eq(ii),
										$cell = $row.find('li').eq(2),
										monto = eval($cell.data('formula'));
										if($cell.data('formula')!=null){
											$cell.html(ciHelper.formatMon(monto));
											$row.data('monto',monto);
										}
									}
									p.calcConc();
								}).data('form',formula).data('cod',conceptos[i].cod);
								$row.find('li:eq(1) .ui-button').css('height','14px');
							}else{
								eval('var '+conceptos[i].cod+' = '+monto+';');
								$row.find('li:eq(2)').data('formula',conceptos[i].formula);
							}
							$row.find('li:eq(2)').html(ciHelper.formatMon(monto));
							$row.wrapInner('<a class="item" name="'+conceptos[i]._id.$id+'" />');
							$row.find('.item').data('monto',monto);
							$table.find(".gridBody").append( $row.children() );
						}
					}else if(p.$w.find('[name=tabs]').tabs("option","active")==1){
						$table = p.$w.find('[id^=tabsConcPayment]').eq(1);
						servicio = $table.find('[name^=serv]').data('data');
						conceptos = $table.find('[name^=serv]').data('concs');
						cuotas = parseInt($table.find('[name=cuotas]').val());
						if(servicio==null){
							return K.notification({title: 'Servicio no seleccionado',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
						}
						if(SERV.FECVEN<0) SERV.FECVEN = 0;
						SERV.FECVEN = 0;
						$table.find('.gridBody').empty().width((380+(140*cuotas))+'px');
						$table.find('.gridReference:first').empty();
						$table.find('.gridHeader ul').empty().append('<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:380px;max-width:380px;">Concepto</li>');
						$table.find('.gridReference:last ul').empty().append('<li style="min-width:380px;max-width:380px;"></li>');
						var $row = $table.find('.gridReference:last').clone();
						for(var i=0,j=cuotas; i<j; i++){
							$table.find('.gridHeader ul').append('<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:140px;max-width:140px;">Cuota '+(i+1)+'</li>');
							$row.find('ul').append('<li name="'+i+'" style="min-width:70px;max-width:70px;"></li>');
							$row.find('ul').append('<li name="'+i+'" style="min-width:70px;max-width:70px;"></li>');
						}
						$table.find(".gridReference:first").append( $row.children() );
						for(var i=0,j=conceptos.length; i<j; i++){
							var $row = $table.find('.gridReference:first').clone();
							var monto = eval(conceptos[i].formula);
							$row.find('li:eq(0)').html(conceptos[i].nomb);
							$row.wrapInner('<a class="item" name="'+conceptos[i]._id.$id+'" />');
							for(var k=0; k<cuotas; k++){
								if(conceptos[i].formula.indexOf('__VALUE__')!=-1){
									var formula = conceptos[i].formula;
									formula = ciHelper.string.replaceAll(formula,"__VALUE__","__VALUE"+conceptos[i].cod+"__");
									$row.find('li[name='+k+']').eq(0).html('<input type="text" size="3" name="codform'+conceptos[i].cod+'">');
									$row.find('[name^=codform]:last').val(0).numeric().spinner({step: 0.1,min: 0,stop: function(){
										$(this).change();
									}}).change(function(){
										var val = parseFloat($(this).val()),
										formula = $(this).data('form'),
										cod = $(this).data('cod'),
										k = $(this).data('index'),
										$row = $(this).closest('.item');
										eval("var __VALUE"+cod+"__ = "+val+";");
										var monto = eval(formula);
										$row.find('li[name='+k+']').eq(1).html(ciHelper.formatMon(monto));
										$row.data('monto'+k,monto);
										p.calcConc();
									}).data('form',formula).data('cod',conceptos[i].cod).data('index',k);
									$row.find('li[name='+k+']:eq(0) .ui-button').css('height','14px');
								}
								$row.find('li[name='+k+']').eq(1).html(ciHelper.formatMon(monto));
								$row.find('.item').data('monto'+k,monto);
							}
							$table.find(".gridBody").append( $row.children() );
							$table.find('.gridHeader ul').append('<li class="ui-button ui-widget ui-state-default ui-button-text-only" style="min-width:40px;max-width:40px;">&nbsp;</li>');
						}
					}
				}
				p.calcConc();
			},
			calcConc: function(){
				K.clearNoti();
				var $table, servicio, conceptos, total = 0, cuotas=0;
				if(p.$w.find('[name=tabs]').tabs("option","active")==0){
					$table = p.$w.find('[id^=tabsConcPayment]').eq(0);
					servicio = $table.find('[name^=serv]').data('data');
					conceptos = $table.find('[name^=serv]').data('concs');
					if(servicio==null){
						return K.notification({title: 'Servicio no seleccionado',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
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
				}else if(p.$w.find('[name=tabs]').tabs("option","active")==1){
					$table = p.$w.find('[id^=tabsConcPayment]').eq(1);
					servicio = $table.find('[name^=serv]').data('data');
					conceptos = $table.find('[name^=serv]').data('concs');
					cuotas = parseInt($table.find('[name=cuotas]').val());
					if(servicio==null){
						return K.notification({title: 'Servicio no seleccionado',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
					}
					total = [];
					for(var k=0; k<cuotas; k++){
						total[k] = 0;
					}
					for(var i=0,j=conceptos.length; i<j; i++){
						var $row = $table.find('.item').eq(i);
						for(var k=0; k<cuotas; k++){
							total[k] = +total[k] + parseFloat($row.data('monto'+k));
						}
					}
					if(conceptos.length!=$table.find('.item').length){
						$table.find('.item:last').remove();
						$table.find('.item:last').remove();
					}
					var $row = $table.find('.gridReference:first').clone();
					$row.find('li:eq(0)').addClass('ui-button ui-widget ui-state-default');
					$row.find('li:eq(0)').html('Total');
					var total_total = 0;
					for(var i=0; i<cuotas; i++){
						$row.find('li[name='+i+']').eq(1).html(ciHelper.formatMon(total[i])).data('total',total[i]);
						total_total += total[i];
					}
					$row.wrapInner('<a class="item" />');
					$row.find('.item').data('total',total_total);
					$table.find(".gridBody").append( $row.children() );
					$table.find('.gridReference:last ul').empty().append('<li style="min-width:380px;max-width:380px;">Fecha de Vencimiento</li>');
					$row.find('li:eq(0)').addClass('ui-button ui-widget ui-state-default');
					var $row = $table.find('.gridReference:last').clone();
					for(var i=0,j=cuotas; i<j; i++){
						$row.find('ul').append('<li name="'+i+'" style="min-width:140px;max-width:140px;"><input type="text" name="fec" size="11"></li>');
						$row.find('[name=fec]:last').datepicker();
					}
					$row.wrapInner('<a class="item" />');
					$table.find(".gridBody").append( $row.children() );
				}
			}
		});
		p.$w.find('.payment').eq(1).bind('scroll',function(){
			p.$w.find('.payment').eq(0).scrollLeft(p.$w.find('.payment').eq(1).scrollLeft());
		});
		p.$w.find('.payment').eq(3).bind('scroll',function(){
			p.$w.find('.payment').eq(2).scrollLeft(p.$w.find('.payment').eq(3).scrollLeft());
		});
		p.$w.find('[name=tabs]').tabs({
			activate: function( event, ui ){
				p.$w.find('.ui-layout-center .payment .gridBody').empty();
				p.loadConc();
			}
		});
		p.$w.find('[name^=btnServ]').click(function(){
			var $row = $(this).closest('tr');
			if(p.getEspa!=null){
				if(p.getEspa()==null)
					return K.notification({
						title: ciHelper.titleMessages.infoReq,
						text: 'Debe seleccionar un espacio antes de seleccionar el servicio!',
						type: 'error'
					});
			}
			if(p.getAcce!=null){
				if(p.getAcce()==null)
					return K.notification({
						title: ciHelper.titleMessages.infoReq,
						text: 'Debe seleccionar un accesorio antes de seleccionar el servicio!',
						type: 'error'
					});
			}
			mgServ.windowSelect({callback: function(data){
				$row.find('[name^=serv]').html('').removeData('data');
				p.$w.find('[id^=tabsConcPayment] .gridBody').empty();
				$.post('cj/conc/get_serv','id='+data._id.$id,function(concs){
					if(concs.serv==null){
						return K.notification({title: 'Servicio inv&aacute;lido',text: 'El servicio seleccionado no tiene conceptos asociados!',type: 'error'});
					}
					p.$w.data('vars',concs.vars);
					$row.find('[name^=serv]').html(data.nomb).data('data',data).data('concs',concs.serv);
					p.loadConc();
				},'json');
			}});
		}).button({icons: {primary: 'ui-icon-search'}});
		p.$w.find('[name=fecven_pag]').val(ciHelper.dateFormatNowBDNotHour());
		p.$w.find('[name=fecven_pag]').datepicker().change(function(){
			p.loadConc();
		});
		p.$w.find('[name=fecven_pag]').closest('tr').hide();
		p.$w.find('[name=cuotas]').spinner({step: 1,min: 1,max: 12,stop: function(){ $(this).change(); }})
		.val(1).numeric().change(function(){
			p.loadConc();
		}).parent().find('.ui-button').css('height','14px');
	},
	windowNewConcesion: function(p){
		if(p==null) p = {};
		$.extend(p,{
			cbGestor: function(data){
				p.$w.find('[name=nomb]').data('data',data);
				if(data.tipo_enti=='P'){
					p.$w.find('tr:eq(1)').show();
					p.$w.find('[name=nomb]').html( data.nomb );
					p.$w.find('[name=apell]').html( data.appat + ' ' + data.apmat );
				}else{
					p.$w.find('tr:eq(1)').hide();
					p.$w.find('[name=nomb]').html( data.nomb );
				}
			},
			cbEspacio: function(data){
				p.$w.find('[name=rbtnTipo]').button('enable',true);
				var tipo = '';
				p.$w.find('[name=det]').html("");
				if(data.nicho!=null){
					tipo = "Nicho-"+cmOper.statesNicho[data.nicho.tipo].descr;
					p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>Nombre del Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.nomb+"</td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.num+"</td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Nicho:</label></td><td>"+data.nicho.num+"</td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>Piso:</label></td><td>"+data.nicho.piso+"</td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>Fila:</label></td><td>"+data.nicho.fila+"</td></tr>");
				}
				else if(data.tumba!=null){
					tipo = "Tumba";
					p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.tumba.denominacion+"</td></tr>");
				 }
				 else {
					tipo = "Mausoleo";
					p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>Tipo de Zona:</label></td><td>"+cmOper.statesZonaMaus[data.mausoleo.zona].descr+"</td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.mausoleo.denominacion+"</td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Lote:</label></td><td>"+data.mausoleo.lote+"</td></tr>");
					p.$w.find('input:radio[name=rbtnTipo]:eq(1)').attr('checked',true).click();
					p.$w.find('[name=rbtnTipo]').button('refresh');
					p.$w.find('[name=rbtnTipo]').button('disable',true);
				 }
				p.$w.find('[name=det]').append("</table>");
				p.$w.find('[name=tipo]').data('data',data);
				var tmp_serv = {};
				if(data.mausoleo!=null){
					tmp_serv = p.config.CONC_MAU;
				}else{
					var condicion = p.$w.find('[name=rbtnTipo]:checked').val();
					if(condicion=='T'){
						tmp_serv = p.config.CONC_NIC_TEMP;
					}else{
						tmp_serv = p.config.CONC_NIC_PER;
					}
				}
				p.$w.find('[name=section4] [name^=fecven]').val(ciHelper.dateFormatNowBDNotHour());
				p.$w.find('[name=section4] [name^=serv]').html('').removeData('data');
				p.$w.find('[name=section4] [id^=tabsConcPayment] .gridBody').empty();
				$.post('cj/conc/get_serv','id='+tmp_serv._id.$id,function(concs){
					if(concs.serv==null){
						return K.notification({title: 'Servicio inv&aacute;lido',text: 'El servicio seleccionado no tiene conceptos asociados!',type: 'error'});
					}
					p.$w.data('vars',concs.vars);
					p.$w.find('[name=section4] [name^=serv]').html(tmp_serv.nomb).data('data',tmp_serv).data('concs',concs.serv);
					p.$w.find('[name=section4] [name^=fecven]').change();
				},'json');
			}
		});
		if(p.entidad!=null)
			p.windId = 'windowNewConce'+p.entidad._id.$id;
		else
			p.windId = 'windowNewConce';
		new K.Modal({
			id: p.windId,
			title: 'Nueva Concesi&oacute;n',
			icon: 'ui-icon-bookmark',
			width: 675,
			height: 370,
			contentURL: 'cm/conc/new',
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {},
					propietario = p.$w.find('[name=nomb]').data('data');
					data.observ = p.$w.find('[name=observ]').val();
					if(p.$w.find('[name=nomb]').html() == ""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un propietario!",type:"error"});
					}
					data.propietario = {
						_id: propietario._id.$id,
						tipo_enti: propietario.tipo_enti,
						nomb: propietario.nomb
					};
					if(propietario.appat !=null){
						data.propietario.appat = propietario.appat;
						data.propietario.apmat = propietario.apmat;
					}
					if(p.$w.find('[name=tipo]').html() == null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un espacio!",type:"error"});
					}
					var espacio = p.$w.find('[name=tipo]').data('data');
					data.espacio = {
						_id: espacio._id.$id,
						nomb: espacio.nomb
					};
					data.concesion = {
						condicion: p.$w.find('[name=rbtnTipo]:checked').val()
					};
					if(data.concesion.condicion == "T"){
						if(p.$w.find('[name=anios]').val() == ""){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese los a&ntilde;os!",type:"error"});
						}
						data.concesion.fecven = p.$w.find('[name=fecven]').val();
					}
					var extra = p.payment.getPay();
					if(extra!=false&&extra!=10) $.extend(data,extra);
					else if(extra==false) return false;
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/oper/save_conc",data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						//$('#pageWrapperLeft .ui-state-highlight').click();
						cmCuen.init();
						K.notification({title: 'Operaci&oacute;n Guardada',text: "La Operaci&oacute;n se guard&oacute; exitosamente!"});
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#'+p.windId);
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
				});
				p.$w.find('[name=fecven]').replaceWith('<input type="text" name="fecven" />');
				p.$w.find('[name=fecven]').datepicker();
				p.$w.find('[name=anios]').val(10).numeric().spinner({step: 1,min: 1,max: 1000,stop: function(){ $(this).change(); }});
				p.$w.find('[name=anios]').parent().find('.ui-button').css('height','14px');
				p.$w.find('[name=btnBusPro]').click(function(){
					//ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbGestor});
					mgEnti.windowSelect({callback: p.cbGestor});
				}).button({icons: {primary: 'ui-icon-search'}});
				/*p.$w.find('[name=btnNewPro]').click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbGestor});
				}).button({icons: {primary: 'ui-icon-plusthick'}});*/
				p.$w.find('[name=btnNewPro]').remove();
				p.$w.find('[name=btnSelEspacio]').click(function(){
					cmEspa.windowSelect({
						$parent: p.$w,
						callback: p.cbEspacio,
						filter: [{nomb: 'estado',value: 'D'}]
					});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('#rbtnCond1').click(function(){
					p.$w.find('[name=rowTemporal]').show();
					if(p.$w.find('[name=tipo]').data('data')!=null){
						var tmp_serv = {},
						tmp_espa = p.$w.find('[name=tipo]').data('data');
						if(tmp_espa.mausoleo!=null){
							tmp_serv = p.config.CONC_MAU;
						}else{
							var condicion = p.$w.find('[name=rbtnTipo]:checked').val();
							if(condicion=='T'){
								tmp_serv = p.config.CONC_NIC_TEMP;
							}else{
								tmp_serv = p.config.CONC_NIC_PER;
							}
						}
						p.$w.find('[name=section4] [name^=fecven]').val(ciHelper.dateFormatNowBDNotHour());
						p.$w.find('[name=section4] [name^=serv]').html('').removeData('data');
						p.$w.find('[name=section4] [id^=tabsConcPayment] .gridBody').empty();
						$.post('cj/conc/get_serv','id='+tmp_serv._id.$id,function(concs){
							if(concs.serv==null){
								return K.notification({title: 'Servicio inv&aacute;lido',text: 'El servicio seleccionado no tiene conceptos asociados!',type: 'error'});
							}
							p.$w.data('vars',concs.vars);
							p.$w.find('[name=section4] [name^=serv]').html(tmp_serv.nomb).data('data',tmp_serv).data('concs',concs.serv);
							p.$w.find('[name=section4] [name^=fecven]').change();
						},'json');
					}
				});
				p.$w.find('#rbtnCond2').click(function(){
					p.$w.find('[name=rowTemporal]').hide();
					if(p.$w.find('[name=tipo]').data('data')!=null){
						var tmp_serv = {},
						tmp_espa = p.$w.find('[name=tipo]').data('data');
						if(tmp_espa.mausoleo!=null){
							tmp_serv = p.config.CONC_MAU;
						}else{
							var condicion = p.$w.find('[name=rbtnTipo]:checked').val();
							if(condicion=='T'){
								tmp_serv = p.config.CONC_NIC_TEMP;
							}else{
								tmp_serv = p.config.CONC_NIC_PER;
							}
						}
						p.$w.find('[name=section4] [name^=fecven]').val(ciHelper.dateFormatNowBDNotHour());
						p.$w.find('[name=section4] [name^=serv]').html('').removeData('data');
						p.$w.find('[name=section4] [id^=tabsConcPayment] .gridBody').empty();
						$.post('cj/conc/get_serv','id='+tmp_serv._id.$id,function(concs){
							if(concs.serv==null){
								return K.notification({title: 'Servicio inv&aacute;lido',text: 'El servicio seleccionado no tiene conceptos asociados!',type: 'error'});
							}
							p.$w.data('vars',concs.vars);
							p.$w.find('[name=section4] [name^=serv]').html(tmp_serv.nomb).data('data',tmp_serv).data('concs',concs.serv);
							p.$w.find('[name=section4] [name^=fecven]').change();
						},'json');
					}
				}).click();
				p.$w.find('#rbtnTipo').buttonset();
				if(p.entidad!=null){
					p.$w.find('td:first').hide();
					p.cbGestor(p.entidad);
				}
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
				p.$w.find('[name=anios]').change(function(){
					var date = ciHelper.dateSumNow(parseInt(p.$w.find('[name=anios]').val()));
					//p.$w.find('[name=fecven]').data('date',K.dateTimeFormat(date));
					p.$w.find('[name=fecven]').val(moment(new Date()).add('years',parseInt(p.$w.find('[name=anios]').val())).format('YYYY-MM-DD'));
				}).change();
				p.payment = new cmOper.payment({
					$w: p.$w,
					getEspa: function(){
						return p.$w.find('[name=tipo]').data('data');
					}
				});
				$.post('cj/cuen/get_config_ceme',function(data){
					p.config = data;
					if(p.espacio!=null){
						p.cbEspacio(p.espacio);
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowNewInhumacion: function(p){
		if(p==null) p = {};
		p.cbFuneraria = function(data){
			p.$w.find('[name=funeraria]').data('data',data);
			p.$w.find('[name=funeraria]').html( data.nomb );
		};
		p.cbMunicipalidad = function(data){
			p.$w.find('[name=muni]').data('data',data);
			p.$w.find('[name=muni]').html( data.nomb );
		};
		p.cbGestor = function(data){
			p.$w.find('[name=nomb]').data('data',data);
			if(data.tipo_enti=='P'){
				p.$w.find('tr:eq(1)').show();
				p.$w.find('[name=nomb]').html( data.nomb );
				p.$w.find('[name=apell]').html( data.appat + ' ' + data.apmat );
			}else{
				p.$w.find('tr:eq(1)').hide();
				p.$w.find('[name=nomb]').html( data.nomb );
			}
			$.post('cm/ocup/all_ocu_pro','_id='+data._id.$id,function(difuntos){
				var $select = p.$w.find('[name=difunto]');
				$select.empty().append('<option value="">--</option>');
				if(difuntos!=null){
					for(var i=0; i<difuntos.length; i++){
						var ocupante = difuntos[i].appat + ' '+difuntos[i].apmat+', '+difuntos[i].nomb;
						$select.append('<option data-espacio="'+difuntos[i].roles.ocupante.espacio._id.$id+'" value="'+difuntos[i]._id.$id+'-'+difuntos[i].nomb+'-'+difuntos[i].appat+'-'+difuntos[i].apmat+'">'+ocupante+'</option>');
					}
				}
			},'json');
		};
		p.cbEspacio = function(data){
			var tipo = '';
			var espacio = p.$w.find('[name=espacio]');
			espacio.html("");
			if(data!=null){
				if(data.nicho!=null){
					tipo = "Nicho-"+cmOper.statesNicho[data.nicho.tipo].descr;
					espacio.append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
					espacio.append("<tr><td><label>Nombre del Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.nomb+"</td></tr>");
					espacio.append("<tr><td><label>N&uacute;mero de Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.num+"</td></tr>");
					espacio.append("<tr><td><label>N&uacute;mero de Nicho:</label></td><td>"+data.nicho.num+"</td></tr>");
					espacio.append("<tr><td><label>Piso:</label></td><td>"+data.nicho.piso+"</td></tr>");
					espacio.append("<tr><td><label>Fila:</label></td><td>"+data.nicho.fila+"</td></tr>");
				}
				else if(data.tumba!=null){
						tipo = "Tumba";
						espacio.append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
						espacio.append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.tumba.denominacion+"</td></tr>");
					 }
					 else {
						tipo = "Mausoleo";
						espacio.append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
						espacio.append("<tr><td><label>Tipo de Zona:</label></td><td>"+cmOper.statesZonaMaus[data.mausoleo.zona].descr+"</td></tr>");
						espacio.append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.mausoleo.denominacion+"</td></tr>");
						espacio.append("<tr><td><label>N&uacute;mero de Lote:</label></td><td>"+data.mausoleo.lote+"</td></tr>");
					 }
				p.$w.find('[name=tipo]').data('data',data);
				var tmp_serv = {};
				if(data.mausoleo!=null){
					tmp_serv = p.config.INH_MAU;
				}else{
					tmp_serv = p.config.INH_NIC;
				}
				p.$w.find('[name=section5] [name^=fecven]').val(ciHelper.dateFormatNowBDNotHour());
				p.$w.find('[name=section5] [name^=serv]').html('').removeData('data');
				p.$w.find('[name=section5] [id^=tabsConcPayment] .gridBody').empty();
				$.post('cj/conc/get_serv','id='+tmp_serv._id.$id,function(concs){
					if(concs.serv==null){
						return K.notification({title: 'Servicio inv&aacute;lido',text: 'El servicio seleccionado no tiene conceptos asociados!',type: 'error'});
					}
					p.$w.data('vars',concs.vars);
					p.$w.find('[name=section5] [name^=serv]').html(tmp_serv.nomb).data('data',tmp_serv).data('concs',concs.serv);
					p.$w.find('[name=section5] [name^=fecven]').change();
				},'json');
			}
		};
		if(p.entidad!=null){
			p.windId = 'windowNewInhumacion'+p.entidad._id.$id;
		}
		else
			p.windId = 'windowNewInhumacion';
		new K.Modal({
			id: p.windId,
			title: 'Nueva Inhumaci&oacute;n',
			icon: 'ui-icon-person',
			width: 700,
			height: 350,
			contentURL: 'cm/oper/new_inhu',
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var datos = {},
					data = {};
					if(p.$w.find('[name=nomb]').html() == ""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un propietario!",type:"error"});
					}
					var propietario = p.$w.find('[name=nomb]').data('data');
					data.propietario = ciHelper.enti.dbRel(propietario);
					if(p.$w.find('[name=difunto] option:selected').html() == "--"){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un ocupante!",type:"error"});
					}
					var ocu = p.$w.find('[name=difunto] option:selected').val().split("-");
					data.ocupante = {};
					data.ocupante._id = ocu[0];
					data.ocupante.tipo_enti = 'P';
					data.ocupante.nomb = ocu[1];
					data.ocupante.appat = ocu[2];
					data.ocupante.apmat = ocu[3];
					var espacio = p.$w.find('[name=tipo]').data('data');
					data.espacio = {
						_id: espacio._id.$id,
						nomb: espacio.nomb
					};
					if(p.$w.find('[name=fecdef]').val() == ""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese fecha de defunci&oacute;n!",type:"error"});
					}
					data.inhumacion = new Object;
					data.inhumacion.fecdef = p.$w.find('[name=fecdef]').val();
					data.inhumacion.partdef = p.$w.find('[name=partdef]').val();
					/*if(data.inhumacion.partdef == ""){
						p.$w.find('[name=partdef]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese parte de defunci&oacute;n!",type:"error"});
					}*/
					data.inhumacion.edad = p.$w.find('[name=edad]').val();
					data.inhumacion.causa = p.$w.find('[name=causa]').val();
					data.inhumacion.folio = p.$w.find('[name=folio]').val();
					data.inhumacion.puerta = p.$w.find('[name=puerta] option:selected').val();
					if(p.$w.find('[name=muni]').html()!=""){
						var municipalidad = p.$w.find('[name=muni]').data('data');
						data.inhumacion.municipalidad = new Object;
						data.inhumacion.municipalidad._id = municipalidad._id.$id;
						data.inhumacion.municipalidad.tipo_enti = municipalidad.tipo_enti;
						data.inhumacion.municipalidad.nomb = municipalidad.nomb;
					}
					if(p.$w.find('[name=funeraria]').html()!=""){
						var funeraria = p.$w.find('[name=funeraria]').data('data');
						data.inhumacion.funeraria = new Object;
						data.inhumacion.funeraria._id = funeraria._id.$id;
						data.inhumacion.funeraria.tipo_enti = funeraria.tipo_enti;
						data.inhumacion.funeraria.nomb = funeraria.nomb;
					}
					if(p.$w.find('[name=rbtnAuto]:checked').val()=='0'){
						if(p.$w.find('[name=fecprog]').val() == ""){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese fecha programada!",type:"error"});
						}
						data.programacion = {
							fecprog: p.$w.find('[name=fecprog]').val(),
							observ: p.$w.find('[name=observ]').val()
						};
					}else{
						data.fec_auto = p.$w.find('[name=fec_auto]').val();
						data.observ_auto = p.$w.find('[name=observ_auto]').val();
					}
					datos.fecnac = p.$w.find('[name=fecnac]').val();
					if(p.$w.find('[name=rbtnAuto]:checked').val()=='0'){
						var extra = p.payment.getPay();
						if(extra!=false&&extra!=10) $.extend(data,extra);
						else if(extra==false) return false;
					}
					datos.data = data;
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/oper/save_inhu",datos,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						cmCuen.init();
						K.notification({title: 'Operaci&oacute;n Guardada',text: "La operaci&oacute;n se guard&oacute; exitosamente!"});
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#'+p.windId);
				p.$w.find('[name=btnBusPro]').click(function(){
					/*ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbGestor,filter: [
					    //{nomb: 'tipo_enti',value: 'P'},
					    {nomb: 'roles.propietario',value: {$exists: true}}
					]});*/
					mgEnti.windowSelect({callback: p.cbGestor,filter: [
					    {nomb: 'roles.propietario',value: {$exists: true}}
					]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnNewPro]').remove();/*.click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbGestor});
				}).button({icons: {primary: 'ui-icon-plusthick'}}).remove();*/
				p.$w.find('[name=btnBusFun]').click(function(){
					//ciSearch.windowSearchEmpresa({$window: p.$w,callback: p.cbFuneraria});
					mgEnti.windowSelect({callback: p.cbFuneraria,filter: [{nomb: 'tipo_enti',value: 'E'}]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnNewFun]').remove();/*.click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbFuneraria});
				}).button({icons: {primary: 'ui-icon-plusthick'}});*/
				p.$w.find('[name=btnBusMuni]').click(function(){
					//ciSearch.windowSearchEmpresa({$window: p.$w,callback: p.cbMunicipalidad});
					mgEnti.windowSelect({callback: p.cbMunicipalidad,filter: [{nomb: 'tipo_enti',value: 'E'}]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnNewMuni]').remove();/*.click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbMunicipalidad});
				}).button({icons: {primary: 'ui-icon-plusthick'}});*/
				p.$w.find('[name=fecdef]').datetimepicker({
					minuteGrid: 10
				});
				p.$w.find('[name=fecprog]').datetimepicker({
					minuteGrid: 10
				});
				p.$w.find('[name=fecnac]').datetimepicker({
					minuteGrid: 10
				});
				if(p.entidad!=null){
					p.$w.find('td:first').hide();
					p.cbGestor(p.entidad);
				}
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
				p.$w.find('[name=difunto]').change(function () {
					var selected = $(this).find('option:selected');
				    var espa = {
				    	_id: selected.data('espacio')
				    };
                  if(espa._id=='Infinity') espa._id = selected.attr('data-espacio');
					$.post('cm/espa/get_one',espa,function(espacio){
						p.cbEspacio(espacio);
					},'json');
				});
				p.payment = new cmOper.payment({
					$w: p.$w,
					getEspa: function(){
						return p.$w.find('[name=tipo]').data('data');
					}
				});
				p.$w.find('input[name^=rbtnAuto]').click(function(){
					if($(this).val()=='0'){
						p.$w.find('[name=section4] table').show();
						p.$w.find('[name=section4] div:last').hide();
						p.$w.find('[name=section5]').show();
					}else{
						p.$w.find('[name=section4] table').hide();
						p.$w.find('[name=section4] div:last').show();
						p.$w.find('[name=section5]').hide();
					}
				});
				p.$w.find('[id=rbtnAuto0]').click();
				p.$w.find('[name=fec_auto]').datepicker();
				p.$w.find("#rbtnAuto").buttonset();
				$.post('cj/cuen/get_config_ceme',function(data){
					p.config = data;
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowNewAsignacion: function(p){
		if(p==null) p = {};
		p.cbProp = function(data){
			p.entidad = data;
			p.$w.find('[name=nomb]').data('data',data);
			if(data.tipo_enti=='P'){
				p.$w.find('tr:eq(1)').show();
				p.$w.find('[name=nomb]').html( data.nomb );
				p.$w.find('[name=apell]').html( data.appat + ' ' + data.apmat );
			}else{
				p.$w.find('tr:eq(1)').hide();
				p.$w.find('[name=nomb]').html( data.nomb );
			}
			p.cbEspacios();
		};
		p.cbOcup = function(data){
			var $row = p.$w.find('[name=section3] .gridReference').clone();
			$li = $('li',$row);
			$li.eq(0).html( '<button name="btnEliminar">Eliminar</button>' );
			$li.eq(1).html( p.$w.find("[name=section3] .gridBody:last a").length+1 );
			$li.eq(2).html( ciHelper.enti.formatName(data) );
			$li.eq(3).html( '<input type="text" name="progra" size="16">' );
			$row.wrapInner('<a class="item"  href="javascript: void(0);" />');
			$row.find('a').data('data',data);
			$row.find('[name=progra]').datetimepicker().hide().datepicker( "option", "buttonImage", "" );
			if(p.espacio.nicho!=null){
				if(p.espacio.nicho.tipo=='P' && p.$w.find("[name=section3] .gridBody:last a").length==1){
					p.$w.find('[name=btnAgregar],[name=btnBuscar]').button('disable');
					K.notification({title: 'L&iacute;mite de ocupantes alcanzado!',text: 'Solamente se puede asignar una persona en un nicho de tipo p&aacute;rvulo!',type: 'error'});
				}else if(p.espacio.nicho.tipo=='N' && p.$w.find("[name=section3] .gridBody:last a").length==3){
					p.$w.find('[name=btnAgregar],[name=btnBuscar]').button('disable');
					K.notification({title: 'L&iacute;mite de ocupantes alcanzado!',text: 'Solamente se puede asignar tres personas en un nicho normal!',type: 'error'});
				}
			}else if(p.$w.find("[name=section3] .gridBody:last a").length==1 && p.espacio.tumba!=null){
				p.$w.find('[name=btnAgregar],[name=btnBuscar]').button('disable');
				K.notification({title: 'L&iacute;mite de ocupantes alcanzado!',text: 'Solamente se puede asignar una persona en una tumba!',type: 'error'});
			}
			if(data.old!=null){
				$row.find('[name=btnEliminar]').remove();
			}else if(data.hist!=null){
				$row.find('a').attr('data-hist',1);
				$row.find('[name=btnEliminar]').click(function(){
					$(this).closest('.item').remove();
					p.$w.find('[name=btnAgregar],[name=btnBuscar]').button('enable');
				}).button({icons:{primary: 'ui-icon-closethick'},text: false});
			}else{
				$row.find('[name=btnEliminar]').click(function(){
					$(this).closest('.item').remove();
				}).button({icons:{primary: 'ui-icon-closethick'},text: false});
					
				if(p.espacio.nicho!=null){
					$row.find('[name=btnEliminar]').click(function(){
						$(this).closest('.item').remove();
						p.$w.find('[name=btnAgregar],[name=btnBuscar]').button('enable');
					});
					p.$w.find('[name=btnAgregar],[name=btnBuscar]').button('disable');
					K.notification({title: 'M&aacute;ximo de asignaciones alcanzado!',text: 'Solamente se puede agregar una persona a la vez en un nicho!'});
					if(p.$w.find("[name=section3] .gridBody:last a").length>0){
						p.$w.find("[name=section3] .gridBody:last a:last").find('[name=progra]').show().datepicker( "option", "buttonImage", "images/calendar.gif" );
						K.notification({title: 'Reducci&oacute;n pendiente!',text: 'Al existir ya un ocupante del nicho se debe realizar una reducci&oacute;n a este &uacute;ltimo!'});
					}
				}else if(p.espacio.tumba!=null){
					$row.find('[name=btnEliminar]').click(function(){
						$(this).closest('.item').remove();
						p.$w.find('[name=btnAgregar],[name=btnBuscar]').button('enable');
					});
					p.$w.find('[name=btnAgregar],[name=btnBuscar]').button('disable');
					K.notification({title: 'M&aacute;ximo de asignaciones alcanzado!',text: 'Solamente se puede asignar una persona a una tumba!'});
				}
			}
			p.$w.find("[name=section3] .gridBody").append( $row.children() );
		};
		p.cbEspacios = function(){
			p.$w.find('[name=cboSelEspacio]').removeData('espacios');
			p.$w.find('[name=cboSelEspacio]').empty();
			p.$w.find(".gridBody:last").empty();
			p.$w.find("[name=section3] .gridBody").empty();
			p.$w.find('[name=det]').html("");
			p.$w.find('[name=btnAgregar],[name=btnBuscar]').button('disable');
			p.espacio = null;
			$.post('cm/espa/espa_prop','_id='+p.entidad._id.$id,function(data){
				if(data!=null){
					$select = p.$w.find('[name=cboSelEspacio]').data('espacios',data);
					for(var i=0; i<data.length; i++){
						$select.append('<option value="'+data[i]._id.$id+'" _index="'+i+'">'+data[i].nomb+'</option>');
					}
					$select.unbind().bind('change',function(){
						var data = p.$w.find('[name=cboSelEspacio]').data('espacios');
						p.cbEspacio(data[$(this).find('option:selected').attr('_index')]);
					});//.change();
					if(p.espacio_ori!=null){
						$select.selectVal(p.espacio_ori._id.$id);
						$select.attr('disabled','disabled');
						$select.change();
					}else
						$select.change();
				}else{
					K.notification({title: ciHelper.titleMessages.infoReq,text: 'La entidad seleccionada no tiene ning&uacute;n espacio asignado!'});
				}
			},'json');
		};
		p.cbEspacio = function(data){
			p.$w.find(".gridBody:last").empty();
			p.$w.find("[name=section3] .gridBody").empty();
			p.$w.find('[name=btnAgregar],[name=btnBuscar]').button('enable');
			p.espacio = data;
			var tipo = '';
			p.$w.find('[name=det]').html("");
			p.$w.find('[name=det]').append("<table>");
			if(data.nicho!=null){
				tipo = "Nicho - "+cmOper.statesNicho[data.nicho.tipo].descr;
				p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Nombre del Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.nomb+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.num+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Nicho:</label></td><td>"+data.nicho.num+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Piso:</label></td><td>"+data.nicho.piso+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Fila:</label></td><td>"+data.nicho.fila+"</td></tr>");
			}else if(data.tumba!=null){
				tipo = "Tumba";
				p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Referencia:</label></td><td>"+data.nomb+"</td></tr>");
			}else{
				tipo = "Mausoleo";
				p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Tipo de Zona:</label></td><td>"+cmOper.statesZonaMaus[data.mausoleo.zona].descr+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.mausoleo.denominacion+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Lote:</label></td><td>"+data.mausoleo.lote+"</td></tr>");
			}
			p.$w.find('[name=det]').append("</table>");
			p.$w.find('[name=tipo]').data('data',data);
			p.payment.loadData();
			if(data.ocupantes!=null){
				for(var i=0; i<data.ocupantes.length; i++){
					data.ocupantes[i].old = true;
					if((data.ocupantes.length-1)>i) data.ocupantes[i].nold = true;
					p.cbOcup(data.ocupantes[i]);
				}
				if(p.espacio.nicho!=null){
					if(p.espacio.nicho.tipo=='P' && p.$w.find(".gridBody:last a").length==1){
						p.$w.find('[name=btnAgregar],[name=btnBuscar]').button('disable');
						K.notification({title: 'L&iacute;mite de ocupantes alcanzado!',text: 'Solamente se puede asignar una persona en un nicho de tipo p&aacute;rvulo!',type: 'error'});
					}else if(p.espacio.nicho.tipo=='N' && p.$w.find(".gridBody:last a").length==3){
						p.$w.find('[name=btnAgregar],[name=btnBuscar]').button('disable');
						K.notification({title: 'L&iacute;mite de ocupantes alcanzado!',text: 'Solamente se puede asignar tres personas en un nicho normal!',type: 'error'});
					}
				}else if(p.$w.find(".gridBody:last a").length==1 && p.espacio.tumba!=null){
					p.$w.find('[name=btnAgregar],[name=btnBuscar]').button('disable');
					K.notification({title: 'L&iacute;mite de ocupantes alcanzado!',text: 'Solamente se puede asignar una persona a una tumba!',type: 'error'});
				}
			}
		};
		if(p.entidad!=null)
			p.windId = 'windowNewAsig'+p.entidad._id.$id;
		else
			p.windId = 'windowNewAsig';
		new K.Modal({
			id: p.windId,
			title: 'Nueva Asignaci&oacute;n',
			icon: 'ui-icon-tag',
			width: 680,
			height: 370,
			contentURL: 'cm/oper/new_asig',
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {};
					data.observ = p.$w.find('[name=observ]').val();
					if(p.$w.find('[name=nomb]').html() == ""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un propietario!",type:"error"});
					}
					var propietario = p.$w.find('[name=nomb]').data('data');
					data.propietario = ciHelper.enti.dbRel(propietario);
					if(p.$w.find('[name=cboSelEspacio] option').length == 0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un propietario con espacios asignados!",type:"error"});
					}
					var espacio = p.$w.find('[name=tipo]').data('data');
					data.espacio = new Object;
					data.espacio._id = espacio._id.$id;
					data.espacio.nomb = espacio.nomb;
					data.asignacion = true;
					if(p.$w.find('[name=btnEliminar]').length>1){
						data.ocupante = [];
						data.ocupante_old = [];
						for(var i=0; i<p.$w.find('[name=btnEliminar]').length; i++){
							var $row = p.$w.find('[name=btnEliminar]').eq(i).closest('.item');
							var tmp = $row.data('data');
							var ocupante = ciHelper.enti.dbRel(tmp);
							if($row.attr('data-hist')==undefined)
								data.ocupante.push( ocupante );
							else
								data.ocupante_old.push( ocupante );
						}
					}else if(p.$w.find('[name=btnEliminar]').length==1){
						var $row = p.$w.find('[name=btnEliminar]:last').closest('.item');
						var ocupante = ciHelper.enti.dbRel($row.data('data'));
						data.ocupante = ocupante;
					}else{
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar al menos un ocupante!',type: 'error'});
					}
					if(p.$w.find('[name=progra]').length>1){
						for(var i=0; i<p.$w.find('[name=progra]').length; i++){
							var $progra = p.$w.find('[name=progra]').eq(i);
							if($progra.css('display')!='none'){
								if($progra.val()==''){
									$progra.focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe designar la fecha de reducci&oacute;n!',type: 'error'});
								}else{
									var tmp = new Object;
									tmp.programacion = new Object;
									tmp.programacion.fecprog = $progra.val()+':00';
									tmp.ocupante = ciHelper.enti.dbRel($progra.closest('.item').data('data'));
									data.asignacion = tmp;
								}
							}
						}
					}
					var extra = p.payment.getPay();
					if(extra!=false&&extra!=10) $.extend(data,extra);
					else if(extra==false) return false;
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/oper/save_asig",data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						cmCuen.init();
						K.notification({title: ciHelper.titleMessages.regiGua,text: "La operaci&oacute;n se guard&oacute; exit&oacute;samente!"});
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#'+p.windId);
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
				});
				p.$mainPanel = p.$w.find('.ui-layout-center');
				p.$leftPanel = p.$w.find('.ui-layout-west');
				p.$w.find('.grid:eq(1)').css('overflow','hidden');
				p.$w.find('.grid:eq(2)').scroll(function(){
					p.$w.find('.grid:eq(1)').scrollLeft($(this).scrollLeft());
				});
				p.$w.find('[name=btnAgregar]').remove();/*.click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbOcup});
				}).button({icons: {primary: 'ui-icon-plusthick'},text: false});*/
				p.$w.find('[name=btnBuscar]').click(function(){
					/*ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbOcup,filter: [
					    {nomb: 'tipo_enti',value: 'P'},
					    {nomb: 'roles.ocupante',value: {$exists: false}}
					]});*/
					mgEnti.windowSelect({callback: p.cbOcup,filter: [
					    {nomb: 'tipo_enti',value: 'P'},
					    //{nomb: 'roles.ocupante',value: {$exists: false}}
					]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnAgregar],[name=btnBuscar]').button('disable');
				if(p.entidad!=null){
					p.cbProp(p.entidad);
					//p.cbEspacios();
					p.$w.find('td:first').remove();
				}else{
					p.$w.find('[name=btnBusPro]').click(function(){
						/*ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbProp,filter: [
 						    //{nomb: 'tipo_enti',value: 'P'},
						    {nomb: 'roles.propietario',value: {$exists: true}}
						]});*/
						mgEnti.windowSelect({callback: p.cbProp,filter: [
 						    //{nomb: 'tipo_enti',value: 'P'},
						    {nomb: 'roles.propietario',value: {$exists: true}}
						]});
					}).button({icons: {primary: 'ui-icon-search'}});
					p.$w.find('[name=btnNewPro]').remove();
				}
				p.$w.find('[name=btnOldOcup]').click(function(){
					mgEnti.windowSelect({callback: function(data){
						data.hist = true;
						p.cbOcup(data);
					}});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$mainPanel.css('overflow-x','hidden');
				p.$leftPanel.find('a').bind('click',function(event){
					event.preventDefault();
					p.$mainPanel.scrollTo( p.$mainPanel.find('[name='+$(this).attr('name')+']'), 800 );
				}).eq(0).click();
				p.$leftPanel.find('a:first').click().find('ul').addClass('ui-state-highlight');
				p.$w.layout({
					resizeWithWindow:	false,
					west__size:			150,
					west__closable:		false,
					west__resizable:	false,
					west__slidable:		false
				});
				p.payment = new cmOper.payment({
					$w: p.$w,
					getEspa: function(){
						return p.$w.find('[name=tipo]').data('data');
					}
				});
				$.post('cj/cuen/get_config_ceme',function(data){
					p.config = data;
					var tmp_serv = p.config.ASIG;
					p.$w.find('[name=section4] [name^=fecven]').val(ciHelper.dateFormatNowBDNotHour());
					p.$w.find('[name=section4] [name^=serv]').html('').removeData('data');
					p.$w.find('[name=section4] [id^=tabsConcPayment] .gridBody').empty();
					$.post('cj/conc/get_serv','id='+tmp_serv._id.$id,function(concs){
						if(concs.serv==null){
							return K.notification({title: 'Servicio inv&aacute;lido',text: 'El servicio seleccionado no tiene conceptos asociados!',type: 'error'});
						}
						p.$w.data('vars',concs.vars);
						p.$w.find('[name=section4] [name^=serv]').html(tmp_serv.nomb).data('data',tmp_serv).data('concs',concs.serv);
						p.$w.find('[name=section4] [name^=fecven]').change();
					},'json');
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowNewConstruccion: function(p){
		if(p==null) p = new Object;
		p.cbProp = function(data){
			p.$w.find('[name=btnBusPro]').button('option','text',false);
			p.$w.find('[name=nomb]').data('data',data);
			if(data.tipo_enti=='P'){
				p.$w.find('tr:eq(1)').show();
				p.$w.find('[name=nomb]').html( data.nomb );
				p.$w.find('[name=apell]').html( data.appat + ' ' + data.apmat );
			}else{
				p.$w.find('tr:eq(1)').hide();
				p.$w.find('[name=nomb]').html( data.nomb );
			}
			$.post('cm/espa/all_mausoleos',data,function(mausoleos){
				var $select = p.$w.find('[name=cboSelEspacio]');
				$select.empty().append('<option value="">--</option>');
				if(mausoleos!=null){
					for(var i=0; i<mausoleos.length; i++){
						$select.append('<option data-capacidad="'+mausoleos[i].capacidad+'" data-largo="'+mausoleos[i].mausoleo.medidas.largo+'" data-ancho="'+mausoleos[i].mausoleo.medidas.ancho+'" value="'+mausoleos[i]._id.$id+'">'+mausoleos[i].nomb+'</option>');
					}
				}
				if(p.espacio!=null){
					$select.selectVal(p.espacio._id.$id);
					$select.attr('disabled','disabled');
				}
			},'json');
		};
		p.validarMedidas = function(){
			var fl = true;
			if(p.$w.find('[name=txtCantidad]').val()>p.espacio.capacidad){
				p.$w.find('[name=txtCantidad]').focus();
				K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar la capacidad menor a '+p.espacio.capacidad,type: 'error'});
				fl = false;
			}
			if(p.$w.find('[name=txtLargo]').val()>p.espacio.ancho){
				p.$w.find('[name=txtLargo]').focus();
				K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el largo menor a '+p.espacio.ancho,type: 'error'});
				fl = false;
			}
			if(p.$w.find('[name=txtAncho]').val()>p.espacio.largo){
				p.$w.find('[name=txtAncho]').focus();
				K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el ancho menor a '+p.espacio.largo,type: 'error'});
				fl = false;
			}
			return fl;
		};
		p.selEspacio = function(){
			var selected = p.$w.find('[name=cboSelEspacio] option:selected');
		    p.espacio = new Object;
		    p.espacio._id = selected.val();
		    p.espacio.nomb = selected.html();
		    p.espacio.capacidad = selected.data('capacidad');
		    p.espacio.largo = selected.data('largo');
		    p.espacio.ancho = selected.data('ancho');
			p.payment.loadData();
		};
		if(p.entidad!=null){
			p.windId = 'windowNewConstruccion'+p.entidad._id.$id;
		}
		else
			p.windId = 'windowNewConstruccion';
		new K.Modal({
			id: p.windId,
			title: 'Nueva Construcci&oacute;n',
			icon: 'ui-icon-home',
			width: 700,
			height: 410,
			contentURL: 'cm/oper/new_cons',
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {};
					if(p.$w.find('[name=nomb]').data('data')==null){
						p.$w.find('[name=btnBusPro]').click();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: "Seleccione un propietario!",
							type:"error"
						});
					}
					data.propietario = ciHelper.enti.dbRel(p.$w.find('[name=nomb]').data('data'));
					if(p.$w.find('[name=cboSelEspacio] option:selected').html() == "--"){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: "Seleccione un mausoleo!",
							type:"error"
						});
					}
					data.nomb = p.espacio.nomb;
					data.espacio = {
						_id: p.espacio._id,
						nomb: p.espacio.nomb
					};
					data.construccion = {
						decreto: p.$w.find('[name=decreto]').val(),
						capacidad: p.$w.find('[name=txtCantidad]').val(),
						ancho: p.$w.find('[name=txtAncho]').val(),
						largo: p.$w.find('[name=txtLargo]').val()
					};
					if(data.construccion.capacidad == ""){
						p.$w.find('[name=txtCantidad]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe Ingresar la capacidad!",type:"error"});
					}
					if(data.construccion.ancho == ""){
						p.$w.find('[name=txtAncho]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe Ingresar el ancho!",type:"error"});
					}
					if(data.construccion.largo == ""){
						p.$w.find('[name=txtLargo]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe Ingresar el largo!",type:"error"});
					}
					if(data.construccion.decreto == ""){
						p.$w.find('[name=decreto]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: "Debe ingresar el decreto correspondiente a la construcci&oacute;n!",
							type:"error"
						});
					}
					data.construccion.altura1 = p.$w.find('[name=txtAltura1]').val();
					data.construccion.altura2 = p.$w.find('[name=txtAltura2]').val();
					data.construccion.fecven = p.$w.find('[name=fecven]').val();
					if(data.construccion.fecven==""){
						p.$w.find('[name=fecven]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe Ingresar la fecha de vencimiento!",type:"error"});
					}
					data.construccion.fecdec = p.$w.find('[name=fecdec]').val();
					if(data.construccion.fecdec==""){
						p.$w.find('[name=fecdec]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe Ingresar la fecha de decreto!",type:"error"});
					}
					data.programacion = new Object;
					data.programacion.observ = p.$w.find('[name=observ]').val();
					var extra = p.payment.getPay();
					if(extra!=false&&extra!=10) $.extend(data,extra);
					else if(extra==false) return false;
					if(p.validarMedidas()){
						K.sendingInfo();
						p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
						$.post("cm/oper/save_cons",data,function(){
							K.clearNoti();
							K.closeWindow(p.$w.attr('id'));
							cmCuen.init();
							K.notification({title: 'Operaci&oacute;n Guardada',text: "La Operaci&oacute;n se guard&oacute; exitosamente!"});
						},'json');
					}
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#'+p.windId);
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
				});
				p.$mainPanel = p.$w.find('.ui-layout-center');
				p.$leftPanel = p.$w.find('.ui-layout-west');
				p.$w.find('[name=btnBusPro]').click(function(){
					/*ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbProp,filter: [
					    //{nomb: 'tipo_enti',value: 'P'},
					    {nomb: 'roles.propietario',value: {$exists: true}}
					]});*/
					mgEnti.windowSelect({callback: p.cbProp,filter: [
					    //{nomb: 'tipo_enti',value: 'P'},
					    {nomb: 'roles.propietario',value: {$exists: true}}
					]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnNewPro]').remove();
				p.$w.find('[name=fecven]').datepicker();
				p.$w.find('[name=fecdec]').datepicker();
				if(p.entidad!=null){
					p.$w.find('td:first').hide();
					p.cbProp(p.entidad);
				}
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
				K.unblock({$element: p.$w});
				p.$w.find('[name=cboSelEspacio]').change(function () {
					p.selEspacio();
				});
				p.$w.find('[name=txtCantidad], [name=txtAncho], [name=txtLargo]').change(function () {
					p.validarMedidas();
				});
				p.payment = new cmOper.payment({
					$w: p.$w,
					getEspa: function(){
						return p.espacio;
					}
				});
			}
		});
	},
	windowNewAmpliacion: function(p){
		if(p==null) p = {};
		p.cbProp = function(data){
			p.$w.find('[name=btnBusPro]').button('option','text',false);
			p.$w.find('[name=nomb]').data('data',data);
			if(data.tipo_enti=='P'){
				p.$w.find('tr:eq(1)').show();
				p.$w.find('[name=nomb]').html( data.nomb );
				p.$w.find('[name=apell]').html( data.appat + ' ' + data.apmat );
			}else{
				p.$w.find('tr:eq(1)').hide();
				p.$w.find('[name=nomb]').html( data.nomb );
			}
			$.post('cm/espa/all_mausoleos',data,function(mausoleos){
				var $select = p.$w.find('[name=cboSelEspacio]');
				$select.empty().append('<option value="">--</option>');
				if(mausoleos!=null){
					for(var i=0; i<mausoleos.length; i++){
						$select.append('<option data-capacidad="'+mausoleos[i].capacidad+'" data-largo="'+mausoleos[i].mausoleo.medidas.largo+'" data-ancho="'+mausoleos[i].mausoleo.medidas.ancho+'" value="'+mausoleos[i]._id.$id+'">'+mausoleos[i].nomb+'</option>');
					}
				}
				if(p.espacio!=null){
					$select.selectVal(p.espacio._id.$id);
					$select.attr('disabled','disabled');
				}
			},'json');
		};
		p.selEspacio = function(){
			var selected = p.$w.find('[name=cboSelEspacio] option:selected');
		    p.espacio = {
		    	_id: selected.val(),
		    	nomb: selected.html(),
		    	capacidad: selected.data('capacidad'),
		    	largo: selected.data('largo'),
		    	ancho: selected.data('ancho')
		    };
			p.payment.loadData();
		};
		if(p.entidad!=null){
			p.windId = 'windowNewAmpliacion'+p.entidad._id.$id;
		}
		else
			p.windId = 'windowNewAmpliacion';
		new K.Modal({
			id: p.windId,
			title: 'Nueva Ampliaci&oacute;n',
			icon: 'ui-icon-home',
			width: 700,
			height: 410,
			contentURL: 'cm/oper/new_cons',
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {};
					if(p.$w.find('[name=nomb]').data('data')==null){
						p.$w.find('[name=btnBusPro]').click();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: "Seleccione un propietario!",
							type:"error"
						});
					}
					data.propietario = ciHelper.enti.dbRel(p.$w.find('[name=nomb]').data('data'));
					if(p.$w.find('[name=cboSelEspacio] option:selected').html() == "--"){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: "Seleccione un mausoleo!",
							type:"error"
						});
					}
					data.nomb = p.espacio.nomb;
					data.espacio = {
						_id: p.espacio._id,
						nomb: p.espacio.nomb
					};
					data.ampliacion = {
						decreto: p.$w.find('[name=decreto]').val(),
						capacidad: p.$w.find('[name=txtCantidad]').val(),
						ancho: p.$w.find('[name=txtAncho]').val(),
						largo: p.$w.find('[name=txtLargo]').val()
					};
					if(data.ampliacion.capacidad == ""){
						p.$w.find('[name=txtCantidad]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe Ingresar la capacidad!",type:"error"});
					}
					if(data.ampliacion.ancho == ""){
						p.$w.find('[name=txtAncho]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe Ingresar el ancho!",type:"error"});
					}
					if(data.ampliacion.largo == ""){
						p.$w.find('[name=txtLargo]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe Ingresar el largo!",type:"error"});
					}
					if(data.ampliacion.decreto == ""){
						p.$w.find('[name=decreto]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: "Debe ingresar el decreto correspondiente a la construcci&oacute;n!",
							type:"error"
						});
					}
					data.ampliacion.altura1 = p.$w.find('[name=txtAltura1]').val();
					data.ampliacion.altura2 = p.$w.find('[name=txtAltura2]').val();
					data.ampliacion.fecven = p.$w.find('[name=fecven]').val();
					data.ampliacion.fecdec = p.$w.find('[name=fecdec]').val();
					if(data.ampliacion.fecven==""){
						p.$w.find('[name=fecven]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe Ingresar la fecha de vencimiento!",type:"error"});
					}
					if(data.ampliacion.fecdec==""){
						p.$w.find('[name=fecdec]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe Ingresar la fecha de decreto!",type:"error"});
					}
					data.programacion = new Object;
					data.programacion.observ = p.$w.find('[name=observ]').val();
					var extra = p.payment.getPay();
					if(extra!=false&&extra!=10) $.extend(data,extra);
					else if(extra==false) return false;
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/oper/save_amp",data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						cmCuen.init();
						K.notification({title: 'Operaci&oacute;n Guardada',text: "La Operaci&oacute;n se guard&oacute; exitosamente!"});
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#'+p.windId);
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
				});
				p.$mainPanel = p.$w.find('.ui-layout-center');
				p.$mainPanel.find('fieldset:eq(1) label:eq(0)').html('Capacidad Total');
				p.$leftPanel = p.$w.find('.ui-layout-west');
				p.$w.find('[name=btnBusPro]').click(function(){
					/*ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbProp,filter: [
					    //{nomb: 'tipo_enti',value: 'P'},
					    {nomb: 'roles.propietario',value: {$exists: true}}
					]});*/
					mgEnti.windowSelect({callback: p.cbProp,filter: [
					    //{nomb: 'tipo_enti',value: 'P'},
					    {nomb: 'roles.propietario',value: {$exists: true}}
					]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnNewPro]').remove();
				p.$w.find('[name=fecven]').datepicker();
				p.$w.find('[name=fecdec]').datepicker();
				if(p.entidad!=null){
					p.$w.find('td:first').hide();
					p.cbProp(p.entidad);
				}
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
				K.unblock({$element: p.$w});
				p.$w.find('[name=cboSelEspacio]').change(function () {
					p.selEspacio();
				});
				/*p.$w.find('[name=txtCantidad], [name=txtAncho], [name=txtLargo]').change(function () {
					p.validarMedidas();
				});*/
				p.payment = new cmOper.payment({
					$w: p.$w,
					getEspa: function(){
						return p.espacio;
					}
				});
			}
		});
	},
	windowNewColo: function(p){
		if(p==null) p = new Object;
		if(p.entidad!=null)
			p.windId = 'windowNewColo'+p.entidad._id.$id;
		else
			p.windId = 'windowNewColo';
		p.cbProp = function(data){
			p.entidad = data;
			p.$w.find('[name=nomb]').data('data',data);
			p.$w.find('[name=nomb]').html(mgEnti.formatName(data));
			p.$w.find('[name=doc]').html(mgEnti.formatIden(data));
			p.$w.find('[name=tel]').html(mgEnti.formatTelf(data));
			if(data.domicilios!=null)
				p.$w.find('[name=direc]').html(data.domicilios[0].direccion);
			p.$w.find('[name=griddif]').empty();
			p.cbEspacios();
		};
		p.cbAccesorios = function(data){
			var $row = p.$w.find('.gridReference1').clone();
			$li = $('li',$row);
			$li.eq(0).html('<button>Eliminar</button>');
			$li.eq(1).html( data.nomb);
			$row.find('button').click(function(){
				$(this).closest('.item').remove();
				p.payment.loadData();
			}).button({icons: {primary: 'ui-icon-trash'},text: false});
			$row.wrapInner('<a class="item"  href="javascript: void(0);" />');
			$row.find('a').data('data',data);
			p.$w.find('[name=gridacce]').append( $row.children() );
			p.payment.loadData();
		};
		p.cbDifuntos = function(data){
			p.$w.find('[name=gridacce], [name=griddif]').empty();
			$.post('mg/enti/all_difu_espa',data,function(difuntos){
				if(difuntos!=null){
					for(var i=0; i<difuntos.length; i++){
						var $row = p.$w.find('[name=section3] .gridReference').clone();
						$li = $('li',$row);
						$li.eq(1).html( difuntos[i].nomb + ' ' + difuntos[i].appat + ' ' + difuntos[i].apmat);
						$row.wrapInner('<a class="item"  href="javascript: void(0);" />');
						$row.find('a').data('data',difuntos[i]);
						p.$w.find('[name=griddif]').append( $row.children() );
					}
				}
				p.payment.loadData();
			},'json');
		};
		p.cbEspacios = function(){
			p.$w.find('[name=cboSelEspacio]').removeData('espacios');
			p.$w.find('[name=cboSelEspacio]').empty();
			p.$w.find(".gridBody:last").empty();
			p.$w.find('[name=det]').html("");
			p.espacio = null;
			$.post('cm/espa/espa_prop','_id='+p.entidad._id.$id,function(data){
				if(data!=null){
					$select = p.$w.find('[name=cboSelEspacio]').data('espacios',data);
					for(var i=0; i<data.length; i++){
						$select.append('<option value="'+data[i]._id.$id+'" _index="'+i+'">'+data[i].nomb+'</option>');
					}
					$select.unbind().bind('change',function(){
						var data = p.$w.find('[name=cboSelEspacio]').data('espacios');
						p.cbEspacio(data[$(this).find('option:selected').attr('_index')]);
					})//.change();
					if(p.espacio_ori!=null){
						$select.selectVal(p.espacio_ori._id.$id);
						$select.attr('disabled','disabled');
						$select.change();
					}else
						$select.change();
					p.$w.find('[name=btnAgreAcc]').button('enable');
					p.$w.find('[name=btnIncAcc]').button('enable');
				}else{
					K.notification({title: ciHelper.titleMessages.infoReq,text: 'La entidad seleccionada no tiene ning&uacute;n espacio asignado!'});
				}
			},'json');
		};
		p.cbEspacio = function(data){
			p.$w.find('[name=btnAgregar]').button('enable');
			p.espacio = data;
			var tipo = '';
			p.$w.find('[name=det]').html("").empty();
			if(data.nicho!=null){
				tipo = "Nicho - "+cmOper.statesNicho[data.nicho.tipo].descr;
				p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Nombre del Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.nomb+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.num+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Nicho:</label></td><td>"+data.nicho.num+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Piso:</label></td><td>"+data.nicho.piso+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Fila:</label></td><td>"+data.nicho.fila+"</td></tr>");
			}else if(data.tumba!=null){
				tipo = "Tumba";
				p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Referencia:</label></td><td>"+data.nomb+"</td></tr>");
			}else{
				tipo = "Mausoleo";
				if(data.osario==true){
					p.$w.find('[name=det]').append("<tr><td>Actualmente en el Osario</td></tr>");
				}else{
					p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>Tipo de Zona:</label></td><td>"+cmOper.statesZonaMaus[data.mausoleo.zona].descr+"</td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.mausoleo.denominacion+"</td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Lote:</label></td><td>"+data.mausoleo.lote+"</td></tr>");
				}
			}
			p.$w.find('[name=tipo]').data('data',data);
			p.payment.loadData();
			p.cbDifuntos(data);
		};
		new K.Modal({
			id: p.windId,
			title: 'Nueva Colocaci&oacute;n',
			icon: 'ui-icon-copy',
			width: 660,
			height: 410,
			contentURL: 'cm/oper/new_colo',
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					if(p.$w.find('[name=nomb]').html() == ""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un propietario!",type:"error"});
					}
					var propietario = p.$w.find('[name=nomb]').data('data');
					data.propietario = ciHelper.enti.dbRel(propietario);
					if(p.$w.find('[name=cboSelEspacio] option').length == 0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un propietario con espacios asignados!",type:"error"});
					}
					var espacio = p.$w.find('[name=tipo]').data('data');
					data.espacio = new Object;
					data.espacio._id = espacio._id.$id;
					data.espacio.nomb = espacio.nomb;
					/*var ocupante = p.$w.find('[name=griddif] .ui-state-highlight').closest('.item').data('data');
					if(p.$w.find('[name=griddif] .ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un difunto!',type: 'error',layout: 'topLeft'});
					}
					data.ocupante = new Object;
					data.ocupante._id = ocupante._id.$id;
					data.ocupante.nomb = ocupante.nomb;
					if(ocupante.appat !=null){
						data.ocupante.appat = ocupante.appat;
						data.ocupante.apmat = ocupante.apmat;
					}*/
					data.colocacion = new Object;
					var $accesorios = p.$w.find('[name=gridacce] a');
					if($accesorios.length<=0) return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar al menos un accesorio!',type: 'error'});
					data.colocacion.accesorios = new Array;
					for(var i=0; i<$accesorios.length; i++){
						var acc = $accesorios.eq(i).data('data');
						data.colocacion.accesorios.push(acc);
					}
					data.programacion = new Object;
					if(p.$w.find('[name=fecprog]').val() == ""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione una fecha de programaci&oacute;n!",type:"error"});
					}
					data.programacion.fecprog = p.$w.find('[name=fecprog]').val();
					data.programacion.observ = p.$w.find('[name=observ]').val();
					var extra = p.payment.getPay();
					if(extra!=false&&extra!=10) $.extend(data,extra);
					else if(extra==false) return false;
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/oper/save_colo",data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						cmCuen.init();
						K.notification({title: ciHelper.titleMessages.regiGua,text: "La operaci&oacute;n se guard&oacute; exit&oacute;samente!"});
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#'+p.windId);
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
				});
				K.unblock({$element: p.$w});
				p.$mainPanel = p.$w.find('.ui-layout-center');
				p.$leftPanel = p.$w.find('.ui-layout-west');
				if(p.entidad!=null){
					p.cbProp(p.entidad);
					p.$w.find('[name=btnBusPro]').remove();
					p.$w.find('[name=btnEdiPro]').click(function(){
						var entidad = p.$w.find('[name=nomb]').data('data');
						var params = {
							id: entidad._id.$id,
							nomb: mgEnti.formatName(entidad),
							tipo_enti: entidad.tipo_enti,
							data: entidad,
							callBack: function(data){
								$.post('cm/prop/save',data,function(rpta){
									p.cbProp(rpta);
									K.notification({title: ciHelper.titleMessages.regiAct,text: 'Ocupante actualizado!'});
									K.closeWindow('windowEntiEdit'+rpta._id.$id);
								},'json');
							}
						};
						ciEdit.windowEditEntidad(params);
					}).button({icons: {primary: 'ui-icon-pencil'}});
				}else{
					p.$w.find('[name=btnBusPro]').click(function(){
    					mgEnti.windowSelect({callback: p.cbProp,filter: [
    					    {nomb: 'roles.propietario',value: {$exists: true}}
    					]});
					}).button({icons: {primary: 'ui-icon-search'}});
					p.$w.find('[name=btnEdiPro]').remove();
				}
				p.$w.find('.grid:eq(1)').css('overflow','hidden');
				p.$w.find('.grid:eq(2)').scroll(function(){
					p.$w.find('.grid:eq(1)').scrollLeft($(this).scrollLeft());
				});
				p.$w.find('[name=fecprog]').datetimepicker({
					minuteGrid: 10
				});
				p.$w.find('[name=btnAgreAcc]').click(function(){
					ciSearch.windowSearchAcce({$window: p.$w,callBack: p.cbAccesorios});
				}).button({icons: {primary: 'ui-icon-search'}}).button('disable');
				p.$w.find('[name=btnIncAcc]').click(function(){
					cmAcce.windowNew({callback: p.cbAccesorios});
				}).button({icons: {primary: 'ui-icon-plusthick'}}).button('disable');
				p.$mainPanel.css('overflow-x','hidden');
				p.$leftPanel.find('a').bind('click',function(event){
					event.preventDefault();
					p.$mainPanel.scrollTo( p.$mainPanel.find('[name='+$(this).attr('name')+']'), 800 );
				}).eq(0).click();
				p.$leftPanel.find('a:first').click().find('ul').addClass('ui-state-highlight');
				p.$w.layout({
					resizeWithWindow:	false,
					west__size:			150,
					west__closable:		false,
					west__resizable:	false,
					west__slidable:		false
				});
				p.payment = new cmOper.payment({
					$w: p.$w,
					getEspa: function(){
						return p.$w.find('[name=tipo]').data('data');
					},
					getAcce: function(){	
						var acce,total = 0;
						for(var i=0,j=p.$w.find('[name=gridacce] .item').length; i<j; i++){
							acce = p.$w.find('[name=gridacce] .item').eq(i).data('data');
							total += parseFloat(acce.precio);
						}
						return total;
					}
				});
				$.post('cj/cuen/get_config_ceme',function(data){
					p.config = data;
					var tmp_serv = p.config.COLO;
					p.$w.find('[name=section6] [name^=fecven]').val(ciHelper.dateFormatNowBDNotHour());
					p.$w.find('[name=section6] [name^=serv]').html('').removeData('data');
					p.$w.find('[name=section6] [id^=tabsConcPayment] .gridBody').empty();
					$.post('cj/conc/get_serv','id='+tmp_serv._id.$id,function(concs){
						if(concs.serv==null){
							return K.notification({title: 'Servicio inv&aacute;lido',text: 'El servicio seleccionado no tiene conceptos asociados!',type: 'error'});
						}
						p.$w.data('vars',concs.vars);
						p.$w.find('[name=section6] [name^=serv]').html(tmp_serv.nomb).data('data',tmp_serv).data('concs',concs.serv);
						p.$w.find('[name=section6] [name^=fecven]').change();
						K.unblock({$element: p.$w});
					},'json');
				},'json');
			}
		});
	},
	windowNewTraslado: function(p){
		if(p==null) p = new Object;
		p.cbCementerio = function(data){
			p.$w.find('[name=cementerio]').data('data',data);
			p.$w.find('[name=cementerio]').html( data.nomb );
		};
		p.cbGestor = function(data){
			p.$w.find('[name=nomb]').data('data',data);
			if(data.tipo_enti=='P'){
				p.$w.find('tr:eq(1)').show();
				p.$w.find('[name=nomb]').html( data.nomb );
				p.$w.find('[name=apell]').html( data.appat + ' ' + data.apmat );
			}else{
				p.$w.find('tr:eq(1)').hide();
				p.$w.find('[name=nomb]').html( data.nomb );
			}
			$.post('cm/prop/all_difu','_id='+data._id.$id,function(difuntos){
				var $select = p.$w.find('[name=difunto]');
				$select.empty().append('<option value="">--</option>');
				if(difuntos!=null){
					for(var i=0; i<difuntos.length; i++){
						if(difuntos[i].roles.ocupante.espacio){
							var ocupante = difuntos[i].appat + ' '+difuntos[i].apmat+', '+difuntos[i].nomb;
							$select.append('<option data-espacio="'+difuntos[i].roles.ocupante.espacio._id.$id+'" value="'+difuntos[i]._id.$id+'-'+difuntos[i].nomb+'-'+difuntos[i].appat+'-'+difuntos[i].apmat+'">'+ocupante+'</option>');
						}
					}
				}
			},'json');
		};
		p.cbEspacio = function(data){
			var tipo = '';
			var espacio;
			if(typeof(data.actual) != "undefined" || data.actual != null){
				espacio = p.$w.find('[name=espacio]');
			}else{
				espacio = p.$w.find('[name=destino]');
				data.actual = "tipo";
				p.$w.find('[name=desEspa]').html("destino");
			}
			espacio.html("");
			if(data!=null){
				if(data.nicho!=null){
					tipo = "Nicho-"+cmOper.statesNicho[data.nicho.tipo].descr;
					espacio.append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='"+data.actual+"'>"+tipo+"</span></td></tr>");
					espacio.append("<tr><td><label>Nombre del Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.nomb+"</td></tr>");
					espacio.append("<tr><td><label>N&uacute;mero de Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.num+"</td></tr>");
					espacio.append("<tr><td><label>N&uacute;mero de Nicho:</label></td><td>"+data.nicho.num+"</td></tr>");
					espacio.append("<tr><td><label>Piso:</label></td><td>"+data.nicho.piso+"</td></tr>");
					espacio.append("<tr><td><label>Fila:</label></td><td>"+data.nicho.fila+"</td></tr>");
				}
				else if(data.tumba!=null){
						tipo = "Tumba";
						espacio.append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='"+data.actual+"'>"+tipo+"</span></td></tr>");
						espacio.append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.tumba.denominacion+"</td></tr>");
				}else{
					tipo = "Mausoleo";
					if(data.osario==true){
						espacio.append("<tr><td><label>En el Osario</label></td></tr>");
					}else{
						espacio.append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='"+data.actual+"'>"+tipo+"</span></td></tr>");
						espacio.append("<tr><td><label>Tipo de Zona:</label></td><td>"+cmOper.statesZonaMaus[data.mausoleo.zona].descr+"</td></tr>");
						espacio.append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.mausoleo.denominacion+"</td></tr>");
						espacio.append("<tr><td><label>N&uacute;mero de Lote:</label></td><td>"+data.mausoleo.lote+"</td></tr>");
					}
				}
				p.$w.find('[name='+data.actual+']').data('data',data);
				p.payment.loadData();
			}
		};
		if(p.entidad!=null){
			p.windId = 'windowNewTraslado'+p.entidad._id.$id+p.tipotras;
		}
		else
			p.windId = 'windowNewTraslado'+p.tipotras;
		if(p.tipotras == 1)
			p.titulo = 'Interno - Coactivo';
		else if(p.tipotras == 2){
			p.titulo = 'Externo';
			p.icon = 'ui-icon-extlink';
		}else{
			p.titulo = 'Interno';
			p.icon = 'ui-icon-arrow-2-e-w';
		}
		new K.Modal({
			id: p.windId,
			title: 'Nuevo Traslado '+p.titulo,
			icon: p.icon,
			width: 700,
			height: 410,
			contentURL: 'cm/oper/new_tras',
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					if(p.$w.find('[name=nomb]').html() == ""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un propietario!",type:"error"});
					}
					var propietario = p.$w.find('[name=nomb]').data('data');
					data.propietario = ciHelper.enti.dbRel(propietario);
					if(p.$w.find('[name=difunto] option:selected').html() == "--"){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un difunto!",type:"error"});
					}
					var ocu = p.$w.find('[name=difunto] option:selected').val().split("-");
					data.ocupante = new Object;
					data.ocupante._id = ocu[0];
					data.ocupante.tipo_enti = 'P';
					data.ocupante.nomb = ocu[1];
					data.ocupante.appat = ocu[2];
					data.ocupante.apmat = ocu[3];
					var espacio = p.$w.find('[name=espaActual]').data('data');
					data.espacio = new Object;
					data.espacio._id = espacio._id.$id;
					data.espacio.nomb = espacio.nomb;
					data.traslado = new Object;
					if(p.tipotras == 2){
						if(p.$w.find('[name=cementerio]').html()!=""){
							var cementerio = p.$w.find('[name=cementerio]').data('data');
							data.traslado.cementerio = new Object;
							data.traslado.cementerio._id = cementerio._id.$id;
							data.traslado.cementerio.tipo_enti = cementerio.tipo_enti;
							data.traslado.cementerio.nomb = cementerio.nomb;
						}
						data.traslado.ubicacion = p.$w.find('[name=ubi]').val();
					}
					else{
						data.traslado.espacio_destino = new Object;
						if(p.tipotras == 1){
							if(osario != null){
								data.traslado.espacio_destino._id = p.osario._id.$id;
								data.traslado.espacio_destino.nomb = p.osario.nomb;
							}
							else
								return K.notification({title: ciHelper.titleMessages.infoReq,text: "No existe un osario!",type:"error"});
						}
						else{
							if(p.$w.find('[name=desEspa]').html() == ""){
								return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un destino!",type:"error"});
							}
							var espaDest = p.$w.find('[name=tipo]').data('data');
							data.traslado.espacio_destino._id = espaDest._id.$id;
							data.traslado.espacio_destino.nomb = espaDest.nomb;
							if(data.traslado.espacio_destino._id==data.espacio._id){
								return K.notification({
									title: ciHelper.titleMessages.infoReq,
									text: 'Debe seleccionar un espacio distinto al espacio de origen!',
									type: 'error'
								});
							}
						}
					}
					if(p.$w.find('[name=fecprog]').val() == ""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese fecha programada!",type:"error"});
					}
					data.programacion = new Object;
					data.programacion.fecprog = p.$w.find('[name=fecprog]').val();
					data.programacion.observ = p.$w.find('[name=observ]').val();
					var extra = p.payment.getPay();
					if(extra!=false&&extra!=10) $.extend(data,extra);
					else if(extra==false) return false;
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/oper/save_tras",data,function(){
						K.clearNoti();
						cmCuen.init();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: 'Operaci&oacute;n Guardada',text: "La Operaci&oacute;n se guard&oacute; exitosamente!"});
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#'+p.windId);
				p.$w.find('[name=btnSelEspacio]').click(function(){
					//cmEspa.windowElegir({$parent: p.$w,callBack: p.cbEspacio,prop: true});
					if(p.$w.find('[name=nomb]').data('data')==null){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un propietario!',
							type: 'error'
						});
					}
					cmEspa.windowSelect({
						$window: p.$w,
						callback: p.cbEspacio,
						filter: [
						    //{nomb: 'propietario',value: {$exists: true}}
						    {nomb: 'propietario._id',value: p.$w.find('[name=nomb]').data('data')._id.$id}
						]
					});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnBusPro]').click(function(){
					/*ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbGestor,filter: [
					    {nomb: 'roles.propietario',value: {$exists: true}}
					]});*/
					mgEnti.windowSelect({callback: p.cbGestor,filter: [
					    {nomb: 'roles.propietario',value: {$exists: true}}
					]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnNewPro]').remove();
				p.$w.find('[name=btnBusCem]').click(function(){
					ciSearch.windowSearchEmpresa({$window: p.$w,callback: p.cbCementerio});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnNewCem]').click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbCementerio});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('[name=fecprog]').datetimepicker({
					minuteGrid: 10
				});
				p.$w.find('[name=fecprog]').datepicker( "option", "dateFormat", 'yy-mm-dd' );
				if(p.entidad!=null){
					p.$w.find('td:first').hide();
					p.cbGestor(p.entidad);
				}
				if(p.tipotras == 1){
					p.$w.find('[name=btnSelEspacio]').remove();
					p.$w.find('[name=rowCoactivo]').show();
					p.$w.find('[name=rowExterno]').hide();
					p.$w.find('[name=rowIxterno]').hide();
					$.post("cm/espa/get_osario",function(osario){
							p.osario = osario;
					},'json');
				}
				else if(p.tipotras == 2){
						p.$w.find('[name=btnSelEspacio]').remove();
						p.$w.find('[name=rowExterno]').show();
						p.$w.find('[name=rowIxterno]').hide();
						p.$w.find('[name=rowCoactivo]').hide();
					}
					else {
						p.$w.find('[name=rowIxterno]').show();
						p.$w.find('[name=rowExterno]').hide();
						p.$w.find('[name=rowCoactivo]').hide();
					}
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
				});
				K.unblock({$element: p.$w});
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
				p.$w.find('[name=difunto]').change(function () {
					var selected = $(this).find('option:selected');
				    var espa = new Object;
				    espa._id = selected.data('espacio');
					$.post('cm/espa/get_one',espa,function(espacio){
						espacio.actual = "espaActual";
						p.cbEspacio(espacio);
					},'json');
				});
				p.payment = new cmOper.payment({
					$w: p.$w,
					getEspa: function(){
						return p.$w.find('[name=espaActual]').data('data');
					}
				});
			}
		});
	},
	windowNewTrasp: function(p){
		if(p==null) p = new Object;
		p.cbProp = function(data){
			p.entidad = data;
			p.$w.find('[name=nomb]').data('data',p.entidad);
			if(p.entidad.tipo_enti=='P'){
				p.$w.find('tr:eq(1)').show();
				p.$w.find('[name=nomb]').html( p.entidad.nomb );
				p.$w.find('[name=apell]').html( p.entidad.appat + ' '+p.entidad.apmat );
			}else{
				p.$w.find('tr:eq(1)').hide();
				p.$w.find('[name=nomb]').html( p.entidad.nomb );
			}
			p.cbEspacios();
		};
		p.cbEspacios = function(){
			p.$w.find('[name=cboSelEspacio]').removeData('espacios');
			p.$w.find('[name=cboSelEspacio]').empty();
			p.$w.find(".gridBody:last").empty();
			p.$w.find('[name=det]').html("");
			p.espacio = null;
			$.post('cm/espa/espa_prop','_id='+p.entidad._id.$id,function(data){
				if(data!=null){
					$select = p.$w.find('[name=cboSelEspacio]').data('espacios',data);
					for(var i=0; i<data.length; i++){
						$select.append('<option value="'+data[i]._id.$id+'" _index="'+i+'">'+data[i].nomb+'</option>');
					}
					$select.unbind().bind('change',function(){
						var data = p.$w.find('[name=cboSelEspacio]').data('espacios');
						p.cbEspacio(data[$(this).find('option:selected').attr('_index')]);
					});//.change();
					if(p.espacio_ori!=null){
						$select.selectVal(p.espacio_ori._id.$id);
						$select.attr('disabled','disabled');
						$select.change();
					}else
						$select.change();
				}else{
					K.notification({title: ciHelper.titleMessages.infoReq,text: 'La entidad seleccionada no tiene ning&uacute;n espacio asignado!'});
				}
			},'json');
		};
		p.cbEspacio = function(data){
			p.$w.find("[name=section3] .gridBody").empty();
			p.espacio = data;
			var tipo = '';
			p.$w.find('[name=det]').html("");
			p.$w.find('[name=det]').append("<table>");
			if(data.nicho!=null){
				tipo = "Nicho - "+cmOper.statesNicho[data.nicho.tipo].descr;
				p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Nombre del Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.nomb+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.num+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Nicho:</label></td><td>"+data.nicho.num+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Piso:</label></td><td>"+data.nicho.piso+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Fila:</label></td><td>"+data.nicho.fila+"</td></tr>");
			}else if(data.tumba!=null){
				tipo = "Tumba";
				p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Referencia:</label></td><td>"+data.nomb+"</td></tr>");
			}else{
				tipo = "Mausoleo";
				p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Tipo de Zona:</label></td><td>"+cmOper.statesZonaMaus[data.mausoleo.zona].descr+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.mausoleo.denominacion+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Lote:</label></td><td>"+data.mausoleo.lote+"</td></tr>");
			}
			p.$w.find('[name=det]').append("</table>");
			p.$w.find('[name=tipo]').data('data',data);
			if(data.ocupantes!=null){
				for(var i=0,j=data.ocupantes.length; i<j; i++){
					var $row = p.$w.find('[name=section3] .gridReference').clone();
					$row.find('li:eq(0)').html(i+1);
					if(data.ocupantes[i].tipo_enti=='P') $row.find('li:eq(1)').html(data.ocupantes[i].nomb+' '+data.ocupantes[i].appat+' '+data.ocupantes[i].apmat);
					else $row.find('li:eq(1)').html(data.ocupantes[i].nomb);
					$row.wrapInner('<a class="item"/>');
					p.$w.find("[name=section3] .gridBody").append( $row.children() );
				}
			}
		};
		p.cbNewProp = function(data){
			p.newEntidad = data;
			p.$w.find('[name=nombCuent]').data('data',p.newEntidad);
			if(p.newEntidad.tipo_enti=='P'){
				p.$w.find('table:last tr:eq(1)').show();
				p.$w.find('[name=nombCuent]').html( p.newEntidad.nomb );
				p.$w.find('[name=apellCuent]').html( p.newEntidad.appat + ' '+p.newEntidad.apmat );
			}else{
				p.$w.find('table:last tr:eq(1)').hide();
				p.$w.find('[name=nombCuent]').html( p.newEntidad.nomb );
			}
		};
		new K.Modal({
			id: 'windowNewTrasp',
			title: 'Traspaso de Titularidad',
			icon: 'ui-icon-bookmark',
			width: 680,
			height: 400,
			contentURL: 'cm/oper/trasp',
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					var propietario = p.$w.find('[name=nomb]').data('data');
					if(propietario==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un propietario!',type: 'error'});
					}
					data.propietario = ciHelper.enti.dbRel(propietario);
					var espacio = p.$w.find('[name=tipo]').data('data');
					if(espacio==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un espacio!',type: 'error'});
					}
					data.espacio = {};
					data.espacio._id = espacio._id.$id;
					data.espacio.nomb = espacio.nomb;
					data.ocupante = [];
					if(espacio.ocupantes!=null){
						for(var i=0; i<espacio.ocupantes.length; i++){
							var ocup = espacio.ocupantes[i];
							ocup._id = ocup._id.$id;
							data.ocupante.push(ocup);
						}
					}
					propietario = p.$w.find('[name=nombCuent]').data('data');
					if(propietario==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un nuevo propietario!',type: 'error'});
					}
					data.new_propietario = new Object;
					data.new_propietario._id = propietario._id.$id;
					data.new_propietario.tipo_enti = propietario.tipo_enti;
					data.new_propietario.nomb = propietario.nomb;
					data.new_propietario.appat = propietario.appat;
					data.new_propietario.apmat = propietario.apmat;
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('cm/oper/save_trasp',data,function(){
						K.clearNoti();
						K.notification({title: 'Operaci&oacute;n ejecutada',text: 'La operaci&oacute;n se realiz&oacute; con &eacute;xito!'});
						K.closeWindow(p.$w.attr('id'));
						cmCuen.init();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowNewTrasp');
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
				});
				K.unblock({$element: p.$w});
				p.$mainPanel = p.$w.find('.ui-layout-center');
				p.$leftPanel = p.$w.find('.ui-layout-west');
				if(p.entidad!=null){
					p.cbProp(p.entidad);
					p.$w.find('td:first').remove();
				}else{
					p.$w.find('[name=btnBusPro]').click(function(){
						/*ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbProp,filter: [
     					    //{nomb: 'tipo_enti',value: 'P'},
    					    {nomb: 'roles.propietario',value: {$exists: true}}
    					]});*/
    					mgEnti.windowSelect({callback: p.cbProp,filter: [
     					    //{nomb: 'tipo_enti',value: 'P'},
    					    {nomb: 'roles.propietario',value: {$exists: true}}
    					]});
					}).button({icons: {primary: 'ui-icon-search'}});
					p.$w.find('[name=btnNewPro]').remove();
				}
				p.$w.find('.grid:eq(1)').css('overflow','hidden');
				p.$w.find('.grid:eq(2)').scroll(function(){
					p.$w.find('.grid:eq(1)').scrollLeft($(this).scrollLeft());
				});
				p.$w.find('[name=btnBusCuent]').click(function(){
					//ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbNewProp});
					mgEnti.windowSelect({callback: p.cbNewProp});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnNewCuent]').remove();/*.click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbNewProp});
				}).button({icons: {primary: 'ui-icon-plusthick'}});*/
				p.$mainPanel.css('overflow-x','hidden');
				p.$leftPanel.find('a').bind('click',function(event){
					event.preventDefault();
					p.$mainPanel.scrollTo( p.$mainPanel.find('[name='+$(this).attr('name')+']'), 800 );
				}).eq(0).click();
				p.$leftPanel.find('a:first').click().find('ul').addClass('ui-state-highlight');
				p.$w.layout({
					resizeWithWindow:	false,
					west__size:			150,
					west__closable:		false,
					west__resizable:	false,
					west__slidable:		false
				});
			}
		});
	},
	windowAnulAsig: function(p){
		if(p==null) p = new Object;
		p.cbPropietario = function(data){
			p.$w.find('[name=nomb]').data('data',data);
			if(data.tipo_enti=='P'){
				p.$w.find('tr:eq(1)').show();
				p.$w.find('[name=nomb]').html( data.nomb );
				p.$w.find('[name=apell]').html( data.appat + ' ' + data.apmat );
			}else{
				p.$w.find('tr:eq(1)').hide();
				p.$w.find('[name=nomb]').html( data.nomb );
			}
			$.post('cm/ocup/all_ocu_pro','_id='+data._id.$id,function(ocupantes){
				var $select = p.$w.find('[name=ocupante]');
				$select.empty().append('<option value="">--</option>');
				if(ocupantes!=null){
					for(var i=0; i<ocupantes.length; i++){
						var ocupante = ocupantes[i].appat + ' '+ocupantes[i].apmat+', '+ocupantes[i].nomb;
						$select.append('<option data-espacio="'+ocupantes[i].roles.ocupante.espacio._id.$id+'" value="'+ocupantes[i]._id.$id+'-'+ocupantes[i].nomb+'-'+ocupantes[i].appat+'-'+ocupantes[i].apmat+'">'+ocupante+'</option>');
					}
				}
			},'json');
		};
		p.cbNewOcupante = function(data){
			p.$w.find('[name=ocu_nomb]').data('data',data);
			if(data.tipo_enti=='P'){
				p.$w.find('tr:eq(1)').show();
				p.$w.find('[name=ocu_nomb]').html( data.nomb );
				p.$w.find('[name=ocu_apell]').html( data.appat + ' ' + data.apmat );
			}else{
				p.$w.find('tr:eq(1)').hide();
				p.$w.find('[name=ocu_nomb]').html( data.nomb );
			}
		};
		p.cbEspacio = function(data){
			var espacio;
			data.actual = "tipo";
			espacio = p.$w.find('[name=espacio]');
			espacio.html("");
			if(data!=null){
				if(data.nicho!=null){
					tipo = "Nicho-"+cmOper.statesNicho[data.nicho.tipo].descr;
					espacio.append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='"+data.actual+"'>"+tipo+"</span></td></tr>");
					espacio.append("<tr><td><label>Nombre del Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.nomb+"</td></tr>");
					espacio.append("<tr><td><label>N&uacute;mero de Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.num+"</td></tr>");
					espacio.append("<tr><td><label>N&uacute;mero de Nicho:</label></td><td>"+data.nicho.num+"</td></tr>");
					espacio.append("<tr><td><label>Piso:</label></td><td>"+data.nicho.piso+"</td></tr>");
					espacio.append("<tr><td><label>Fila:</label></td><td>"+data.nicho.fila+"</td></tr>");
				}
				else if(data.tumba!=null){
						tipo = "Tumba";
						espacio.append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='"+data.actual+"'>"+tipo+"</span></td></tr>");
						espacio.append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.tumba.denominacion+"</td></tr>");
					 }
					 else {
						tipo = "Mausoleo";
						espacio.append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='"+data.actual+"'>"+tipo+"</span></td></tr>");
						espacio.append("<tr><td><label>Tipo de Zona:</label></td><td>"+cmOper.statesZonaMaus[data.mausoleo.zona].descr+"</td></tr>");
						espacio.append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.mausoleo.denominacion+"</td></tr>");
						espacio.append("<tr><td><label>N&uacute;mero de Lote:</label></td><td>"+data.mausoleo.lote+"</td></tr>");
					 }
				p.$w.find('[name='+data.actual+']').data('data',data);
			}
		};
		if(p.entidad!=null)
			p.windId = 'windowAnulAsig'+p.entidad._id.$id;
		else
			p.windId = 'windowAnulAsig';
		
		new K.Modal({
			id: p.windId,
			title: 'Reasignaci&oacuten',
			icon: 'ui-icon-tag',
			width: 700,
			height: 430,
			contentURL: 'cm/oper/new_reasig',
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					var datos = new Object;
					if(p.$w.find('[name=nomb]').html() == ""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un propietario!",type:"error"});
					}
					var propietario = p.$w.find('[name=nomb]').data('data');
					data.propietario = ciHelper.enti.dbRel(propietario);
					if(p.$w.find('[name=ocupante] option:selected').html() == "--"){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un ocupante!",type:"error"});
					}
					var ocupante = p.$w.find('[name=ocu_nomb]').data('data');
					if(ocupante==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un nuevo Ocupante!",type:"error"});
					}
					data.ocupante = ciHelper.enti.dbRel(ocupante);
					var espacio = p.$w.find('[name=tipo]').data('data');
					data.espacio = new Object;
					data.espacio._id = espacio._id.$id;
					data.espacio.nomb = espacio.nomb;
					if(p.$w.find('[name=ocu_nomb]').html() == ""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un nuevo ocupante!",type:"error"});
					}
					var ocu = p.$w.find('[name=ocupante] option:selected').val().split("-");
					datos.ocupante = new Object;
					datos.ocupante._id = ocu[0];
					datos.ocupante.tipo_enti = 'P';
					datos.ocupante.nomb = ocu[1];
					datos.ocupante.appat = ocu[2];
					datos.ocupante.apmat = ocu[3];
					datos.data = data;
					datos.observ = p.$w.find('[name=observ]').val();
					$.post("cm/oper/save_reasig",datos,function(){
						K.notification({title: 'Operaci&oacute;n Guardada',text: "Asignac&oacute;n anulada se ejecut&oacute; exit&oacute;samente!"});
					},'json');
					var asig = new Object;
					asig.propietario = data.propietario;
					asig.ocupante = data.ocupante;
					asig.espacio = data.espacio;
					asig.asignacion = true;
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/oper/save_asig",asig,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: ciHelper.titleMessages.regiGua,text: "Reasignaci&oacute;n se guard&oacute; exit&oacute;samente!"});
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#'+p.windId);
				p.$w.find('[name=btnBusPro]').click(function(){
					/*ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbPropietario,filter: [
					    //{nomb: 'tipo_enti',value: 'P'},
					    {nomb: 'roles.propietario',value: {$exists: true}}
					]});*/
					mgEnti.windowSelect({callback: p.cbPropietario,filter: [
					    //{nomb: 'tipo_enti',value: 'P'},
					    {nomb: 'roles.propietario',value: {$exists: true}}
					]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnNewPro]').remove();
				p.$w.find('[name=btnBusOcu]').click(function(){
					//ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbNewOcupante});
					mgEnti.windowSelect({callback: p.cbNewOcupante});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnNewOcu]').remove();/*.click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbNewOcupante});
				}).button({icons: {primary: 'ui-icon-plusthick'}});*/
				if(p.entidad!=null){
					p.$w.find('td:first').hide();
					p.cbPropietario(p.entidad);
				}
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
				});
				K.unblock({$element: p.$w});
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
				p.$w.find('[name=ocupante]').change(function () {
					var selected = $(this).find('option:selected');
				    var espa = new Object;
				    espa._id = selected.data('espacio');
					$.post('cm/espa/get_one',espa,function(espacio){
						p.cbEspacio(espacio);
					},'json');
				});
			}
		});
	},
	windowAnulCon: function(p){
		if(p==null) p = new Object;
		p.cbPropietario = function(data){
			p.entidad = data;
			p.$w.find('[name=nomb]').data('data',data);
			if(data.tipo_enti=='P'){
				p.$w.find('tr:eq(1)').show();
				p.$w.find('[name=nomb]').html( data.nomb );
				p.$w.find('[name=apell]').html( data.appat + ' ' + data.apmat );
			}else{
				p.$w.find('tr:eq(1)').hide();
				p.$w.find('[name=nomb]').html( data.nomb );
			}
			p.cbEspacios();
		};
		p.cbEspacios = function(){
			p.$w.find('[name=cboEspacio]').removeData('espacios');
			p.$w.find('[name=cboEspacio]').removeData('ocupantes');
			p.$w.find('[name=cboEspacio]').empty();
			p.$w.find('[name=det]').html("");
			p.$w.find('table:eq(1)').hide();
			p.$w.find("divAsig .gridBody, divDifu .gridBody").empty();
			p.espacio = null;
			$.post('cm/espa/espa_prop_ocup','_id='+p.entidad._id.$id,function(data){
				//$.post('cm/ocup/all_ocu_pro','_id='+data.propietario._id.$id,function(ocupantes){
				ocup = data.ocup;
				p.$w.find('[name=cboEspacio]').data('ocupantes',ocup);
				data = data.espa;
					if(data!=null){
						$select = p.$w.find('[name=cboEspacio]').data('espacios',data);
						for(var i=0; i<data.length; i++){
							$select.append('<option value="'+data[i]._id.$id+'" _index="'+i+'">'+data[i].nomb+'</option>');
						}
						$select.unbind().bind('change',function(){
							var data = p.$w.find('[name=cboEspacio]').data('espacios');
							p.cbEspacio(data[$(this).find('option:selected').attr('_index')]);
						})//.change();
						if(p.espacio!=null){
							$select.selectVal(p.espacio._id.$id);
							$select.attr('disabled','disabled');
							$select.change();
						}else
							$select.change();
					}else{
						K.notification({title: ciHelper.titleMessages.infoReq,text: 'La entidad seleccionada no tiene ning&uacute;n espacio asignado!'});
					}
				//},'json');
			},'json');
		};

		p.cbEspacio = function(data){
			p.$w.find("[name=divAsig] .gridBody, [name=divDifu] .gridBody").empty();
			p.espacio = data;
			var tipo = '';
			p.$w.find('[name=det]').html("");
			p.$w.find('table:eq(1)').hide();
			p.$w.find('[name=det]').append("<table>");
			if(data.nicho!=null){
				tipo = "Nicho - "+cmOper.statesNicho[data.nicho.tipo].descr;
				p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Nombre del Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.nomb+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.num+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Nicho:</label></td><td>"+data.nicho.num+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Piso:</label></td><td>"+data.nicho.piso+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Fila:</label></td><td>"+data.nicho.fila+"</td></tr>");
			}else if(data.tumba!=null){
				tipo = "Tumba";
				p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Referencia:</label></td><td>"+data.nomb+"</td></tr>");
			}else{
				tipo = "Mausoleo";
				p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Tipo de Zona:</label></td><td>"+cmOper.statesZonaMaus[data.mausoleo.zona].descr+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.mausoleo.denominacion+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Lote:</label></td><td>"+data.mausoleo.lote+"</td></tr>");
			}
			p.$w.find('[name=det]').append("</table>");
			p.$w.find('[name=tipo]').data('data',data);
			p.$w.find('[name=cboEspacio]').removeData('ocupantes');
			if(data.ocupantes!=null){
				if(data.ocupantes.length!=0){
					$.post('cm/ocup/all_ocu_pro','_id='+data.propietario._id.$id,function(ocupantes){
						p.$w.find('[name=cboEspacio]').data('ocupantes',ocupantes);
						var ocupantes = p.$w.find('[name=cboEspacio]').data('ocupantes');
						for(var i=0; i<ocupantes.length; i++){
							if(ocupantes[i].roles.ocupante.espacio._id.$id==data._id.$id){
								var ocup = ocupantes[i];
								if(ocup.roles.ocupante.difunto==false) var $row = p.$w.find('[name=divAsig] .gridReference').clone();
								else var $row = p.$w.find('[name=divDifu] .gridReference').clone();
								$row.find('li:eq(0)').html(i+1);
								if(ocup.tipo_enti=='P') $row.find('li:eq(1)').html(ocup.nomb+' '+ocup.appat+' '+ocup.apmat);
								else $row.find('li:eq(1)').html(ocup.nomb);
								if(ocup.roles.ocupante.difunto==false) p.$w.find('[name=divAsig] .gridBody').append( $row.children() );
								else p.$w.find("[name=divDifu] .gridBody").append( $row.children() );
								if(ocup.roles.ocupante.difunto==true) p.$w.find('table:eq(2)').show();
							}
						}
					},'json');
				}
			}
		};
		new K.Modal({
			id: 'windowAnulConce',
			title: 'Finalizar concesi&oacuten',
			icon: 'ui-icon-cancel',
			width: 700,
			height: 430,
			contentURL: 'cm/oper/anul_conce',
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data.observ = p.$w.find('[name=observ]').val();
					if(p.$w.find('[name=nomb]').data('data')==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un propietario!'});
					}
					data.propietario = ciHelper.enti.dbRel(p.$w.find('[name=nomb]').data('data'));
					var espacio = p.$w.find('[name=cboEspacio]').data('espacios')[p.$w.find('[name=cboEspacio]').find('option:selected').attr('_index')];
					if(espacio==null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un espacio!'});
					}
					data.espacio = new Object;
					data.espacio._id = espacio._id.$id;
					data.espacio.nomb = espacio.nomb;
					data.difuntos = new Array;
					data.asignados = new Array;
					var ocupantes = p.$w.find('[name=cboEspacio]').data('ocupantes');
					if(ocupantes!=null){
						for(var i=0; i<ocupantes.length; i++){
							var ocup = new Object;
							ocup._id = ocupantes[i]._id.$id;
							ocup.tipo_enti = ocupantes[i].tipo_enti;
							ocup.nomb = ocupantes[i].nomb;
							ocup.appat = ocupantes[i].appat;
							ocup.apmat = ocupantes[i].apmat;
							if(ocupantes[i].roles.ocupante.difunto==false) data.asignados.push(ocup);
							else data.difuntos.push(ocup);
						}
					}
					if(p.$w.find('table:eq(2)').css('display')!='none'){
						data.fecprog = p.$w.find('[name=fecprog]').val();
						if(data.fecprog==''){
							p.$w.find('[name=fecprog]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha para el traslado coactivo!'});
						}
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('cm/oper/save_anul_conce',data,function(){
						K.clearNoti();
						K.notification({title: 'Operaci&oacute;n ejecutada',text: 'La operaci&oacute;n se realiz&oacute; con &eacute;xito!'});
						K.closeWindow(p.$w.attr('id'));
						cmCuen.init();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowAnulConce');
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
				p.$w.find('[name=btnBusPro]').click(function(){
					/*ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbPropietario,filter: [
					    //{nomb: 'tipo_enti',value: 'P'},
					    {nomb: 'roles.propietario',value: {$exists: true}}
					]});*/
					mgEnti.windowSelect({callback: p.cbPropietario,filter: [
					    /*{nomb: 'tipo_enti',value: 'P'},*/
					    //{nomb: 'roles.propietario',value: {$exists: true}}
					]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('table:eq(1)').hide();
				p.$w.find('[name=fecprog]').datetimepicker();
				p.$w.find('[name=fecprog]').datepicker( "option", "dateFormat", 'yy-mm-dd' );
				p.$w.layout({
					resizeWithWindow:	false,
					west__size:			150,
					west__closable:		false,
					west__resizable:	false,
					west__slidable:		false
				});
				K.unblock({$element: p.$w});
			}
		});
	},
	executeOper: function(p){
		new K.Modal({
			id: 'windowExecuteOper'+p.id,
			title: 'Registrar ejecuci&oacute;n',
			contentURL: 'cm/oper/ejecutar',
			icon: 'ui-icon-clipboard',
			width: 420,
			height: 320,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data._id = p.id;
					if(p.$w.find('[name=inicio]').val()==''){
						p.$w.find('[name=inicio]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de inicio!',type: 'error'});
					}
					data.fecini = p.$w.find('[name=inicio]').val()+':00';
					if(p.$w.find('[name=fin]').val()==''){
						p.$w.find('[name=fin]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una fecha de fin!',type: 'error'});
					}
					data.fecfin = p.$w.find('[name=fin]').val()+':00';
					data.observ = p.$w.find('[name=observ]').val();
					if(p.data.traslado){
						data.ocupante = p.data.ocupante;
						data.ocupante._id = data.ocupante._id.$id;
						data.espacio = p.data.espacio;
						data.espacio._id = data.espacio._id.$id;
						data.propietario = p.data.propietario;
						data.propietario._id = data.propietario._id.$id;
						data.traslado = p.data.traslado;
						if(data.traslado.espacio_destino!=null){
							data.traslado.espacio_destino._id = data.traslado.espacio_destino._id.$id;
						}
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button:first').button('disable');
					$.post('cm/oper/save_ejecutar',data,function(){
						K.clearNoti();
						K.notification({title: 'Operaci&oacute;n ejecutada',text: 'La operaci&oacute;n se realiz&oacute; con &eacute;xito!'});
						K.closeWindow(p.$w.attr('id'));
						$('#mainPanel .gridBody').empty();
						cmOperAll.init();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowExecuteOper'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=inicio]').change(function(){
					p.$w.find('[name=fin]').val($(this).val());
				}).datetimepicker();
				p.$w.find('[name=fin]').datetimepicker();
				p.$w.find('[name=fin]').closest('tr').hide();
				$.post('cm/oper/get','id='+p.id,function(data){
					p.data = data;
					if(data.concesion != null)
						p.$w.find('[name=actividad]').html( 'Concesion' );
					else if(data.construccion != null)
						p.$w.find('[name=actividad]').html( 'Construcci&oacute;n' );
					else if(data.asignacion != null)
						p.$w.find('[name=actividad]').html( 'Asignaci&oacute;n' );
					else if(data.inhumacion != null)
						p.$w.find('[name=actividad]').html( 'Inhumaci&oacute;n' );
					else if(data.traslado != null)
						p.$w.find('[name=actividad]').html( 'Traslado' );
					else if(data.colocacion != null)
						p.$w.find('[name=actividad]').html( 'Colocaci&oacute;n' );
					else if(data.adjuntacion != null)
						p.$w.find('[name=actividad]').html( 'Adjuntaci&oacute;n' );
					if(data.ocupante!=null){
						if(data.ocupante.tipo_enti=='E') p.$w.find('[name=ocupante]').html( data.ocupante.nomb );
						else p.$w.find('[name=ocupante]').html( data.ocupante.nomb + ' '+ data.ocupante.appat+ ' '+data.ocupante.apmat );
					}else p.$w.find().remove();
					p.$w.find('[name=espacio]').html( data.espacio.nomb );
					p.$w.find('[name=fecha]').html( ciHelper.dateFormatLong(data.programacion.fecprog) );
					p.$w.find('[name=inicio]').val(ciHelper.date.format.bd_ymdhi(data.programacion.fecprog))
						.change();
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	executeCons: function(p){
		p.cbRec = function(data){
			p.$w.find('[name=recibido]').html(ciHelper.enti.formatName(data)).data('data',data);
			p.$w.find('[name=btnSelEnti]').button('option','text',false);
			p.$w.find('[name=btnAgrEnti]').button('option','text',false);
		};
		new K.Modal({
			id: 'windowExecuteCons'+p.id,
			title: 'Registrar Ejecuci&oacute;n de Construcci&oacute;n',
			contentURL: 'cm/oper/exe_cons',
			icon: 'ui-icon-clipboard',
			width: 480,
			height: 350,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = new Object;
					data._id = p.data._id.$id;
					data.espacio = p.data.espacio._id.$id;
					data.fecini = ciHelper.dateFormatBD(p.data.programacion.fecprog);
					data.observ = p.$w.find('[name=observ]').val();
					data.tipo = p.$w.find('[name=cboSelTipo] option:selected').val();
					data.capacidad = p.$w.find('[name=txtCantidad]').val();
					data.ancho = p.$w.find('[name=txtAncho]').val();
					data.largo = p.$w.find('[name=txtLargo]').val();
					if(data.capacidad == ""){
						p.$w.find('[name=txtCantidad]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar la capacidad!",type:"error"});
					}
					if(data.ancho == ""){
						p.$w.find('[name=txtAncho]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar el ancho!",type:"error"});
					}
					if(data.largo == ""){
						p.$w.find('[name=txtLargo]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar el largo!",type:"error"});
					}
					data.altura1 = p.$w.find('[name=txtAltura1]').val();
					data.altura2 = p.$w.find('[name=txtAltura2]').val();
					if(p.$w.find('[name=recibido]').data('data')==null){
						p.$w.find('[name=btnSelEnti]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe seleccionar una entidad!",type:"error"});
					}else
						data.recibido = ciHelper.enti.dbRel(p.$w.find('[name=recibido]').data('data'));
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('cm/oper/save_exe_cons',data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La operaci&oacute;n se realiz&oacute; con &eacute;xito!'});
						K.closeWindow(p.$w.attr('id'));
						$('#mainPanel .gridBody').empty();
						cmOperPro.loadData({page: 1,url: 'cm/oper/listaprog',fecbus:$('.divSearch [name=buscar]').val()});
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowExecuteCons'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=nomb]').html( p.data.propietario.nomb );
				p.$w.find('[name=apell]').html( p.data.propietario.appat+' '+p.data.propietario.apmat );
				p.$w.find('[name=espacio]').html( p.data.espacio.nomb );
				p.$w.find('[name=btnSelEnti]').click(function(){
					//ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbRec});
					mgEnti.windowSelect({callback: p.cbRec});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnAgrEnti]').remove();/*.click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbRec});
				}).button({icons: {primary: 'ui-icon-plusthick'}});*/
				K.unblock({$element: p.$w});
			}
		});
	},
	executeAmp: function(p){
		p.cbRec = function(data){
			p.$w.find('[name=recibido]').html(ciHelper.enti.formatName(data)).data('data',data);
			p.$w.find('[name=btnSelEnti]').button('option','text',false);
			p.$w.find('[name=btnAgrEnti]').button('option','text',false);
		};
		new K.Modal({
			id: 'windowExecuteAmp'+p.id,
			title: 'Registrar Ejecuci&oacute;n de Ampliaci&oacute;n',
			contentURL: 'cm/oper/exe_cons',
			icon: 'ui-icon-clipboard',
			width: 480,
			height: 350,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.data._id.$id,
						espacio: p.data.espacio._id.$id,
						fecini: ciHelper.dateFormatBD(p.data.programacion.fecprog),
						observ: p.$w.find('[name=observ]').val(),
						tipo: p.$w.find('[name=cboSelTipo] option:selected').val(),
						capacidad: p.$w.find('[name=txtCantidad]').val(),
						ancho: p.$w.find('[name=txtAncho]').val(),
						largo: p.$w.find('[name=txtLargo]').val()
					};
					if(data.capacidad == ""){
						p.$w.find('[name=txtCantidad]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar la capacidad!",type:"error"});
					}
					if(data.ancho == ""){
						p.$w.find('[name=txtAncho]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar el ancho!",type:"error"});
					}
					if(data.largo == ""){
						p.$w.find('[name=txtLargo]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe ingresar el largo!",type:"error"});
					}
					data.altura1 = p.$w.find('[name=txtAltura1]').val();
					data.altura2 = p.$w.find('[name=txtAltura2]').val();
					if(p.$w.find('[name=recibido]').data('data')==null){
						p.$w.find('[name=btnSelEnti]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Debe seleccionar una entidad!",type:"error"});
					}else
						data.recibido = ciHelper.enti.dbRel(p.$w.find('[name=recibido]').data('data'));
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('cm/oper/save_exe_amp',data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'La operaci&oacute;n se realiz&oacute; con &eacute;xito!'});
						K.closeWindow(p.$w.attr('id'));
						$('#mainPanel .gridBody').empty();
						cmOperPro.loadData({page: 1,url: 'cm/oper/listaprog',fecbus:$('.divSearch [name=buscar]').val()});
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowExecuteAmp'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=nomb]').html( p.data.propietario.nomb );
				p.$w.find('[name=apell]').html( p.data.propietario.appat+' '+p.data.propietario.apmat );
				p.$w.find('[name=espacio]').html( p.data.espacio.nomb );
				p.$w.find('[name=btnSelEnti]').click(function(){
					//ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbRec});
					mgEnti.windowSelect({callback: p.cbRec});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnAgrEnti]').remove();/*.click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbRec});
				}).button({icons: {primary: 'ui-icon-plusthick'}});*/
				K.unblock({$element: p.$w});
			}
		});
	},
	windowDetailsInhu: function(p){
//		K.notification({text:p.id});
		$.post('cm/oper/get','id='+p.id,function(data){
			var params = {
					id: 'windowDetailInhu'+p.id,
					contentURL: 'cm/oper/consult',
					title: 'Detalle de Operación',
					icon: 'ui-icon-person',
					minimizable : false,
					width: 820,
					height: 300,
					buttons: {
						"Agregar Informacion": function(){
							cmOper.windowEditInhu({id: p.id});
						},
						"Cerrar": function(){
							$(this).dialog('close');
						}
					},
					onContentLoaded: function(){
						$modal = $('#windowDetailInhu'+p.id);
						K.block({$element: $modal});
						$modal.find('[name=principal]').append("<tr><td><label>Operaci&oacute;n</label></td><td><b>Inhumaci&oacute;n</b></td></tr>");
						if(data.propietario.appat != null) $modal.find('[name=principal]').append("<tr><td><label>Propietario</label></td><td><a name='propietario'>"+data.propietario.nomb+" "+data.propietario.appat+" "+data.propietario.apmat+"</a></td></tr>");			
						else $modal.find('[name=principal]').append("<tr><td><label>Propietario</label></td><td><a name='propietario'>"+data.propietario.nomb+"</a></td></tr>");	
						$modal.find('[name=principal]').append("<tr><td><label>Ocupante</label></td><td><a name='ocupante'>"+data.ocupante.nomb+" "+data.ocupante.appat+" "+data.ocupante.apmat+"</a></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Espacio</label></td><td><a name='espacio'>"+data.espacio.nomb+"</a></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Defunci&oacute;n</label></td><td>"+ciHelper.dateFormatLong(data.inhumacion.fecdef)+"</td></tr>");
						if(data.inhumacion.puerta!=null)
							$modal.find('[name=principal]').append("<tr><td><label>Puerta de Ingreso</label></td><td>"+data.inhumacion.puerta+"</td></tr>");
						if(data.inhumacion.partdef!=null)
							$modal.find('[name=principal]').append("<tr><td><label>N° Partida</label></td><td>"+data.inhumacion.partdef+"</td></tr>");
						else if(data.inhumacion.folio!=null)
							$modal.find('[name=principal]').append("<tr><td><label>N° Partida</label></td><td>"+data.inhumacion.folio+"</td></tr>");
						if(data.inhumacion.municipalidad!=null)
							$modal.find('[name=principal]').append("<tr><td><label>Municipalidad</label></td><td><a name='muni'>"+data.inhumacion.municipalidad.nomb+"</a></td></tr>");
						if(data.inhumacion.funeraria!=null) $modal.find('[name=principal]').append("<tr><td><label>Funeraria</label></td><td><a name='fune'>"+data.inhumacion.funeraria.nomb+"</a></td></tr>");
						if(data.inhumacion.fecdef!='')
							$modal.find('[name=principal]').append("<tr><td><label>Fecha de Defunci&oacute;n</label></td><td>"+ciHelper.dateFormat(data.inhumacion.fecdef)+"</td></tr>");
						if(data.inhumacion.edad!='')
							$modal.find('[name=principal]').append("<tr><td><label>Edad</label></td><td>"+data.inhumacion.edad+"</td></tr>");
						if(data.inhumacion.causa!='')
							$modal.find('[name=principal]').append("<tr><td><label>Causa</label></td><td>"+data.inhumacion.causa+"</td></tr>");
						
						$modal.find('[name=detalle]').append("<tr><td><label>Registrado</label></td><td>"+ciHelper.dateFormatLong(data.fecreg)+"</td></tr>");
						if(data.trabajador.appat != null) $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='trab'>"+data.trabajador.nomb+" "+data.trabajador.appat+" "+data.trabajador.apmat+"</a></td></tr>");			
						else $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='trab'>"+data.trabajador.nomb+"</a></td></tr>");	
						if(data.programacion){
							$modal.find('[name=detalle]').append("<tr><td><b><label>Programaci&oacute;n</label><b></td><td><hr></td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Programado</label></td><td>"+ciHelper.dateFormatLong(data.programacion.fecprog)+"</td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Observaciones</label></td><td>"+data.programacion.observ+"</td></tr>");
						}
						if(data.ejecucion){
							$modal.find('[name=detalle]').append("<tr><td><b><label>Ejecuci&oacute;n</label><b></td><td><hr></td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Fecha de Inicio</label></td><td>"+ciHelper.dateFormatLong(data.ejecucion.fecini)+"</td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Fecha de Fin</label></td><td>"+ciHelper.dateFormatLong(data.ejecucion.fecfin)+"</td></tr>");
							if(data.ejecucion.trabajador.appat != null)	$modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='ejec_trab'>"+data.ejecucion.trabajador.nomb+" "+data.ejecucion.trabajador.appat+" "+data.ejecucion.trabajador.apmat +"</a></td></tr>");
							else $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a ref='#' name='ejec_trab'>"+data.ejecucion.trabajador.nomb+"</a></td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Observaciones</label></td><td>"+data.ejecucion.observ+"</td></tr>");
							$modal.find("[name=ejec_trab]").click(function(){
								ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
							}).data('id',data.ejecucion.trabajador._id.$id).data('tipo_enti',data.ejecucion.trabajador.tipo_enti);
						}
						if(data.url_imagen!=null){
							var url = data.url_imagen;
							var displayText = "Ver imagen"; // La palabra que se mostrará como enlace
							$modal.find('[name=detalle]').append("<tr><td><label>Digitalizado</label></td><td><a href='" + url + "' target='_blank'>" + displayText + "</a></td></tr>");
						} else {
							$modal.find('[name=detalle]').append("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
						}
						
						if(data.recibos!=null){
							$modal.find('[name=comprobante]').append("<tr><td><b><label>Comprobantes</label><b></td><td><hr></td></tr>");
							url = 'cj/comp/print_reci?id=';
							for(var i=0;i<data.recibos.length;i++){
								comprobante = data.recibos[0];
								$modal.find('[name=comprobante]').append("<tr><td><label>Comprobante Nro: </label></td><td><a href='" + url+comprobante._id.$id + "' target='_blank'>" + comprobante.serie+"-"+comprobante.num + "</a></td></tr>");
							}
						}
						K.unblock({$element: $modal});
						$modal.find("[name=propietario]").click(function(){
							 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.propietario._id.$id).data('tipo_enti',data.propietario.tipo_enti);
						$modal.find("[name=ocupante]").click(function(){
							 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.ocupante._id.$id).data('tipo_enti',data.ocupante.tipo_enti);
						$modal.find("[name=espacio]").click(function(){
							cmEspa.showDetailsEspa({id: $(this).data('id')});
						}).data('id',data.espacio._id.$id);
						if(data.inhumacion.municipalidad!=null) $modal.find("[name=muni]").click(function(){
							 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.inhumacion.municipalidad._id.$id).data('tipo_enti',data.inhumacion.municipalidad.tipo_enti);
						if(data.inhumacion.funeraria!=null) $modal.find("[name=fune]").click(function(){
							ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.inhumacion.funeraria._id.$id).data('tipo_enti',data.inhumacion.funeraria.tipo_enti);
						$modal.find("[name=trab]").click(function(){
							ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.trabajador._id.$id).data('tipo_enti',data.trabajador.tipo_enti);
						
					}
				};
				if(p.modal!=null){
					params.header = false;
					new K.Modal(params);
				}else new K.Window(params);
		},"json");
	},
	windowDetailsAsig: function(p){
//		K.notification({text:p.id});
		$.post('cm/oper/get','id='+p.id,function(data){
			var params = {
					id: 'windowDetailAsig'+p.id,
					contentURL: 'cm/oper/consult',
					title: 'Detalle de Operación',
					icon: 'ui-icon-tag',
					minimizable : false,
					width: 820,
					height: 300,
					buttons: {
						"Cerrar": function(){
							$(this).dialog('close');
						}
					},
					onContentLoaded: function(){
						$modal = $('#windowDetailAsig'+p.id);
						K.block({$element: $modal});
						$modal.find('[name=principal]').append("<tr><td><label>Operaci&oacute;n</label></td><td><b>Asignaci&oacute;n</b></td></tr>");
						if(data.propietario.appat != null)$modal.find('[name=principal]').append("<tr><td><label>Propietario</label></td><td><a name='propietario'>"+data.propietario.nomb+" "+data.propietario.appat+" "+data.propietario.apmat+"</a></td></tr>");
						else $modal.find('[name=principal]').append("<tr><td><label>Propietario</label></td><td><a name='propietario'>"+data.propietario.nomb+"</a></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Ocupante</label></td><td><a name='ocupante'>"+data.ocupante.nomb+" "+data.ocupante.appat+" "+data.ocupante.apmat+"</a></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Espacio</label></td><td><a name='espacio'>"+data.espacio.nomb+"</a></td></tr>");
						
						$modal.find('[name=detalle]').append("<tr><td><label>Registrado</label></td><td>"+ciHelper.dateFormatLong(data.fecreg)+"</td></tr>");
						if(data.trabajador.appat != null) $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='trab'>"+data.trabajador.nomb+" "+data.trabajador.appat+" "+data.trabajador.apmat+"</a></td></tr>");			
						else $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='trab'>"+data.trabajador.nomb+"</a></td></tr>");	
						if(data.anulacion) {
							$modal.find('[name=detalle]').append("<tr><td><b><label>Anulaci&oacute;n</label><b></td><td>"+ciHelper.dateFormatLong(data.anulacion.fecanl)+"</td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><b><label>Observaciones</label><b></td><td>"+data.anulacion.observ+"</td></tr>");
						}
						if(data.observ!=null)
							$modal.find('[name=detalle]').append("<tr><td><label>Observaciones</label></td><td>"+data.observ+"</td></tr>");
						if(data.url_imagen!=null){
							var url = data.url_imagen;
							var displayText = "Ver imagen"; // La palabra que se mostrará como enlace
							$modal.find('[name=detalle]').append("<tr><td><label>Digitalizado</label></td><td><a href='" + url + "' target='_blank'>" + displayText + "</a></td></tr>");
						} else {
							$modal.find('[name=detalle]').append("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
						}
						if(data.recibos!=null){
							$modal.find('[name=comprobante]').append("<tr><td><b><label>Comprobantes</label><b></td><td><hr></td></tr>");
							url = 'cj/comp/print_reci?id=';
							for(var i=0;i<data.recibos.length;i++){
								comprobante = data.recibos[0];
								$modal.find('[name=comprobante]').append("<tr><td><label>Comprobante Nro: </label></td><td><a href='" + url+comprobante._id.$id + "' target='_blank'>" + comprobante.serie+"-"+comprobante.num + "</a></td></tr>");
							}
						}
						K.unblock({$element: $modal});
						$modal.find("[name=propietario]").click(function(){
							 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.propietario._id.$id).data('tipo_enti',data.propietario.tipo_enti);
						$modal.find("[name=ocupante]").click(function(){
							 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.ocupante._id.$id).data('tipo_enti',data.ocupante.tipo_enti);
						$modal.find("[name=trab]").click(function(){
							ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.trabajador._id.$id).data('tipo_enti',data.trabajador.tipo_enti);
						$modal.find("[name=espacio]").click(function(){
							cmEspa.showDetailsEspa({id: $(this).data('id')});
						}).data('id',data.espacio._id.$id);
					}
				};
				if(p.modal!=null){
					params.header = false;
					new K.Modal(params);
				}else new K.Window(params);
		},"json");
	},
	windowDetailsConc: function(p){
//		K.notification({text:p.id});
		$.post('cm/oper/get',{id: p.id,servicio: true},function(data){
			var params = {
					id: 'windowDetailConc'+p.id,
					contentURL: 'cm/oper/consult',
					title: 'Detalle de Operación',
					icon: 'ui-icon-bookmark',
					minimizable : false,
					width: 820,
					height: 300,
					buttons:{
						"Cerrar": function(){
							$(this).dialog('close');
						}
					},
					onContentLoaded: function(){
						$modal = $('#windowDetailConc'+p.id);
						K.block({$element: $modal});
						var servicio = "";
						if(data.cuentas_cobrar!=null){
							if(data.cuentas_cobrar.servicio!=null){
								servicio = " ("+data.cuentas_cobrar.servicio.nomb+")";
							}
						}
						$modal.find('[name=principal]').append("<tr><td><label>Operaci&oacute;n</label></td><td><b>Concesi&oacute;n"+servicio+"</b></td></tr>");
						if(data.propietario.appat != null)$modal.find('[name=principal]').append("<tr><td><label>Propietario</label></td><td><a name='propietario'>"+data.propietario.nomb+" "+data.propietario.appat+" "+data.propietario.apmat+"</a></td></tr>");
						else $modal.find('[name=principal]').append("<tr><td><label>Propietario</label></td><td><a name='propietario'>"+data.propietario.nomb+"</a></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Espacio</label></td><td><a name='espacio'>"+data.espacio.nomb+"</a></td></tr>");
						if(data.concesion.condicion=="T"){ 
							$modal.find('[name=principal]').append("<tr><td><label>Condici&oacute;n</label></td><td>Temporal</td></tr>");
							$modal.find('[name=principal]').append("<tr><td><label>Vencimiento</label></td><td>"+ciHelper.dateFormatLong(data.concesion.fecven)+"</td></tr>");
						}
						else $modal.find('[name=principal]').append("<tr><td><label>Condici&oacute;n</label></td><td>Permanente</td></tr>");
											
						$modal.find('[name=detalle]').append("<tr><td><label>Registrado</label></td><td>"+ciHelper.dateFormatLong(data.fecreg)+"</td></tr>");
						if(data.trabajador.appat != null) $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='trab'>"+data.trabajador.nomb+" "+data.trabajador.appat+" "+data.trabajador.apmat+"</a></td></tr>");			
						else $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='trab'>"+data.trabajador.nomb+"</a></td></tr>");	
						if(data.anulacion) {
							$modal.find('[name=detalle]').append("<tr><td><b><label>Anulaci&oacute;n</label><b></td><td>"+ciHelper.dateFormatLong(data.anulacion.fecanl)+"</td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><b><label>Observaciones</label><b></td><td>"+data.anulacion.observ+"</td></tr>");
						}
						if(data.observ!=null)
							$modal.find('[name=detalle]').append("<tr><td><label>Observaciones</label></td><td>"+data.observ+"</td></tr>");
							if(data.url_imagen!=null){
								var url = data.url_imagen;
								var displayText = "Ver imagen"; // La palabra que se mostrará como enlace
								$modal.find('[name=detalle]').append("<tr><td><label>Digitalizado</label></td><td><a href='" + url + "' target='_blank'>" + displayText + "</a></td></tr>");
							} else {
								$modal.find('[name=detalle]').append("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
							}
						if(data.recibos!=null){
								$modal.find('[name=comprobante]').append("<tr><td><b><label>Comprobantes</label><b></td><td><hr></td></tr>");
								url = 'cj/comp/print_reci?id=';
								for(var i=0;i<data.recibos.length;i++){
									comprobante = data.recibos[0];
									$modal.find('[name=comprobante]').append("<tr><td><label>Comprobante Nro: </label></td><td><a href='" + url+comprobante._id.$id + "' target='_blank'>" + comprobante.serie+"-"+comprobante.num + "</a></td></tr>");
								}
						}
						K.unblock({$element: $modal});
						$modal.find("[name=propietario]").click(function(){
							 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.propietario._id.$id).data('tipo_enti',data.propietario.tipo_enti);
						$modal.find("[name=trab]").click(function(){
							ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.trabajador._id.$id).data('tipo_enti',data.trabajador.tipo_enti);
						$modal.find("[name=espacio]").click(function(){
							cmEspa.showDetailsEspa({id: $(this).data('id')});
						}).data('id',data.espacio._id.$id);
					}
				};
				if(p.modal!=null){
					params.header = false;
					new K.Modal(params);
				}else new K.Window(params);
		},"json");
	},
	windowDetailsConver: function(p){
//		K.notification({text:p.id});
		$.post('cm/oper/get',{id:p.id},function(data){
			var params = {
					id: 'windowDetailConv'+p.id,
					contentURL: 'cm/oper/consult',
					title: 'Detalle de Conversi&oacute;n',
					icon: 'ui-icon-bookmark',
					minimizable : false,
					width: 820,
					height: 300,
					buttons:{
						"Cerrar": function(){
							$(this).dialog('close');
						}
					},
					onContentLoaded: function(){
						$modal = $('#windowDetailConv'+p.id);
						K.block({$element: $modal});
						$modal.find('[name=principal]').append("<tr><td><label>Operaci&oacute;n</label></td><td><b>Conversi&oacute;n</b></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Propietario</label></td><td><a name='propietario'>"+ciHelper.enti.formatName(data.propietario)+"</a></td></tr>");
						if(data.conversion.propietario_nuevo!=null){
							$modal.find('[name=principal]').append("<tr><td><label>Nuevo Propietario</label></td><td><a name='propietario_n'>"+ciHelper.enti.formatName(data.conversion.propietario_nuevo)+"</a></td></tr>");
							$modal.find("[name=propietario_n]").click(function(){
								 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
							}).data('id',data.conversion.propietario_nuevo._id.$id).data('tipo_enti',data.conversion.propietario_nuevo.tipo_enti);
						}
						$modal.find('[name=principal]').append("<tr><td><label>Espacio</label></td><td><a name='espacio'>"+data.espacio.nomb+"</a></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Condici&oacute;n</label></td><td>Permanente</td></tr>");
											
						$modal.find('[name=detalle]').append("<tr><td><label>Registrado</label></td><td>"+ciHelper.dateFormatLong(data.fecreg)+"</td></tr>");
						$modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='trab'>"+ciHelper.enti.formatName(data.trabajador)+"</a></td></tr>");	
						if(data.anulacion) {
							$modal.find('[name=detalle]').append("<tr><td><b><label>Anulaci&oacute;n</label><b></td><td>"+ciHelper.dateFormatLong(data.anulacion.fecanl)+"</td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><b><label>Observaciones</label><b></td><td>"+data.anulacion.observ+"</td></tr>");
						}
						if(data.url_imagen!=null){
							var url = data.url_imagen;
							var displayText = "Ver imagen"; // La palabra que se mostrará como enlace
							$modal.find('[name=detalle]').append("<tr><td><label>Digitalizado</label></td><td><a href='" + url + "' target='_blank'>" + displayText + "</a></td></tr>");
						} else {
							$modal.find('[name=detalle]').append("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
						}
						if(data.recibos!=null){
							$modal.find('[name=comprobante]').append("<tr><td><b><label>Comprobantes</label><b></td><td><hr></td></tr>");
							url = 'cj/comp/print_reci?id=';
							for(var i=0;i<data.recibos.length;i++){
								comprobante = data.recibos[0];
								$modal.find('[name=comprobante]').append("<tr><td><label>Comprobante Nro: </label></td><td><a href='" + url+comprobante._id.$id + "' target='_blank'>" + comprobante.serie+"-"+comprobante.num + "</a></td></tr>");
							}
						}
						K.unblock({$element: $modal});
						$modal.find("[name=propietario]").click(function(){
							 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.propietario._id.$id).data('tipo_enti',data.propietario.tipo_enti);
						$modal.find("[name=trab]").click(function(){
							ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.trabajador._id.$id).data('tipo_enti',data.trabajador.tipo_enti);
						$modal.find("[name=espacio]").click(function(){
							cmEspa.showDetailsEspa({id: $(this).data('id')});
						}).data('id',data.espacio._id.$id);
					}
				};
				if(p.modal!=null){
					params.header = false;
					new K.Modal(params);
				}else new K.Window(params);
		},"json");
	},
	windowDetailsTras: function(p){
//		K.notification({text:p.id});
		$.post('cm/oper/get','id='+p.id,function(data){
			var params = {
					id: 'windowDetailTras'+p.id,
					contentURL: 'cm/oper/consult',
					title: 'Detalle de Operación',
					icon: 'ui-icon-arrow-2-e-w',
					minimizable : false,
					width: 820,
					height: 300,
					buttons: {
						"Cerrar": function(){
							$(this).dialog('close');
						}
					},
					onContentLoaded: function(){
						$modal = $('#windowDetailTras'+p.id);
						K.block({$element: $modal});
						$modal.find('[name=principal]').append("<tr><td><label>Operaci&oacute;n</label></td><td><b>Traslado</b></td></tr>");
						if(data.propietario.appat != null)$modal.find('[name=principal]').append("<tr><td><label>Propietario</label></td><td><a name='propietario'>"+data.propietario.nomb+" "+data.propietario.appat+" "+data.propietario.apmat+"</a></td></tr>");
						else $modal.find('[name=principal]').append("<tr><td><label>Propietario</label></td><td><a name='propietario'>"+data.propietario.nomb+"</a></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Ocupante</label></td><td><a name='ocupante'>"+data.ocupante.nomb+" "+data.ocupante.appat+" "+data.ocupante.apmat+"</a></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Espacio</label></td><td><a name='espacio'>"+data.espacio.nomb+"</a></td></tr>");
						if(data.traslado.cementerio) {
							$modal.find('[name=principal]').append("<tr><td><label>Cementerio</label></td><td><a name='cem'>"+data.traslado.cementerio.nomb+"</a></td></tr>");
							$modal.find('[name=principal]').append("<tr><td><label>Ubicaci&oacute;n</label></td><td>"+data.traslado.ubicacion+"</td></tr>");
							$("#cem").click(function(){
								 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
							}).data('id',data.traslado.cementerio._id.$id).data('tipo_enti',data.traslado.cementerio.tipo_enti);
						}
						else {
							$modal.find('[name=principal]').append("<tr><td><label>Espacio de Destino</label></td><td><a name='espacio_des'>"+data.traslado.espacio_destino.nomb+"</a></td></tr>");
							$("[name=espacio_des]").click(function(){
								cmEspa.showDetailsEspa({id: $(this).data('id')});
							}).data('id',data.traslado.espacio_destino._id.$id);
						}				
						$modal.find('[name=detalle]').append("<tr><td><label>Registrado</label></td><td>"+ciHelper.dateFormatLong(data.fecreg)+"</td></tr>");
						if(data.trabajador.appat != null) $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='trab'>"+data.trabajador.nomb+" "+data.trabajador.appat+" "+data.trabajador.apmat+"</a></td></tr>");			
						else $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='trab'>"+data.trabajador.nomb+"</a></td></tr>");	
						if(data.programacion){
							$modal.find('[name=detalle]').append("<tr><td><b><label>Programaci&oacute;n</label><b></td><td><hr></td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Programado</label></td><td>"+ciHelper.dateFormatLong(data.programacion.fecprog)+"</td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Observaciones</label></td><td>"+data.programacion.observ+"</td></tr>");
						}
						if(data.ejecucion){
							$modal.find('[name=detalle]').append("<tr><td><b><label>Ejecuci&oacute;n</label><b></td><td><hr></td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Fecha de Inicio</label></td><td>"+ciHelper.dateFormatLong(data.ejecucion.fecini)+"</td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Fecha de Fin</label></td><td>"+ciHelper.dateFormatLong(data.ejecucion.fecfin)+"</td></tr>");
							if(data.ejecucion.trabajador.appat != null)	$modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='ejec_trab'>"+data.ejecucion.trabajador.nomb+" "+data.ejecucion.trabajador.appat+" "+data.ejecucion.trabajador.apmat +"</a></td></tr>");
							else $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='ejec_trab'>"+data.ejecucion.trabajador.nomb+"</a></td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Observaciones</label></td><td>"+data.ejecucion.observ+"</td></tr>");
							$("#ejec_trab").click(function(){
								ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
							}).data('id',data.ejecucion.trabajador._id.$id).data('tipo_enti',data.ejecucion.trabajador.tipo_enti);
						}
						if(data.url_imagen!=null){
							var url = data.url_imagen;
							var displayText = "Ver imagen"; // La palabra que se mostrará como enlace
							$modal.find('[name=detalle]').append("<tr><td><label>Digitalizado</label></td><td><a href='" + url + "' target='_blank'>" + displayText + "</a></td></tr>");
						} else {
							$modal.find('[name=detalle]').append("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
						}
						if(data.recibos!=null){
							$modal.find('[name=comprobante]').append("<tr><td><b><label>Comprobantes</label><b></td><td><hr></td></tr>");
							url = 'cj/comp/print_reci?id=';
							for(var i=0;i<data.recibos.length;i++){
								comprobante = data.recibos[0];
								$modal.find('[name=comprobante]').append("<tr><td><label>Comprobante Nro: </label></td><td><a href='" + url+comprobante._id.$id + "' target='_blank'>" + comprobante.serie+"-"+comprobante.num + "</a></td></tr>");
							}
						}
						K.unblock({$element: $modal});
						$modal.find("[name=propietario]").click(function(){
							 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.propietario._id.$id).data('tipo_enti',data.propietario.tipo_enti);
						$modal.find("[name=ocupante]").click(function(){
							 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.ocupante._id.$id).data('tipo_enti',data.ocupante.tipo_enti);					
						$modal.find("[name=trab]").click(function(){
							ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.trabajador._id.$id).data('tipo_enti',data.trabajador.tipo_enti);
						$modal.find("[name=espacio]").click(function(){
							cmEspa.showDetailsEspa({id: $(this).data('id')});
						}).data('id',data.espacio._id.$id);
					}
				};
				if(p.modal!=null){
					params.header = false;
					new K.Modal(params);
				}else new K.Window(params);
		},"json");
	},
	windowDetailsTrasp: function(p){
//		K.notification({text:p.id});
		$.post('cm/oper/get','id='+p.id,function(data){
			var params = {
					id: 'windowDetailTrasp'+p.id,
					contentURL: 'cm/oper/consult',
					title: 'Detalle de Operación',
					icon: 'ui-icon-extlink',
					minimizable : false,
					width: 820,
					height: 300,
					buttons: {
						"Cerrar": function(){
							$(this).dialog('close');
						}
					},
					onContentLoaded: function(){
						$modal = $('#windowDetailTrasp'+p.id);
						K.block({$element: $modal});
						$modal.find('[name=principal]').append("<tr><td><label>Operaci&oacute;n</label></td><td><b>Traspaso</b></td></tr>");
						if(data.propietario.appat != null)$modal.find('[name=principal]').append("<tr><td><label>Propietario</label></td><td><a name='propietario'>"+data.propietario.nomb+" "+data.propietario.appat+" "+data.propietario.apmat+"</a></td></tr>");
						else $modal.find('[name=principal]').append("<tr><td><label>Propietario</label></td><td><a name='propietario'>"+data.propietario.nomb+"</a></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Espacio</label></td><td><a name='espacio'>"+data.espacio.nomb+"</a></td></tr>");
						if(data.traspaso.nuevo_propietario.appat != null)	$modal.find('[name=principal]').append("<tr><td><label>Nuevo Propietario</label></td><td><a name='new_prop'>"+data.traspaso.nuevo_propietario.nomb+" "+data.traspaso.nuevo_propietario.appat+" "+data.traspaso.nuevo_propietario.apmat+"</a></td></tr>");
						else $modal.find('[name=principal]').append("<tr><td><label>Nuevo Propietario</label></td><td><a name='new_prop'>"+data.traspaso.nuevo_propietario.nomb+"</a></td></tr>");
						
						$modal.find('[name=detalle]').append("<tr><td><label>Registrado</label></td><td>"+ciHelper.dateFormatLong(data.fecreg)+"</td></tr>");
						if(data.trabajador.appat != null) $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='trab'>"+data.trabajador.nomb+" "+data.trabajador.appat+" "+data.trabajador.apmat+"</a></td></tr>");			
						else $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='trab'>"+data.trabajador.nomb+"</a></td></tr>");	
						if(data.url_imagen!=null){
							var url = data.url_imagen;
							var displayText = "Ver imagen"; // La palabra que se mostrará como enlace
							$modal.find('[name=detalle]').append("<tr><td><label>Digitalizado</label></td><td><a href='" + url + "' target='_blank'>" + displayText + "</a></td></tr>");
						} else {
							$modal.find('[name=detalle]').append("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
						}
						if(data.recibos!=null){
							$modal.find('[name=comprobante]').append("<tr><td><b><label>Comprobantes</label><b></td><td><hr></td></tr>");
							url = 'cj/comp/print_reci?id=';
							for(var i=0;i<data.recibos.length;i++){
								comprobante = data.recibos[0];
								$modal.find('[name=comprobante]').append("<tr><td><label>Comprobante Nro: </label></td><td><a href='" + url+comprobante._id.$id + "' target='_blank'>" + comprobante.serie+"-"+comprobante.num + "</a></td></tr>");
							}
						}
						K.unblock({$element: $modal});
						$modal.find("[name=propietario]").click(function(){
							 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.propietario._id.$id).data('tipo_enti',data.propietario.tipo_enti);
						$modal.find("[name=new_prop]").click(function(){
							ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.traspaso.nuevo_propietario._id.$id).data('tipo_enti',data.traspaso.nuevo_propietario.tipo_enti);
						$modal.find("[name=trab]").click(function(){
							ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.trabajador._id.$id).data('tipo_enti',data.trabajador.tipo_enti);
						$modal.find("[name=espacio]").click(function(){
							cmEspa.showDetailsEspa({id: $(this).data('id')});
						}).data('id',data.espacio._id.$id);
					}
				};
				if(p.modal!=null){
					params.header = false;
					new K.Modal(params);
				}else new K.Window(params);
		},"json");
	},
	windowDetailsColo: function(p){
//		K.notification({text:p.id});
		$.post('cm/oper/get','id='+p.id,function(data){
			var params = {
					id: 'windowDetailColo'+p.id,
					contentURL: 'cm/oper/consult',
					title: 'Detalle de Operaci&oacute;n',
					icon: 'ui-icon-copy',
					minimizable : false,
					width: 820,
					height: 300,
					buttons: {
						"Cerrar": function(){
							$(this).dialog('close');
						}
					},
					onContentLoaded: function(){
						$modal = $('#windowDetailColo'+p.id);
						K.block({$element: $modal});
						$modal.find('[name=principal]').append("<tr><td><label>Operaci&oacute;n</label></td><td><b>Colocaci&oacute;n</b></td></tr>");
						if(data.propietario.appat != null)$modal.find('[name=principal]').append("<tr><td><label>Propietario</label></td><td><a name='propietario'>"+data.propietario.nomb+" "+data.propietario.appat+" "+data.propietario.apmat+"</a></td></tr>");
						else $modal.find('[name=principal]').append("<tr><td><label>Propietario</label></td><td><a name='propietario'>"+data.propietario.nomb+"</a></td></tr>");
						if(data.espacio.ocupantes!=null){
							$modal.find('[name=principal]').append("<tr><td><label>Ocupante</label></td><td name='ocups'></td></tr>");
							for(var i=0,j=data.espacio.ocupantes.length; i<j; i++){
								$modal.find('[name=ocups]').append("<a>"+ciHelper.enti.formatName(data.espacio.ocupantes[i])+"</a>,&nbsp;");
								$modal.find("[name=ocups] a:last").click(function(){
									 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
								}).data('id',data.espacio.ocupantes[i]._id.$id).data('tipo_enti',data.espacio.ocupantes[i].tipo_enti);
							}
						}
						$modal.find('[name=principal]').append("<tr><td><label>Espacio</label></td><td><a name='espacio'>"+data.espacio.nomb+"</a></td></tr>");
						var accesorios=''; 
						for (acs in data.colocacion.accesorios){
							accesorios += data.colocacion.accesorios[acs].nomb+"<br>";
						}
						$modal.find('[name=principal]').append("<tr><td><label>Accesorios</label></td><td>"+accesorios+"</td></tr>");
											
						$modal.find('[name=detalle]').append("<tr><td><label>Registrado</label></td><td>"+ciHelper.dateFormatLong(data.fecreg)+"</td></tr>");
						if(data.trabajador.appat != null) $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='trab'>"+data.trabajador.nomb+" "+data.trabajador.appat+" "+data.trabajador.apmat+"</a></td></tr>");			
						else $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='trab'>"+data.trabajador.nomb+"</a></td></tr>");	
						if(data.programacion){
							$modal.find('[name=detalle]').append("<tr><td><b><label>Programaci&oacute;n</label><b></td><td><hr></td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Programado</label></td><td>"+ciHelper.dateFormatLong(data.programacion.fecprog)+"</td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Observaciones</label></td><td>"+data.programacion.observ+"</td></tr>");
						}
						if(data.ejecucion){
							$modal.find('[name=detalle]').append("<tr><td><b><label>Ejecuci&oacute;n</label><b></td><td><hr></td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Fecha de Inicio</label></td><td>"+ciHelper.dateFormatLong(data.ejecucion.fecini)+"</td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Fecha de Fin</label></td><td>"+ciHelper.dateFormatLong(data.ejecucion.fecfin)+"</td></tr>");
							if(data.ejecucion.trabajador.appat != null)
								$modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td>"+data.ejecucion.trabajador.nomb+" "+data.ejecucion.trabajador.appat+" "+data.ejecucion.trabajador.apmat +"</td></tr>");
							else
								$modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td>"+data.ejecucion.trabajador.nomb+"</td></tr>");
							
							$modal.find('[name=detalle]').append("<tr><td><label>Observaciones</label></td><td>"+data.ejecucion.observ+"</td></tr>");
						}
						if(data.url_imagen!=null){
							var url = data.url_imagen;
							var displayText = "Ver imagen"; // La palabra que se mostrará como enlace
							$modal.find('[name=detalle]').append("<tr><td><label>Digitalizado</label></td><td><a href='" + url + "' target='_blank'>" + displayText + "</a></td></tr>");
						} else {
							$modal.find('[name=detalle]').append("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
						}
						if(data.recibos!=null){
							$modal.find('[name=comprobante]').append("<tr><td><b><label>Comprobantes</label><b></td><td><hr></td></tr>");
							url = 'cj/comp/print_reci?id=';
							for(var i=0;i<data.recibos.length;i++){
								comprobante = data.recibos[0];
								$modal.find('[name=comprobante]').append("<tr><td><label>Comprobante Nro: </label></td><td><a href='" + url+comprobante._id.$id + "' target='_blank'>" + comprobante.serie+"-"+comprobante.num + "</a></td></tr>");
							}
						}
						K.unblock({$element: $modal});
						$modal.find("[name=propietario]").click(function(){
							 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.propietario._id.$id).data('tipo_enti',data.propietario.tipo_enti);
						$modal.find("[name=espacio]").click(function(){
							cmEspa.showDetailsEspa({id: $(this).data('id')});
						}).data('id',data.espacio._id.$id);
					}
				};
				if(p.modal!=null){
					params.header = false;
					new K.Modal(params);
				}else new K.Window(params);
		},"json");
	},
	windowDetailsAdju: function(p){
//		K.notification({text:p.id});
		$.post('cm/oper/get','id='+p.id,function(data){
			var params = {
					id: 'windowDetailAdju'+p.id,
					contentURL: 'cm/oper/consult',
					title: 'Detalle de Operación',
					icon: 'ui-icon-copy',
					minimizable : false,
					width: 820,
					height: 300,
					buttons: {
						"Cerrar": function(){
							$(this).dialog('close');						
						}
					},
					onContentLoaded: function(){
				$modal = $('#windowDetailAdju'+p.id);
				K.block({$element: $modal});
				$modal.find('[name=principal]').append("<tr><td><label>Operaci&oacute;n</label></td><td><b>Adjuntaci&oacute;n</b></td></tr>");
				if(data.propietario.appat != null)$modal.find('[name=principal]').append("<tr><td><label>Propietario</label></td><td><a name='propietario'>"+data.propietario.nomb+" "+data.propietario.appat+" "+data.propietario.apmat+"</a></td></tr>");
				else $modal.find('[name=principal]').append("<tr><td><label>Propietario</label></td><td><a name='propietario'>"+data.propietario.nomb+"</a></td></tr>");
				$modal.find('[name=principal]').append("<tr><td><label>Ocupante</label></td><td><a name='ocupante'>"+data.ocupante.nomb+" "+data.ocupante.appat+" "+data.ocupante.apmat+"</a></td></tr>");
				$modal.find('[name=principal]').append("<tr><td><label>Espacio</label></td><td><a name='espacio'>"+data.espacio.nomb+"</a></td></tr>");
							
				$modal.find('[name=detalle]').append("<tr><td><label>Registrado</label></td><td>"+ciHelper.dateFormatLong(data.fecreg)+"</td></tr>");
				if(data.trabajador.appat != null)
					$modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a ref='#'>"+data.trabajador.nomb+" "+data.trabajador.appat+" "+data.trabajador.apmat+"</a></td></tr>");
				else
					$modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a ref='#'>"+data.trabajador.nomb+"</a></td></tr>");
				if(data.programacion){
					$modal.find('[name=detalle]').append("<tr><td><b><label>Programaci&oacute;n</label><b></td><td><hr></td></tr>");
					$modal.find('[name=detalle]').append("<tr><td><label>Programado</label></td><td>"+ciHelper.dateFormatLong(data.programacion.fecprog)+"</td></tr>");
					$modal.find('[name=detalle]').append("<tr><td><label>Observaciones</label></td><td>"+data.programacion.observ+"</td></tr>");
				}
				if(data.ejecucion){
					$modal.find('[name=detalle]').append("<tr><td><b><label>Ejecuci&oacute;n</label><b></td><td><hr></td></tr>");
					$modal.find('[name=detalle]').append("<tr><td><label>Fecha de Inicio</label></td><td>"+ciHelper.dateFormatLong(data.ejecucion.fecini)+"</td></tr>");
					$modal.find('[name=detalle]').append("<tr><td><label>Fecha de Fin</label></td><td>"+ciHelper.dateFormatLong(data.ejecucion.fecfin)+"</td></tr>");
					if(data.ejecucion.trabajador.appat != null)	$modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='ejec_trab'>"+data.ejecucion.trabajador.nomb+" "+data.ejecucion.trabajador.appat+" "+data.ejecucion.trabajador.apmat +"</a></td></tr>");
					else $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='ejec_trab'>"+data.ejecucion.trabajador.nomb+"</a></td></tr>");
					$modal.find('[name=detalle]').append("<tr><td><label>Observaciones</label></td><td>"+data.ejecucion.observ+"</td></tr>");
					$("#ejec_trab").click(function(){
						ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
					}).data('id',data.ejecucion.trabajador._id.$id).data('tipo_enti',data.ejecucion.trabajador.tipo_enti);
				}
				if(data.url_imagen!=null){
					var url = data.url_imagen;
					var displayText = "Ver imagen"; // La palabra que se mostrará como enlace
					$modal.find('[name=detalle]').append("<tr><td><label>Digitalizado</label></td><td><a href='" + url + "' target='_blank'>" + displayText + "</a></td></tr>");
				} else {
					$modal.find('[name=detalle]').append("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
				}
				if(data.recibos!=null){
					$modal.find('[name=comprobante]').append("<tr><td><b><label>Comprobantes</label><b></td><td><hr></td></tr>");
					url = 'cj/comp/print_reci?id=';
					for(var i=0;i<data.recibos.length;i++){
						comprobante = data.recibos[0];
						$modal.find('[name=comprobante]').append("<tr><td><label>Comprobante Nro: </label></td><td><a href='" + url+comprobante._id.$id + "' target='_blank'>" + comprobante.serie+"-"+comprobante.num + "</a></td></tr>");
					}
				}
				K.unblock({$element: $modal});
				$modal.find("[name=propietario]").click(function(){
					 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
				}).data('id',data.propietario._id.$id).data('tipo_enti',data.propietario.tipo_enti);
				$modal.find("[name=ocupante]").click(function(){
					 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
				}).data('id',data.ocupante._id.$id).data('tipo_enti',data.ocupante.tipo_enti);
				$modal.find("[name=espacio]").click(function(){
					cmEspa.showDetailsEspa({id: $(this).data('id')});
				}).data('id',data.espacio._id.$id);
			}
			};
			if(p.modal!=null){
				params.header = false;
				new K.Modal(params);
			}else new K.Window(params);
		},"json");
	},
	windowDetailsCons: function(p){
//		K.notification({text:p.id});
		$.post('cm/oper/get','id='+p.id,function(data){
			var params = {
					id: 'windowDetailCons'+p.id,
					contentURL: 'cm/oper/consult',
					title: 'Detalle de Operación',
					icon: 'ui-icon-home',
					minimizable : false,
					width: 820,
					height: 300,
					buttons: {
							"Cerrar": function(){
							$(this).dialog('close');
						}
					},
					onContentLoaded: function(){
						$modal = $('#windowDetailCons'+p.id);
						K.block({$element: $modal});
						$modal.find('[name=principal]').append("<tr><td><label>Operaci&oacute;n</label></td><td><b>Construcci&oacute;n</b></td></tr>");
						if(data.propietario.appat != null)$modal.find('[name=principal]').append("<tr><td><label>Propietario</label></td><td><a name='propietario'>"+data.propietario.nomb+" "+data.propietario.appat+" "+data.propietario.apmat+"</a></td></tr>");
						else $modal.find('[name=principal]').append("<tr><td><label>Propietario</label></td><td><a name='propietario'>"+data.propietario.nomb+"</a></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Espacio</label></td><td><a name='espacio'>"+data.espacio.nomb+"</a></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><b><label>Especificaciones</label><b></td><td><hr></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Capacidad</label></td><td>"+(data.construccion.capacidad!=''?data.construccion.capacidad:'0')+' m.'+"<span name='spConsCapa'></span></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Largo</label></td><td>"+(data.construccion.largo!=''?data.construccion.largo:'0')+' m.'+"<span name='spConsLargo'></span></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Ancho</label></td><td>"+(data.construccion.ancho!=''?data.construccion.ancho:'0')+' m.'+"<span name='spConsAncho'></span></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Altura 1</label></td><td>"+(data.construccion.altura1!=''?data.construccion.altura1:'0')+' m.'+"<span name='spConsAlt1'></span></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Altura 2</label></td><td>"+(data.construccion.altura2!=''?data.construccion.altura2:'0')+' m.'+"<span name='spConsAlt2'></span></td></tr>");
						if(data.construccion.finalizacion)$modal.find('[name=principal]').append("<tr><td><label>Tipo</label></td><td>"+cmEspa.tipos.mausoleo[data.construccion.finalizacion.tipo].nomb+"</td></tr>");
							
						$modal.find('[name=detalle]').append("<tr><td><label>Registrado</label></td><td>"+ciHelper.dateFormatLong(data.fecreg)+"</td></tr>");
						if(data.trabajador.appat != null) $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='trab'>"+data.trabajador.nomb+" "+data.trabajador.appat+" "+data.trabajador.apmat+"</a></td></tr>");			
						else $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='trab'>"+data.trabajador.nomb+"</a></td></tr>");	
						if(data.programacion){
							$modal.find('[name=detalle]').append("<tr><td><b><label>Programaci&oacute;n</label><b></td><td><hr></td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Programado</label></td><td>"+ciHelper.dateFormatLong(data.programacion.fecprog)+"</td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Observaciones</label></td><td>"+data.programacion.observ+"</td></tr>");
						}
						if(data.ejecucion){
							$modal.find('[name=detalle]').append("<tr><td><b><label>Ejecuci&oacute;n</label><b></td><td><hr></td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Fecha de Inicio</label></td><td>"+ciHelper.dateFormatLong(data.ejecucion.fecini)+"</td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Fecha de Fin</label></td><td>"+ciHelper.dateFormatLong(data.ejecucion.fecfin)+"</td></tr>");
							if(data.ejecucion.trabajador.appat != null)	$modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='ejec_trab'>"+data.ejecucion.trabajador.nomb+" "+data.ejecucion.trabajador.appat+" "+data.ejecucion.trabajador.apmat +"</a></td></tr>");
							else $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='ejec_trab'>"+data.ejecucion.trabajador.nomb+"</a></td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Observaciones</label></td><td>"+data.ejecucion.observ+"</td></tr>");
							$("#ejec_trab").click(function(){
								ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
							}).data('id',data.ejecucion.trabajador._id.$id).data('tipo_enti',data.ejecucion.trabajador.tipo_enti);
						}
						if(data.url_imagen!=null){
							var url = data.url_imagen;
							var displayText = "Ver imagen"; // La palabra que se mostrará como enlace
							$modal.find('[name=detalle]').append("<tr><td><label>Digitalizado</label></td><td><a href='" + url + "' target='_blank'>" + displayText + "</a></td></tr>");
						} else {
							$modal.find('[name=detalle]').append("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
						}

						if(data.recibos!=null){
							$modal.find('[name=comprobante]').append("<tr><td><b><label>Comprobantes</label><b></td><td><hr></td></tr>");
							url = 'cj/comp/print_reci?id=';
							for(var i=0;i<data.recibos.length;i++){
								comprobante = data.recibos[0];
								$modal.find('[name=comprobante]').append("<tr><td><label>Comprobante Nro: </label></td><td><a href='" + url+comprobante._id.$id + "' target='_blank'>" + comprobante.serie+"-"+comprobante.num + "</a></td></tr>");
							}
						}
						K.unblock({$element: $modal});
						$modal.find("[name=propietario]").click(function(){
							 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.propietario._id.$id).data('tipo_enti',data.propietario.tipo_enti);
						$modal.find("[name=trab]").click(function(){
							ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.trabajador._id.$id).data('tipo_enti',data.trabajador.tipo_enti);
						$modal.find("[name=espacio]").click(function(){
							cmEspa.showDetailsEspa({id: $(this).data('id')});
						}).data('id',data.espacio._id.$id);
						var tmp = '';
						if(data.construccion.finalizacion){
							if(data.construccion.capacidad!=data.construccion.finalizacion.capacidad){
								$modal.find('[name=spConsCapa]').html('&nbsp;<label style="color:red;">No coincide con solicitud: '+(data.construccion.finalizacion.capacidad!=''?data.construccion.finalizacion.capacidad:'0')+' m.</label>');
								tmp = 'Cobrar excesos de construcci&oacute;n';
							}
							if(data.construccion.ancho!=data.construccion.finalizacion.ancho){
								$modal.find('[name=spConsAncho]').html('&nbsp;<label style="color:red;">No coincide con solicitud: '+(data.construccion.finalizacion.ancho!=''?data.construccion.finalizacion.ancho:'0')+' m.</label>' );
								tmp = 'Cobrar excesos de construcci&oacute;n';
							}
							if(data.construccion.largo != data.construccion.finalizacion.largo){
								$modal.find('[name=spConsLargo]').html('&nbsp;<label style="color:red;">No coincide con solicitud: '+(data.construccion.finalizacion.largo!=''?data.construccion.finalizacion.largo:'0')+' m.</label>' );
								tmp = 'Cobrar excesos de construcci&oacute;n';
							}
							if(data.construccion.altura1!=data.construccion.finalizacion.altura1){
								$modal.find('[name=spConsAlt1]').html('&nbsp;<labels style="color:red;">No coincide con solicitud: '+(data.construccion.finalizacion.altura1!=''?data.construccion.finalizacion.altura1:'0') +' m.</label>' );
								tmp = 'Cobrar excesos de construcci&oacute;n';
							}
							if(data.construccion.altura2!=data.construccion.finalizacion.altura2){
								$modal.find('[name=spConsAlt2]').html('&nbsp;<labels style="color:red;">No coincide con solicitud: '+(data.construccion.finalizacion.altura2!=''?data.construccion.finalizacion.altura2:'0') +' m.</label>' );
								tmp = 'Cobrar excesos de construcci&oacute;n';
							}
							if(tmp!='') $modal.find('[name=spConsObserv]').html( tmp );
						}
					}
				};
				if(p.modal!=null){
					params.header = false;
					new K.Modal(params);
				}else new K.Window(params);
		},"json");
	},
	windowDetailsAmp: function(p){
//		K.notification({text:p.id});
		$.post('cm/oper/get','id='+p.id,function(data){
			var params = {
					id: 'windowDetailAmp'+p.id,
					contentURL: 'cm/oper/consult',
					title: 'Detalle de Operaci&oacuten;n',
					icon: 'ui-icon-home',
					minimizable : false,
					width: 820,
					height: 300,
					buttons: {
							"Cerrar": function(){
							$(this).dialog('close');
						}
					},
					onContentLoaded: function(){
						$modal = $('#windowDetailAmp'+p.id);
						K.block({$element: $modal});
						$modal.find('[name=principal]').append("<tr><td><label>Operaci&oacute;n</label></td><td><b>Ampliaci&oacute;n</b></td></tr>");
						if(data.propietario.appat != null)$modal.find('[name=principal]').append("<tr><td><label>Propietario</label></td><td><a name='propietario'>"+data.propietario.nomb+" "+data.propietario.appat+" "+data.propietario.apmat+"</a></td></tr>");
						else $modal.find('[name=principal]').append("<tr><td><label>Propietario</label></td><td><a name='propietario'>"+data.propietario.nomb+"</a></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Espacio</label></td><td><a name='espacio'>"+data.espacio.nomb+"</a></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><b><label>Especificaciones</label><b></td><td><hr></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Capacidad</label></td><td>"+(data.ampliacion.capacidad!=''?data.ampliacion.capacidad:'0')+' m.'+"<span name='spConsCapa'></span></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Largo</label></td><td>"+(data.ampliacion.largo!=''?data.ampliacion.largo:'0')+' m.'+"<span name='spConsLargo'></span></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Ancho</label></td><td>"+(data.ampliacion.ancho!=''?data.ampliacion.ancho:'0')+' m.'+"<span name='spConsAncho'></span></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Altura 1</label></td><td>"+(data.ampliacion.altura1!=''?data.ampliacion.altura1:'0')+' m.'+"<span name='spConsAlt1'></span></td></tr>");
						$modal.find('[name=principal]').append("<tr><td><label>Altura 2</label></td><td>"+(data.ampliacion.altura2!=''?data.ampliacion.altura2:'0')+' m.'+"<span name='spConsAlt2'></span></td></tr>");
						if(data.ampliacion.finalizacion)$modal.find('[name=principal]').append("<tr><td><label>Tipo</label></td><td>"+cmEspa.tipos.mausoleo[data.ampliacion.finalizacion.tipo].nomb+"</td></tr>");
							
						$modal.find('[name=detalle]').append("<tr><td><label>Registrado</label></td><td>"+ciHelper.dateFormatLong(data.fecreg)+"</td></tr>");
						if(data.trabajador.appat != null) $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='trab'>"+data.trabajador.nomb+" "+data.trabajador.appat+" "+data.trabajador.apmat+"</a></td></tr>");			
						else $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='trab'>"+data.trabajador.nomb+"</a></td></tr>");	
						if(data.programacion){
							$modal.find('[name=detalle]').append("<tr><td><b><label>Programaci&oacute;n</label><b></td><td><hr></td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Programado</label></td><td>"+ciHelper.dateFormatLong(data.programacion.fecprog)+"</td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Observaciones</label></td><td>"+data.programacion.observ+"</td></tr>");
						}
						if(data.ejecucion){
							$modal.find('[name=detalle]').append("<tr><td><b><label>Ejecuci&oacute;n</label><b></td><td><hr></td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Fecha de Inicio</label></td><td>"+ciHelper.dateFormatLong(data.ejecucion.fecini)+"</td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Fecha de Fin</label></td><td>"+ciHelper.dateFormatLong(data.ejecucion.fecfin)+"</td></tr>");
							if(data.ejecucion.trabajador.appat != null)	$modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='ejec_trab'>"+data.ejecucion.trabajador.nomb+" "+data.ejecucion.trabajador.appat+" "+data.ejecucion.trabajador.apmat +"</a></td></tr>");
							else $modal.find('[name=detalle]').append("<tr><td><label>Registrado por</label></td><td><a name='ejec_trab'>"+data.ejecucion.trabajador.nomb+"</a></td></tr>");
							$modal.find('[name=detalle]').append("<tr><td><label>Observaciones</label></td><td>"+data.ejecucion.observ+"</td></tr>");
							$("#ejec_trab").click(function(){
								ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
							}).data('id',data.ejecucion.trabajador._id.$id).data('tipo_enti',data.ejecucion.trabajador.tipo_enti);
						}
						if(data.url_imagen!=null){
							var url = data.url_imagen;
							var displayText = "Ver imagen"; // La palabra que se mostrará como enlace
							$modal.find('[name=detalle]').append("<tr><td><label>Digitalizado</label></td><td><a href='" + url + "' target='_blank'>" + displayText + "</a></td></tr>");
						} else {
							$modal.find('[name=detalle]').append("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
						}
						if(data.recibos!=null){
							$modal.find('[name=comprobante]').append("<tr><td><b><label>Comprobantes</label><b></td><td><hr></td></tr>");
							url = 'cj/comp/print_reci?id=';
							for(var i=0;i<data.recibos.length;i++){
								comprobante = data.recibos[0];
								$modal.find('[name=comprobante]').append("<tr><td><label>Comprobante Nro: </label></td><td><a href='" + url+comprobante._id.$id + "' target='_blank'>" + comprobante.serie+"-"+comprobante.num + "</a></td></tr>");
							}
						}
						K.unblock({$element: $modal});
						$modal.find("[name=propietario]").click(function(){
							 ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.propietario._id.$id).data('tipo_enti',data.propietario.tipo_enti);
						$modal.find("[name=trab]").click(function(){
							ciDetails.windowDetailsEnti({id: $(this).data('id'),tipo_enti: $(this).data('tipo_enti'),modal: true});
						}).data('id',data.trabajador._id.$id).data('tipo_enti',data.trabajador.tipo_enti);
						$modal.find("[name=espacio]").click(function(){
							cmEspa.showDetailsEspa({id: $(this).data('id')});
						}).data('id',data.espacio._id.$id);
						var tmp = '';
						if(data.ampliacion.finalizacion){
							if(data.ampliacion.capacidad!=data.ampliacion.finalizacion.capacidad){
								$modal.find('[name=spConsCapa]').html('&nbsp;<label style="color:red;">No coincide con solicitud: '+(data.ampliacion.finalizacion.capacidad!=''?data.ampliacion.finalizacion.capacidad:'0')+' m.</label>');
								tmp = 'Cobrar excesos de construcci&oacute;n';
							}
							if(data.ampliacion.ancho!=data.ampliacion.finalizacion.ancho){
								$modal.find('[name=spConsAncho]').html('&nbsp;<label style="color:red;">No coincide con solicitud: '+(data.ampliacion.finalizacion.ancho!=''?data.ampliacion.finalizacion.ancho:'0')+' m.</label>' );
								tmp = 'Cobrar excesos de construcci&oacute;n';
							}
							if(data.ampliacion.largo != data.ampliacion.finalizacion.largo){
								$modal.find('[name=spConsLargo]').html('&nbsp;<label style="color:red;">No coincide con solicitud: '+(data.ampliacion.finalizacion.largo!=''?data.ampliacion.finalizacion.largo:'0')+' m.</label>' );
								tmp = 'Cobrar excesos de construcci&oacute;n';
							}
							if(data.ampliacion.altura1!=data.ampliacion.finalizacion.altura1){
								$modal.find('[name=spConsAlt1]').html('&nbsp;<labels style="color:red;">No coincide con solicitud: '+(data.ampliacion.finalizacion.altura1!=''?data.ampliacion.finalizacion.altura1:'0') +' m.</label>' );
								tmp = 'Cobrar excesos de construcci&oacute;n';
							}
							if(data.ampliacion.altura2!=data.ampliacion.finalizacion.altura2){
								$modal.find('[name=spConsAlt2]').html('&nbsp;<labels style="color:red;">No coincide con solicitud: '+(data.ampliacion.finalizacion.altura2!=''?data.ampliacion.finalizacion.altura2:'0') +' m.</label>' );
								tmp = 'Cobrar excesos de construcci&oacute;n';
							}
							if(tmp!='') $modal.find('[name=spConsObserv]').html( tmp );
						}
					}
				};
				if(p.modal!=null){
					params.header = false;
					new K.Modal(params);
				}else new K.Window(params);
		},"json");
	},
	windowRegiOcup: function(p){
		if(p==null) p = new Object;
		p.cbEspacio = function(data){
			p.$w.find('[name=rbtnConc]').button('enable',true);
			var tipo = '';
			p.$w.find('[name=det]').html("");
			p.$w.find('input:radio[name=rbtnConc]:eq(1)').attr('checked',true).click();
			p.$w.find('[name=rbtnConc]').button('refresh');
			if(data.nicho!=null){
				tipo = "Nicho-"+cmOper.statesNicho[data.nicho.tipo].descr;
				p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Nombre del Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.nomb+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.num+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Nicho:</label></td><td>"+data.nicho.num+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Piso:</label></td><td>"+data.nicho.piso+"</td></tr>");
				p.$w.find('[name=det]').append("<tr><td><label>Fila:</label></td><td>"+data.nicho.fila+"</td></tr>");
			}
			else if(data.tumba!=null){
					tipo = "Tumba";
					p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.tumba.denominacion+"</td></tr>");
				 }
				 else {
					tipo = "Mausoleo";
					p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>Tipo de Zona:</label></td><td>"+cmOper.statesZonaMaus[data.mausoleo.zona].descr+"</td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.mausoleo.denominacion+"</td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Lote:</label></td><td>"+data.mausoleo.lote+"</td></tr>");
					p.$w.find('[name=rbtnConc]').button('disable',true);
				 }
			p.$w.find('[name=det]').append("</table>");
			p.$w.find('[name=divPropTemp]').remove();
			if(data.propietario!=null){
				p.$w.find('[name=section2] table:first').show();
				p.$w.find('[name=section2] table:last').hide();
				if(data.propietario.tipo_enti=='P') p.$w.find('[name=spPropDef]').html(data.propietario.nomb+' '+data.propietario.appat+' '+data.propietario.apmat);
				else p.$w.find('[name=spPropDef]').html(data.propietario.nomb);
			}else{
				p.$w.find('[name=section2] table:first').hide();
				p.$w.find('[name=section2] table:last').show();
			}
			p.$w.find('[name=tipo]').data('data',data);
		};
		p.cbProp = function(data){
			p.entidad = data;
			p.$w.find('[name=spPropSel]').data('data',data);
			if(data.tipo_enti=='P'){
				p.$w.find('[name=spPropSel]').html( data.nomb + ' ' + data.appat + ' ' + data.apmat );
			}else{
				p.$w.find('[name=spPropSel]').html( data.nomb );
			}
		};
		p.cbFuneraria = function(data){
			p.$w.find('[name=funeraria]').data('data',data);
			p.$w.find('[name=funeraria]').html( data.nomb );
		};
		p.cbMunicipalidad = function(data){
			p.$w.find('[name=municipalidad]').data('data',data);
			p.$w.find('[name=municipalidad]').html( data.nomb );
		};
		new K.Window({
			id: 'windowRegiOcup',
			title: 'Registrar Ocupante Anterior',
			width: 830,
			height: 370,
			contentURL: 'cm/oper/ocup_ante',
			icon: 'ui-icon-person',
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						ocupantes: []
					};
					for(var i=0,j=p.$w.find('.gridBody:last .item').length; i<j; i++){
						var $row = p.$w.find('.gridBody:last .item').eq(i),
						tmp = {
							dni: $row.find('[name=dni]').val(),
							nomb: $row.find('[name=nomb]').val(),
							appat: $row.find('[name=appat]').val(),
							apmat: $row.find('[name=apmat]').val(),
							fecnac: $row.find('[name=fecnac]').val()
						};
						/*if(tmp.dni==''){
							$row.find('[name=dni]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar el DNI del ocupante!',
								type: 'error'
							});
						}*/
						if(tmp.nomb==''){
							$row.find('[name=nomb]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar el nombre del ocupante!',
								type: 'error'
							});
						}
						if(tmp.appat==''){
							$row.find('[name=appat]').focus();
							return K.notification({
								title: ciHelper.titleMessages.infoReq,
								text: 'Debe ingresar el apellido paterno del ocupante!',
								type: 'error'
							});
						}
						data.ocupantes.push(tmp);
					}
					if(data.ocupantes.length==0){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar los datos de al menos un ocupante!',
							type: 'error'
						});
					}
					data.espacio = new Object;
					var tmp = p.$w.find('[name=tipo]').data('data');
					if(tmp==null){
						p.$w.find('[name=section2]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Seleccione el espacio!',type: 'error'});
					}
					data.espacio._id = tmp._id.$id;
					data.espacio.nomb = tmp.nomb;
					if(tmp.ocupantes!=null){
						if(tmp.ocupantes.length>0 && tmp.nicho!=null){
							data.adjuntacion = new Object;
							data.adjuntacion.ocupante = tmp.ocupantes[tmp.ocupantes.length-1];
							data.adjuntacion.ocupante._id = data.adjuntacion.ocupante._id.$id;
						}
					}
					data.propietario = {};
					var tmp;
					if(p.$w.find('[name=spPropDef]').html()!=''){
						tmp = p.$w.find('[name=tipo]').data('data').propietario;
						data.espacio_propietario = true;
					}else{
						if(p.$w.find('[name=rbtnProp]:checked').val()==0){
							tmp = K.session.titular;
							data.concesion = new Object;
							data.concesion.condicion = 'P';
						}else{
							tmp = p.$w.find('[name=spPropSel]').data('data');
							if(tmp==null){
								p.$w.find('[name=section2]').click();
								return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar un propietario!',type: 'error'});
							}
							data.concesion = new Object;
							data.concesion.condicion = p.$w.find('[name=rbtnConc]:checked').val();
							if(data.concesion.condicion=='T'){
								data.concesion.fecven = p.$w.find('[name=spFecven]').data('date');
								data.concesion.anos = p.$w.find('[name=anos]').val();
								if(data.concesion.anos==''){
									p.$w.find('[name=section2]').click();
									p.$w.find('[name=anos]').focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una cantidad de a&ntildeos!',type: 'error'});
								}
							}
						}
					}
					data.propietario = ciHelper.enti.dbRel(tmp);
					if(p.$w.find('[name=rbtnAsig]:checked').val()==1){
						data.inh = true;
						data.inhumacion = new Object;
						data.inhumacion.fecdef = p.$w.find('[name=fecdef]').val();
						data.inhumacion.edad = p.$w.find('[name=edad]').val();
						data.inhumacion.causa = p.$w.find('[name=causa]').val();
						/*-if(data.inhumacion.fecdef==''){
							p.$w.find('[name=section3]').click();
							p.$w.find('[name=fecdef]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese la fecha de defunci&oacute;n!',type: 'error'});
						}*/
						/*if(data.inhumacion.edad==''){
							p.$w.find('[name=section3]').click();
							p.$w.find('[name=edad]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese la edad del ocupante al fallecer!',type: 'error'});
						}
						if(data.inhumacion.causa==''){
							p.$w.find('[name=section3]').click();
							p.$w.find('[name=causa]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese la causa de fallecimiento!',type: 'error'});
						}*/
						tmp = p.$w.find('[name=funeraria]').data('data');
						if(tmp==null){
							/*p.$w.find('[name=section3]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una funeraria!',type: 'error'});*/
						}else{
							data.inhumacion.funeraria = new Object;
							data.inhumacion.funeraria._id = tmp._id.$id;
							data.inhumacion.funeraria.tipo_enti = tmp.tipo_enti;
							data.inhumacion.funeraria.nomb = tmp.nomb;
							if(tmp.tipo_enti=='P'){
								data.inhumacion.funeraria.appat = tmp.appat;
								data.inhumacion.funeraria.apmat = tmp.apmat;
							}
						}
						tmp = p.$w.find('[name=municipalidad]').data('data');
						if(tmp==null){
							/*p.$w.find('[name=section3]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una municipalidad!',type: 'error'});*/
						}else{
							data.inhumacion.municipalidad = new Object;
							data.inhumacion.municipalidad._id = tmp._id.$id;
							data.inhumacion.municipalidad.tipo_enti = tmp.tipo_enti;
							data.inhumacion.municipalidad.nomb = tmp.nomb;
							if(tmp.tipo_enti=='P'){
								data.inhumacion.municipalidad.appat = tmp.appat;
								data.inhumacion.municipalidad.apmat = tmp.apmat;
							}
						}
						data.inhumacion.partdef = p.$w.find('[name=partida]').val();
						/*if(data.inhumacion.partdef==''){
							p.$w.find('[name=section3]').click();
							p.$w.find('[name=partida]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese la partida de defunci&oacute;n!',type: 'error'});
						}*/
						data.inhumacion.fecinh = p.$w.find('[name=fecinh]').val();
						/*if(data.inhumacion.fecinh==''){
							p.$w.find('[name=section3]').click();
							p.$w.find('[name=fecinh]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese la fecha de inhumaci&oacute;n!',type: 'error'});
						}*/
						data.inhumacion.observ = p.$w.find('[name=observ]').val();
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('cm/oper/save_ocup_ante',data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'Operaci&oacute;n registrada con &eacute;xito'});
						K.closeWindow(p.$w.attr('id'));
						cmCuen.init();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){
				p.$w.find('[name=btnEli]').die('click');
				p.$w.find('[name=btnAgr]').die('click');
				p = null;
			},
			onContentLoaded: function(){
				p.$w = $('#windowRegiOcup');
				//p.$w.find('label').css('color','#656565');
				p.$w.layout({
					resizeWithWindow:	false,
					west__size:			150,
					west__closable:		false,
					west__resizable:	false,
					west__slidable:		false
				});
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
				p.$w.find('[name=btnAgr]').live('click',function(){
					p.$w.find('.gridBody:last [name=btnAgr]').remove();
					var $row = p.$w.find('.gridReference').clone();
					$row.find('[name=dni]').numeric();
					$row.find('[name=fecnac]').datepicker({dateFormat: 'yy-mm-dd'});
					$row.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
					$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
					$row.wrapInner('<a class="item">');
					p.$w.find('.gridBody:last').append($row.children());
				});
				p.$w.find('[name=btnEli]').live('click',function(){
					$(this).closest('.item').remove();
					if(p.$w.find('.gridBody:last [name=btnAgr]').length==0){
						p.$w.find('.item:last li:eq(5)').append('<button name="btnAgr">Agregar</button>');
						p.$w.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
					}
					if(p.$w.find('.gridBody:last .item').length==0){
						var $row = p.$w.find('.gridReference').clone();
						$row.find('[name=dni]').numeric();
						$row.find('[name=fecnac]').datepicker({dateFormat: 'yy-mm-dd'});
						$row.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
						$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
						$row.wrapInner('<a class="item">');
						p.$w.find('.gridBody:last').append($row.children());
					}
				});
				p.$w.find('.grid').eq(2).bind('scroll',function(){
					p.$w.find('.grid').eq(1).scrollLeft(p.$w.find('.grid').eq(2).scrollLeft());
				});
				var $row = p.$w.find('.gridReference').clone();
				$row.find('[name=dni]').numeric();
				$row.find('[name=fecnac]').datepicker({dateFormat: 'yy-mm-dd'});
				$row.find('[name=btnAgr]').button({icons: {primary: 'ui-icon-plusthick'},text: false});
				$row.find('[name=btnEli]').button({icons: {primary: 'ui-icon-trash'},text: false});
				$row.wrapInner('<a class="item">');
				p.$w.find('.gridBody:last').append($row.children());
				p.$w.find('[name=btnEspacio]').click(function(){
					cmEspa.windowSelect({
						$parent: p.$w,
						callback: p.cbEspacio,
						choose: true,
						filter: [{nomb: 'estado',value: 'D'}]
					});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=section2] table').hide();
				p.$w.find('[name=trProp]').buttonset();
				p.$w.find('#rbtnPropBen').click(function(){
					p.$w.find('.trConProp,.trConPropTemp').hide();
				}).click();
				p.$w.find('#rbtnPropOtr').click(function(){
					p.$w.find('.trConProp').show();
				});
				p.$w.find('[name=btnBuscarProp]').click(function(){
					//ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbProp});
					mgEnti.windowSelect({callback: p.cbProp});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnAgregarProp]').remove();/*.click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbProp});
				}).button({icons: {primary: 'ui-icon-plusthick'}});*/
				p.$w.find('[name=trConc]').buttonset();
				p.$w.find('#rbtnConcTemp').click(function(){
					p.$w.find('.trConPropTemp').show();
				});
				p.$w.find('#rbtnConcPerp').click(function(){
					p.$w.find('.trConPropTemp').hide();
				}).click();
				p.$w.find('[name=anos]').change(function(){
					var date = ciHelper.dateSumNow(parseInt(p.$w.find('[name=anos]').val()));
					p.$w.find('[name=spFecven]').data('date',K.dateTimeFormat(date));
					p.$w.find('[name=spFecven]').html(ciHelper.dateFormatLongS(date));
				}).numeric();
				p.$w.find('[name=divAsig]').buttonset();
				p.$w.find('#rbtnAsig').click(function(){
					p.$w.find('table:last').hide();
				}).click();
				p.$w.find('#rbtnInh').click(function(){
					p.$w.find('table:last').show();
				});
				p.$w.find('[name=fecdef]').datepicker();
				p.$w.find('[name=fecdef]').datepicker( "option", "dateFormat", 'yy-mm-dd' );
				p.$w.find('[name=btnFune]').click(function(){
					ciSearch.windowSearchEmpresa({$window: p.$w,callback: p.cbFuneraria});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnMuni]').click(function(){
					ciSearch.windowSearchEmpresa({$window: p.$w,callback: p.cbMunicipalidad});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=fecinh]').datepicker();
				p.$w.find('[name=fecinh]').datepicker( "option", "dateFormat", 'yy-mm-dd' );
				//p.$w.find('.ui-layout-center').css('overflow','hidden');
				if(p.espacio!=null){
					p.cbEspacio(p.espacio);
				}
				if(p.entidad!=null){
					p.cbProp(p.entidad);
				}
				K.unblock({$element: p.$w});
			}
		});
	},
	windowAnuOper: function(p){
		if(p==null) p = {};
		$.extend(p,{
			cbProp: function(data){
				p.$w.find('[name=nomb]').html(data.nomb).data('data');
				p.$w.find('[name=appat]').html(data.appat+' '+data.apmat);
				K.block({$element: p.$w});
				$.post('cm/espa/espa_prop','_id='+data._id.$id,function(data){
					if(data!=null){
						var $cbo = p.$w.find('[name=espacio]').empty();
						for(var i=0,j=data.length; i<j; i++){
							$cbo.append('<option value="'+data[i]._id.$id+'">'+data[i].nomb+'</option>');
							$cbo.find('option:last').data('data',data[i]);
						}
					}else{
						K.closeWindow(p.$w.attr('id'));
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No hay espacios asignados para el propietario seleccionado!',type: 'info'});
					}
					if(p.espacio!=null){
						$cbo.selectVal(p.espacio._id.$id);
						$cbo.attr('disabled','disabled');
					}
					p.$w.find('[name=espacio]').change();
					K.unblock({$element: p.$w});
				},'json');
			}
		});
		new K.Modal({
			id: 'windowCmAnulOper',
			title: 'Anular Operaci&oacute;n',
			contentURL: 'cm/oper/anul_oper',
			icon: 'ui-icon-circle-close',
			width: 500,
			height: 410,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					if(p.$w.find('.gridBody .ui-state-highlight').length<=0){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe escoger una operaci&oacute;n!',type: 'error'});
					}
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('cm/oper/save_anular',{
						_id: p.$w.find('.gridBody .ui-state-highlight').closest('.item').data('data')._id.$id,
						oper: p.$w.find('.gridBody .ui-state-highlight').closest('.item').data('oper')
					},function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiAct,text: 'Operaci&oacute;n anulada con &eacute;xito'});
						K.closeWindow(p.$w.attr('id'));
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowCmAnulOper');
				p.$w.find('[name=espacio]').change(function(){
					p.$w.find('.gridBody').empty();
					var espa = p.$w.find('[name=espacio] option:selected').data('data');
					K.block({$element: p.$w});
					$.post('cm/espa/operaciones',{_id:espa._id.$id, oper:'',campo:'espacio._id'},function(data){
						if(data!=null){
							for(var i=0,j=data.length; i<j; i++){
								if(data[i].estado=='A'){
									var $row = p.$w.find('.gridReference').clone(),
									result = data[i];
									$row.find('li:eq(1)').html(ciHelper.dateFormat(result.fecreg));
									$row.wrapInner('<a class="item">');
									$row.find('.item').data('data',result);
									if(result.concesion != null){
										if(result.concesion.estado=='A'){
											$row.find('li:eq(0)').html('Concesi&oacute;n');
											$row.find('.item').data('oper','concesion');
										}
									}
									if(result.construccion != null){
										if(result.construccion.finalizacion==null){
											$row.find('li:eq(0)').html('Construcci&oacute;n');
											$row.find('.item').data('oper','construccion');
										}
									}
									if(result.asignacion != null){
										if(result.asignacion==true){
											$row.find('li:eq(0)').html('Asignaci&oacute;n');
											$row.find('.item').data('oper','asignacion');
										}
									}
									if(result.adjuntacion != null){
										if(result.adjuntacion==true){
											$row.find('li:eq(0)').html('Adjuntaci&oacute;n');
											$row.find('.item').data('oper','adjuntacion');
										}
									}
									if(result.traspaso != null){
										/*if(){
											$row.find('li:eq(0)').html('Traspaso');
											$row.find('.item').data('oper','traspaso');
										}*/
									}
									if(result.inhumacion != null){
										$row.find('li:eq(0)').html('Inhumaci&oacute;n');
										$row.find('.item').data('oper','inhumacion');
									}
									if(result.traslado != null){
										if(result.ejecucion==null){
											$row.find('li:eq(0)').html('Traslado');
											$row.find('.item').data('oper','traslado');
										}
									}
									if(result.colocacion != null){
										$row.find('li:eq(0)').html('Colocaci&oacute;n');
										$row.find('.item').data('oper','colocacion');
									}
									if(result.conversion != null){
										$row.find('li:eq(0)').html('Conversi&oacute;n');
										$row.find('.item').data('oper','conversion');
									}
									if(result.traslado_ext != null){
										if(result.ejecucion==null){
											$row.find('li:eq(0)').html('Traslado externo (desde otro cementerio)');
											$row.find('.item').data('oper','traslado_ext');
										}
									}
									if($row.find('li:eq(0)').html()!='')
										p.$w.find('.gridBody').append($row.children());
								}
							}
						}
						K.unblock({$element: p.$w});
					},'json');
				});
				if(p.entidad!=null){
					p.cbProp(p.entidad);
					p.$w.find('[name=btnSelProp]').remove();
				}else{
					p.$w.find('[name=btnSelProp]').click(function(){
						/*ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbProp,filter: [
 						    //{nomb: 'tipo_enti',value: 'P'},
						    {nomb: 'roles.propietario',value: {$exists: true}}
						]});*/
						mgEnti.windowSelect({callback: p.cbProp,filter: [
 						    //{nomb: 'tipo_enti',value: 'P'},
						    //{nomb: 'roles.propietario',value: {$exists: true}}
						]});
					}).button({icons: {primary: 'ui-icon-search'}});
				}
			}
		});
	},
	windowReno: function(p){
		if(p==null) p = {};
		$.extend(p,{
			cbGestor: function(data){
				p.$w.find('[name=nomb]').data('data',data);
				if(data.tipo_enti=='P'){
					p.$w.find('tr:eq(1)').show();
					p.$w.find('[name=nomb]').html( data.nomb );
					p.$w.find('[name=apell]').html( data.appat + ' ' + data.apmat );
				}else{
					p.$w.find('tr:eq(1)').hide();
					p.$w.find('[name=nomb]').html( data.nomb );
				}
				K.block({$element: p.$w});
				$.post('cm/espa/espa_prop','_id='+data._id.$id,function(data){
					if(data!=null){
						if(data.length!=0){
							var $cbo = p.$w.find('[name=espacio]');
							for(var i=0,j=data.length; i<j; i++){
								$cbo.append('<option value="'+data[i]._id.$id+'">'+data[i].nomb+'</option>');
								$cbo.find('option:last').data('data',data[i]);
							}
						}else K.notification({title: ciHelper.titleMessages.infoReq,text: 'El propietario debe tener al menos un espacio asignado para la renovaci&oacute;n!',type: 'info'});
					}else K.notification({title: ciHelper.titleMessages.infoReq,text: 'El propietario debe tener al menos un espacio asignado para la renovaci&oacute;n!',type: 'info'});
					p.$w.find('[name=espacio]').change();
					K.unblock({$element: p.$w});
				},'json');
			}
		});
		if(p.entidad!=null)
			p.windId = 'windowRenoConce'+p.entidad._id.$id;
		else
			p.windId = 'windowRenoConce';
		new K.Modal({
			id: p.windId,
			title: 'Renovar Concesi&oacute;n',
			icon: 'ui-icon-bookmark',
			width: 675,
			height: 370,
			contentURL: 'cm/oper/reno',
			store: false,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {},
					propietario = p.$w.find('[name=nomb]').data('data');
					if(p.$w.find('[name=nomb]').html() == ""){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un propietario!",type:"error"});
					}
					data.propietario = ciHelper.enti.dbRel(propietario);
					if(p.$w.find('[name=espacio] option:selected').data('data') == null){
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione un espacio!",type:"error"});
					}
					var espacio = p.$w.find('[name=espacio] option:selected').data('data');
					data.espacio = {
						_id: espacio._id.$id,
						nomb: espacio.nomb
					};
					data.concesion = {
						condicion: p.$w.find('[name=rbtnTipo]:checked').val()
					};
					if(data.concesion.condicion == "T"){
						if(p.$w.find('[name=anios]').val() == ""){
							return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese los a&ntilde;os!",type:"error"});
						}
						data.concesion.fecven = p.$w.find('[name=fecven]').data('date');
					}
					var extra = p.payment.getPay();
					if(extra!=false&&extra!=10) $.extend(data,extra);
					else if(extra==false) return false;
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/oper/save_reno",data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						cmCuen.init();
						K.notification({title: ciHelper.titleMessages.regiGua,text: "La Operaci&oacute;n se guard&oacute; exitosamente!"});
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#'+p.windId);
				p.$mainPanel = p.$w.find('.ui-layout-center');
				p.$leftPanel = p.$w.find('.ui-layout-west');
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
				});
				p.$w.find('[name=anios]').val(1).numeric().spinner({step: 1,min: 1,max: 1000,stop: function(){ $(this).change(); }});
				p.$w.find('[name=anios]').parent().find('.ui-button').css('height','14px');
				p.$w.find('[name=btnBusPro]').click(function(){
					/*ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbGestor,filter: [
					    {nomb: 'roles.propietario',value: {$exists: true}}
					]});*/
					mgEnti.windowSelect({callback: p.cbGestor,filter: [
					    //{nomb: 'roles.propietario',value: {$exists: true}}
					]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=espacio]').change(function(){
					var data = $(this).find('option:selected').data('data');
					if(data!=null){
						p.$w.find('[name=rbtnTipo]').button('enable',true);
						var tipo = '';
						p.$w.find('[name=det]').html("");
						if(data.nicho!=null){
							tipo = "Nicho-"+cmOper.statesNicho[data.nicho.tipo].descr;
							p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
							p.$w.find('[name=det]').append("<tr><td><label>Nombre del Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.nomb+"</td></tr>");
							p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.num+"</td></tr>");
							p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Nicho:</label></td><td>"+data.nicho.num+"</td></tr>");
							p.$w.find('[name=det]').append("<tr><td><label>Piso:</label></td><td>"+data.nicho.piso+"</td></tr>");
							p.$w.find('[name=det]').append("<tr><td><label>Fila:</label></td><td>"+data.nicho.fila+"</td></tr>");
						}
						else if(data.tumba!=null){
							tipo = "Tumba";
							p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
							p.$w.find('[name=det]').append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.tumba.denominacion+"</td></tr>");
						 }
						 else {
							tipo = "Mausoleo";
							p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
							p.$w.find('[name=det]').append("<tr><td><label>Tipo de Zona:</label></td><td>"+cmOper.statesZonaMaus[data.mausoleo.zona].descr+"</td></tr>");
							p.$w.find('[name=det]').append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.mausoleo.denominacion+"</td></tr>");
							p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Lote:</label></td><td>"+data.mausoleo.lote+"</td></tr>");
							p.$w.find('input:radio[name=rbtnTipo]:eq(1)').attr('checked',true).click();
							p.$w.find('[name=rbtnTipo]').button('refresh');
							p.$w.find('[name=rbtnTipo]').button('disable',true);
						 }
						p.$w.find('[name=det]').append("</table>");
						p.$w.find('[name=tipo]').data('data',data);
						p.payment.loadData();
					}
				});
				p.$w.find('#rbtnCond1').click(function(){
					p.$w.find('[name=rowTemporal]').show();
				});
				p.$w.find('#rbtnCond2').click(function(){
					p.$w.find('[name=rowTemporal]').hide();
				}).click();
				p.$w.find('#rbtnTipo').buttonset();
				if(p.entidad!=null){
					p.$w.find('td:first').hide();
					p.cbGestor(p.entidad);
				}
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
				p.$w.find('[name=anios]').change(function(){
					var date = ciHelper.dateSumNow(parseInt(p.$w.find('[name=anios]').val()));
					p.$w.find('[name=fecven]').data('date',K.dateTimeFormat(date));
					p.$w.find('[name=fecven]').html(ciHelper.dateFormatLongS(date));
				}).change();
				p.payment = new cmOper.payment({
					$w: p.$w,
					getEspa: function(){
						return p.$w.find('[name=espacio] option:selected').data('data');
					}
				});
				K.unblock({$element: p.$w});
			}
		});
	},
	windowConver: function(p){
		if(p==null) p = {};
		$.extend(p,{
			filterGestor: [
				{nomb: 'roles.propietario',value: {$exists: true}},
				{nomb: 'roles.propietario.espacios._id',value: {$exists: true}}
			],
			cbGestor: function(data){
				p.$w.find('[name=nomb]').data('data',data);
				if(data.tipo_enti=='P'){
					p.$w.find('tr:eq(1)').show();
					p.$w.find('[name=nomb]').html( data.nomb );
					p.$w.find('[name=apell]').html( data.appat + ' ' + data.apmat );
				}else{
					p.$w.find('tr:eq(1)').hide();
					p.$w.find('[name=nomb]').html( data.nomb );
				}
				$.post('cm/espa/espa_prop',{_id: data._id.$id},function(data){
					var $cbo = p.$w.find('[name=espacio]');
					for(var i=0,j=data.length; i<j; i++){
						$cbo.append('<option value="'+data[i]._id.$id+'">'+data[i].nomb+'</option>');
						$cbo.find('option:last').data('data',data[i]);
					}
					$cbo.change(function(){
						p.payment.loadData();
					}).change();
					var tmp_serv = {};
					tmp_serv = p.config.CONV;
					p.$w.find('[name=section4] [name^=fecven]').val(ciHelper.dateFormatNowBDNotHour());
					p.$w.find('[name=section4] [name^=serv]').html('').removeData('data');
					p.$w.find('[name=section4] [id^=tabsConcPayment] .gridBody').empty();
					$.post('cj/conc/get_serv','id='+tmp_serv._id.$id,function(concs){
						if(concs.serv==null){
							return K.notification({title: 'Servicio inv&aacute;lido',text: 'El servicio seleccionado no tiene conceptos asociados!',type: 'error'});
						}
						p.$w.data('vars',concs.vars);
						p.$w.find('[name=section4] [name^=serv]').html(tmp_serv.nomb).data('data',tmp_serv).data('concs',concs.serv);
						p.$w.find('[name=section4] [name^=fecven]').change();
					},'json');
				},'json');
			}
		});
		if(p.entidad!=null)
			p.windId = 'windowConver'+p.entidad._id.$id;
		else
			p.windId = 'windowConver';
		new K.Modal({
			id: p.windId,
			title: 'Conversi&oacute;n de Nicho Temporal a Permanente',
			icon: 'ui-icon-bookmark',
			width: 675,
			height: 370,
			contentURL: 'cm/conc/conver',
			store: false,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {},
					propietario = p.$w.find('[name=nomb]').data('data');
					if(p.$w.find('[name=nomb]').html() == ""){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: "Seleccione un propietario!",
							type:"error"
						});
					}else
						data.propietario = ciHelper.enti.dbRel(propietario);
					var espacio = p.$w.find('[name=espacio] option:selected').data('data');
					data.espacio = {
						_id: espacio._id.$id,
						nomb: espacio.nomb
 					};
					data.conversion = {
						propietario_antiguo: data.propietario
					};
					if(data.conversion.propietario_nuevo!=null){
						data.conversion.propietario_nuevo = p.$w.find('[name=new_prop]').data('data');
					}
					var extra = p.payment.getPay();
					if(extra!=false&&extra!=10) $.extend(data,extra);
					else if(extra==false) return false;
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/oper/save_conver",data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						cmCuen.init();
						K.notification({title: 'Operaci&oacute;n Guardada',text: "La Operaci&oacute;n se guard&oacute; exitosamente!"});
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#'+p.windId);
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
				});
				p.payment = new cmOper.payment({
					$w: p.$w,
					getEspa: function(){
						return p.$w.find('[name=espacio] option:selected').data('data');
					}
				});
				p.$w.find('[name=btnBusPro]').click(function(){
					//ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbGestor,filter: p.filterGestor});
					mgEnti.windowSelect({callback: p.cbGestor,filter: p.filterGestor});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnNewPro]').remove();/*.click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbGestor,filter: p.filterGestor});
				}).button({icons: {primary: 'ui-icon-plusthick'}});*/
				p.$w.find('[name=btnBus]').click(function(){
					/*ciSearch.windowSearchEnti({$window: p.$w,callback: function(data){
						p.$w.find('[name=new_prop]').html(ciHelper.enti.formatName(data)).data('data',data);
						p.$w.find('[name=btnBus]').button('option','text',false);
						p.$w.find('[name=btnNew]').button('option','text',false);
					}});*/
					mgEnti.windowSelect({callback: function(data){
						p.$w.find('[name=new_prop]').html(ciHelper.enti.formatName(data)).data('data',data);
						p.$w.find('[name=btnBus]').button('option','text',false);
						p.$w.find('[name=btnNew]').button('option','text',false);
					}});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnNew]').remove();/*.click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: function(data){
						p.$w.find('[name=new_prop]').html(ciHelper.enti.formatName(data)).data('data',data);
						p.$w.find('[name=btnBus]').button('option','text',false);
						p.$w.find('[name=btnNew]').button('option','text',false);
					}});
				}).button({icons: {primary: 'ui-icon-plusthick'}});*/
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
				p.$w.find('[name=fecven_pag]').closest('tr').show();
				$.post('cj/cuen/get_config_ceme',function(data){
					p.config = data;
					if(p.entidad!=null){
						p.$w.find('td:first').hide();
						p.cbGestor(p.entidad);
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowNewHist: function(p){
		new K.Modal({
			id: "windowCmHistOperEdit"+p.id,
			title: 'Ingresar Registro Historico',
			icon: 'ui-icon-bookmark',
			width: 675,
			height: 370,
			contentURL: 'cm/oper/hist',
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {};		
					data.espacio = {_id:p.id};
					var tipo = p.$w.find('[name=tipo] :selected').val();
					data.tipo = tipo;
					p.$t = p.$w.find('#'+tipo);
					if(tipo=="CS"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.condicion = p.$t.find('[name=cond]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
					}else if(tipo=="CT"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
						data.capacidad = p.$t.find('[name=capa]').val();
						data.largo = p.$t.find('[name=larg]').val();
						data.ancho = p.$t.find('[name=anch]').val();
						data.altura1 = p.$t.find('[name=alt1]').val();
						data.altura2 = p.$t.find('[name=alt2]').val();
					}else if(tipo=="AS"||tipo=="AD"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
					}else if(tipo=="TP"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
						data.nuevo_propietario = p.$t.find('[name=new_prop]').val();
					}else if(tipo=="IN"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.feceje = p.$t.find('[name=feceje]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.temporalidad = p.$t.find('[name=temporalidad] option:selected').val();
						data.empaste = p.$t.find('[name=empaste] option:selected').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
						data.dniocupante = p.$t.find('[name=dniocup]').val();
						data.ocupante2 = p.$t.find('[name=ocup2]').val();
						data.dniocupante2 = p.$t.find('[name=dniocup2]').val();
						data.fecdef = p.$t.find('[name=fecdef]').val();
						data.fecven = p.$t.find('[name=fecven]').val();
						data.edad = p.$t.find('[name=edad]').val();
						data.causa = p.$t.find('[name=causa]').val();
						data.partdef = p.$t.find('[name=part]').val();
						data.municipalidad = p.$t.find('[name=muni]').val();
						data.funeraria = p.$t.find('[name=fune]').val();
					}else if(tipo=="TI"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
						data.espacio_destino = p.$t.find('[name=espacio_dest]').data('data');
						if(data.espacio_destino!=null) data.espacio_destino = cmEspa.dbRel(data.espacio_destino);
					}else if(tipo=="TE"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
						data.cementerio = p.$t.find('[name=ceme]').val();
						data.ubicacion = p.$t.find('[name=ubic]').val();
					}else if(tipo=="CO"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
						data.ocupante2 = p.$t.find('[name=ocup2]').val();
						data.accesorios = p.$t.find('[name=acce]').val();
					}else if(tipo=="CV"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
					}else if(tipo=="TEO"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
						data.cementerio = p.$t.find('[name=cement_orig]').val();
						data.ubicacion = p.$t.find('[name=ubic_orig]').val();
					}else if(tipo=="CN"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.recibo_origen = p.$t.find('[name=recibo_origen]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
						data.dniocupante = p.$t.find('[name=dni_ocup]').val();
					}
					data.recibo = p.$t.find('[name=recibo]').val();
					data.referencia = p.$t.find('[name=referencia]').val();
					//console.log(data);
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/oper/save_hist",data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						//$('#pageWrapperLeft .ui-state-highlight').click();
						if($('#windowDetailsNicho'+p.id).length!=0)
							K.closeWindow('windowDetailsNicho'+p.id);
						if($('#windowDetailsMauso'+p.id).length!=0)
							K.closeWindow('windowDetailsMauso'+p.id);
						if($('#windowDetailsTumba'+p.id).length!=0)
							K.closeWindow('windowDetailsTumba'+p.id);
						cmEspa.showDetailsEspa({id: p.id});
						K.notification({title: 'Registro Guardado',text: "El Rregistro se guard&oacute; exitosamente!"});
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowCmHistOperEdit'+p.id);
				K.block({$element: p.$w});
				p.$w.find('[name=fecven]').datepicker();
				p.$w.find('[name=feceje]').datetimepicker();
				p.$w.find('[name=fecoper]').datepicker();
				p.$w.find('[name=fecdef]').datepicker();
				p.$w.find('[name=tipo]').change(function(){
					p.$w.find('#CS,#CT,#AS,#AD,#TP,#IN,#TI,#TE,#CO,#CV,#CN').hide();
					//p.$w.find('[name=tipo] :selected').val();
					p.$w.find('#'+$(this).val()).show();
				}).change();
				p.$w.find('[name=btnDest]').click(function(){
					cmEspa.windowSelect({
						callback: function(data){
							p.$w.find('[name=espacio_dest]').html(data.nomb).data('data',data);
						}
					});
				}).button({icons: {primary: 'ui-plusthick'}});
				p.$w.find('[name=btnDig]').click(function(){
					cmOper.windowDigi({
						callback: function(data){
							console.log(data);
							//p.$w.find('[name=link]').html(data.mediaLink).data('data',data);
						}
					});
				}).button({icons: {primary: 'ui-plusthick'}});
				K.unblock({$element: p.$w});
			}
		});
	},
	windowDigi: function(p){
		new K.Modal({
			id:'windowDigi',
			title: 'Subir imagen',
			icon: 'ui-icon-folder',
			contentURL: 'cm/pabe/upload',
			width: 470,
			height: 220,
			button: {
				'Cancelar': function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){p = null;},
			onContentLoaded: function(){
				p.$w = $('#windowDigi');
				var uploader = new qq.FileUploader({
					element: document.getElementById('buttonUpload'),
					action: 'ci/files/upload_digi',
					debug: false,
					sizeLimit: 2097152,
					allowedExtensions: ['jpg','gif','png'],
					fieldFile: 'foto',
					onSubmit: function(){
						p.$w.find('.img-picture').fadeTo("slow", 0.33);
					},
					onprogress: function(){
					},
					onComplete: function(id,fileName,responseJSON){
						console.log(id);
						console.log(fileName);
						console.log(responseJSON);

					}
					
				});
				
			}
		});
	},
	windowEditHist: function(p){
		new K.Modal({
			id: "windowCmHistOperEdit"+p.id,
			title: 'Editar Registro Historico',
			icon: 'ui-icon-bookmark',
			width: 675,
			height: 370,
			contentURL: 'cm/oper/hist',
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {_id: p.id};
					var tipo = p.$w.find('[name=tipo] :selected').val();
					data.tipo = tipo;
					p.$t = p.$w.find('#'+tipo);
					if(tipo=="CS"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.condicion = p.$t.find('[name=cond]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
					}else if(tipo=="CT"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
						data.capacidad = p.$t.find('[name=capa]').val();
						data.largo = p.$t.find('[name=larg]').val();
						data.ancho = p.$t.find('[name=anch]').val();
						data.altura1 = p.$t.find('[name=alt1]').val();
						data.altura2 = p.$t.find('[name=alt2]').val();
					}else if(tipo=="AS"||tipo=="AD"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
					}else if(tipo=="TP"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
						data.nuevo_propietario = p.$t.find('[name=new_prop]').val();
					}else if(tipo=="IN"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.feceje = p.$t.find('[name=feceje]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.temporalidad = p.$t.find('[name=temporalidad] option:selected').val();
						data.empaste = p.$t.find('[name=empaste] option:selected').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
						data.dniocupante = p.$t.find('[name=dniocup]').val();
						data.ocupante2 = p.$t.find('[name=ocup2]').val();
						data.dniocupante2 = p.$t.find('[name=dniocup2]').val();
						data.fecdef = p.$t.find('[name=fecdef]').val();
						data.fecven = p.$t.find('[name=fecven]').val();
						data.edad = p.$t.find('[name=edad]').val();
						data.causa = p.$t.find('[name=causa]').val();
						data.partdef = p.$t.find('[name=part]').val();
						data.municipalidad = p.$t.find('[name=muni]').val();
						data.funeraria = p.$t.find('[name=fune]').val();
					}else if(tipo=="TI"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
						data.espacio_destino = p.$t.find('[name=espacio_dest]').data('data');
						if(data.espacio_destino!=null) data.espacio_destino = cmEspa.dbRel(data.espacio_destino);
					}else if(tipo=="TE"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
						data.cementerio = p.$t.find('[name=ceme]').val();
						data.ubicacion = p.$t.find('[name=ubic]').val();
					}else if(tipo=="CO"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
						data.ocupante2 = p.$t.find('[name=ocup2]').val();
						data.accesorios = p.$t.find('[name=acce]').val();
					}else if(tipo=="CV"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
					}else if(tipo=="TEO"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
						data.cementerio = p.$t.find('[name=cement_orig]').val();
						data.ubicacion = p.$t.find('[name=ubic_orig]').val();
					}else if(tipo=="CN"){
						data.fecoper = p.$t.find('[name=fecoper]').val();
						data.propietario = p.$t.find('[name=prop]').val();
						data.dni_prop = p.$t.find('[name=dni_prop]').val();
						data.recibo_origen = p.$t.find('[name=recibo_origen]').val();
						data.ocupante = p.$t.find('[name=ocup]').val();
						data.dniocupante = p.$t.find('[name=dni_ocup]').val();
					}
					data.recibo = p.$t.find('[name=recibo]').val();
					data.referencia = p.$t.find('[name=referencia]').val();
					//console.log(data);
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/oper/save_hist",data,function(){
						K.clearNoti();
						K.closeWindow(p.$w.attr('id'));
						$('#pageWrapperLeft .ui-state-highlight').click();
						if($('#windowDetailsNicho'+p.id).length!=0)
							K.closeWindow('windowDetailsNicho'+p.id);
						if($('#windowDetailsMauso'+p.id).length!=0)
							K.closeWindow('windowDetailsMauso'+p.id);
						if($('#windowDetailsTumba'+p.id).length!=0)
							K.closeWindow('windowDetailsTumba'+p.id);
						cmEspa.showDetailsEspa({id: p.id});
						K.notification({title: 'Registro Guardado',text: "El Rregistro se guard&oacute; exitosamente!"});
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowCmHistOperEdit'+p.id);
				p.$w.find('[name=fecven]').datepicker();
				p.$w.find('[name=feceje]').datetimepicker();
				p.$w.find('[name=fecoper]').datepicker();
				p.$w.find('[name=fecdef]').datepicker();
				p.$w.find('[name=tipo]').change(function(){
					p.$w.find('#CS,#CT,#AS,#AD,#TP,#IN,#TI,#TE,#CO,#CV,#CN').hide();
					//p.$w.find('[name=tipo] :selected').val();
					p.$w.find('#'+$(this).val()).show();
				});
				p.$w.find('[name=btnDest]').click(function(){
					cmEspa.windowSelect({
						callback: function(data){
							p.$w.find('[name=espacio_dest]').html(data.nomb).data('data',data);
						}
					});
				}).button({icons: {primary: 'ui-plusthick'}});
				K.block({$element: p.$w});
				$.post('cm/oper/get_hist',{id:p.id},function(data){
					p.$w.find('#'+data.tipo).show();
					p.$w.find('[name=tipo]').find('[value='+data.tipo+']').attr('selected','selected');
					//p.$w.find('[name=tipo]').attr('disabled','disabled');
					p.$t = p.$w.find('#'+data.tipo);
					p.$t.find('[name=recibo]').val(data.recibo);
					if(data.referencia==null) data.referencia = "";
					p.$t.find('[name=referencia]').val(data.referencia);
					if(data.tipo=="CS"){
						p.$t.find('[name=fecoper]').val(ciHelper.date.format.bd_ymd(data.fecoper));
						p.$t.find('[name=prop]').val(data.propietario);
						p.$t.find('[name=cond]').val(data.condicion);
						if(data.ocupante!=null){
							p.$t.find('[name=ocup]').val(data.ocupante);
						}
					}else if(data.tipo=="CT"){
						p.$t.find('[name=fecoper]').val(ciHelper.date.format.bd_ymd(data.fecoper));
						p.$t.find('[name=prop]').val(data.propietario);
						p.$t.find('[name=ocup]').val(data.ocupante);
						p.$t.find('[name=capa]').val(data.capacidad);
						p.$t.find('[name=larg]').val(data.largo);
						p.$t.find('[name=anch]').val(data.ancho);
						p.$t.find('[name=alt1]').val(data.altura1);
						p.$t.find('[name=alt1]').val(data.altura2);
					}else if(data.tipo=="AS"||data.tipo=="AD"){
						p.$t.find('[name=fecoper]').val(ciHelper.date.format.bd_ymd(data.fecoper));
						p.$t.find('[name=prop]').val(data.propietario);
						p.$t.find('[name=ocup]').val(data.ocupante);
					}else if(data.tipo=="TP"){
						p.$t.find('[name=fecoper]').val(ciHelper.date.format.bd_ymd(data.fecoper));
						p.$t.find('[name=prop]').val(data.propietario);
						p.$t.find('[name=ocup]').val(data.ocupante);
						p.$t.find('[name=new_prop]').val(data.nuevo_propietario);
					}else if(data.tipo=="IN"){
						p.$t.find('[name=fecoper]').val(ciHelper.date.format.bd_ymd(data.fecoper));
						p.$t.find('[name=prop]').val(data.propietario);
						p.$t.find('[name=ocup]').val(data.ocupante);
						if(data.ocupante2!=null)
							p.$t.find('[name=ocup2]').val(data.ocupante2);
						else
							p.$t.find('[name=ocup2]').val('');
						if(data.dniocupante!=null)
							p.$t.find('[name=dniocup]').val(data.dniocupante);
						else
							p.$t.find('[name=dniocup]').val('');
						if(data.dniocupante2!=null)
							p.$t.find('[name=dniocup2]').val(data.dniocupante2);
						else
							p.$t.find('[name=dniocup2]').val('');
						if(data.fecdef!='')
							p.$t.find('[name=fecdef]').val(ciHelper.date.format.bd_ymd(data.fecdef));
						else
							p.$t.find('[name=fecdef]').val('');
						p.$t.find('[name=edad]').val(data.edad);
						p.$t.find('[name=causa]').val(data.causa);
						p.$t.find('[name=part]').val(data.partdef);
						p.$t.find('[name=muni]').val(data.municipalidad);
						p.$t.find('[name=fune]').val(data.funeraria);
						if(data.temporalidad!=null){
							p.$t.find('[name=temporalidad]').selectVal(data.temporalidad);
						}
						if(data.empaste!=null){
							p.$t.find('[name=empaste]').selectVal(data.empaste);
						}
						if(data.fecven!=null){
							p.$t.find('[name=fecven]').val(data.fecven);
						}else p.$t.find('[name=fecven]').val('');
						if(data.feceje!=null){
							p.$t.find('[name=feceje]').val(data.feceje);
						}else p.$t.find('[name=feceje]').val('');
					}else if(data.tipo=="TI"){
						if(data.fecoper.sec!=null)
							p.$t.find('[name=fecoper]').val(ciHelper.date.format.bd_ymd(data.fecoper));
						else
							p.$t.find('[name=fecoper]').val(data.fecoper);
						p.$t.find('[name=prop]').val(data.propietario);
						p.$t.find('[name=ocup]').val(data.ocupante);
						if(data.espacio_destino._id!=null){
							p.$w.find('[name=espacio_dest]').html(data.espacio_destino.nomb).data('data',data.espacio_destino);
						}else
							p.$w.find('[name=espacio_dest]').html(data.espacio_destino);
					}else if(data.tipo=="TE"){
						if(data.fecoper.sec!=null)
							p.$t.find('[name=fecoper]').val(ciHelper.date.format.bd_ymd(data.fecoper));
						else
							p.$t.find('[name=fecoper]').val(data.fecoper);
						p.$t.find('[name=prop]').val(data.propietario);
						p.$t.find('[name=ocup]').val(data.ocupante);
						p.$t.find('[name=ceme]').val(data.cementerio);
						p.$t.find('[name=ubic]').val(data.ubicacion);
					}else if(data.tipo=="CO"){
						if(data.fecoper.sec!=null)
							p.$t.find('[name=fecoper]').val(ciHelper.date.format.bd_ymd(data.fecoper));
						else
							p.$t.find('[name=fecoper]').val(data.fecoper);
						p.$t.find('[name=prop]').val(data.propietario);
						p.$t.find('[name=ocup]').val(data.ocupante);
						if(data.ocupante2!=null)
							p.$t.find('[name=ocup2]').val(data.ocupante2);
						else
							p.$t.find('[name=ocup2]').val('');
						p.$t.find('[name=acce]').val(data.accesorios);
					}else if(data.tipo=="CV"){
						if(data.fecoper.sec!=null)
							p.$t.find('[name=fecoper]').val(ciHelper.date.format.bd_ymd(data.fecoper));
						else
							p.$t.find('[name=fecoper]').val(data.fecoper);
						p.$t.find('[name=prop]').val(data.propietario);
						p.$t.find('[name=ocup]').val(data.ocupante);
					}else if(data.tipo=="TEO"){
						if(data.fecoper.sec!=null)
							p.$t.find('[name=fecoper]').val(ciHelper.date.format.bd_ymd(data.fecoper));
						else
							p.$t.find('[name=fecoper]').val(data.fecoper);
						p.$t.find('[name=prop]').val(data.propietario);
						p.$t.find('[name=ocup]').val(data.ocupante);
						p.$t.find('[name=cement_orig]').val(data.cementerio);
						p.$t.find('[name=ubic_orig]').val(data.ubicacion);
					}else if(data.tipo=="CN"){
						if(data.fecoper.sec!=null)
							p.$t.find('[name=fecoper]').val(ciHelper.date.format.bd_ymd(data.fecoper));
						else
							p.$t.find('[name=fecoper]').val(data.fecoper);
						p.$t.find('[name=prop]').val(data.propietario);
						p.$t.find('[name=recibo_origen]').val(data.recibo_origen);
						if(data.dniocupante!=null)
							p.$t.find('[name=dni_ocup]').val(data.dniocupante);
					}
					if(data.referencia!=null){
						p.$t.find('[name=referencia]').val(data.referencia);
					}
					if(data.dni_prop!=null){
						p.$t.find('[name=dni_prop]').val(data.dni_prop);
					}
					if(data.ocupante!=null){
						p.$t.find('[name=ocup]').val(data.ocupante);
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowHistDetails: function(p){
		new K.Window({
			id: "windowCmHistOperDetails"+p.id,
			title: 'Registro Historico',
			icon: 'ui-icon-bookmark',
			width: 675,
			height: 300,
			//header: false,
			contentURL: 'cm/oper/hist',
			store: false,
			buttons: {
				"Editar": function(){
					cmOper.windowEditHist({id: p.id});
					K.closeWindow(p.$w.attr('id'));
				},
				"Cerrar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onContentLoaded: function(){
				p.$w = $('#windowCmHistOperDetails'+p.id);
				K.block({$element: p.$w});
				$.post('cm/oper/get_hist',{id:p.id},function(data){
					p.$w.find('#'+data.tipo).show();
					p.$w.find('[name=tipo]').closest('tbody').append('<tr>'+
						(data.trabajador==null?'':'<td>Registrado por <i>'+mgEnti.formatName(data.trabajador)+'</i></td>')+
						'<td>Modificado el '+ciHelper.dateFormatLong(data.fecreg)+'</td>'+
					'</tr>');
					p.$w.find('[name=tipo]').find('[value='+data.tipo+']').attr('selected','selected');
					p.$w.find('[name=tipo]').attr('disabled','disabled');
					p.$t = p.$w.find('#'+data.tipo);
					p.$t.find('[name=recibo]').replaceWith(data.recibo);
					if(data.referencia==null) data.referencia = "";
					p.$t.find('[name=referencia]').replaceWith(data.referencia);
					if(data.tipo=="CS"){
						p.$t.find('[name=fecoper]').replaceWith(ciHelper.dateFormatLong(data.fecoper));
						p.$t.find('[name=prop]').replaceWith(data.propietario);
						p.$t.find('[name=cond]').replaceWith(data.condicion);
						if(data.url_imagen!=null){
							var displayText	= 'Ver imagen';
							var url = data.url_imagen;
							p.$t.find('[name=url_imagen]').replaceWith("<tr><td><label><strong>Digitalizado: </strong></label></td><td><a href='" + url + "' target='_blank'><u>" + displayText + "</u></a></td></tr>");
						}else{
							p.$t.find('[name=url_imagen]').html("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
						}
						if(data.ocupante!=null){
							p.$t.find('[name=ocup]').replaceWith(data.ocupante);
						}else
							p.$t.find('[name=ocup]').replaceWith('');
					}else if(data.tipo=="CT"){
						p.$t.find('[name=fecoper]').replaceWith(ciHelper.dateFormatLong(data.fecoper));
						p.$t.find('[name=prop]').replaceWith(data.propietario);
						p.$t.find('[name=ocup]').replaceWith(data.ocupante);
						p.$t.find('[name=capa]').replaceWith(data.capacidad);
						p.$t.find('[name=larg]').replaceWith(data.largo);
						p.$t.find('[name=anch]').replaceWith(data.ancho);
						p.$t.find('[name=alt1]').replaceWith(data.altura1);
						p.$t.find('[name=alt1]').replaceWith(data.altura2);
						if(data.url_imagen!=null){
							var displayText	= 'Ver imagen';
							var url = data.url_imagen;
							p.$t.find('[name=url_imagen]').replaceWith("<tr><td><label><strong>Digitalizado: </strong></label></td><td><a href='" + url + "' target='_blank'><u>" + displayText + "</u></a></td></tr>");
						}else{
							p.$t.find('[name=url_imagen]').html("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
						}
					}else if(data.tipo=="AS"||data.tipo=="AD"){
						p.$t.find('[name=fecoper]').replaceWith(ciHelper.dateFormatLong(data.fecoper));
						p.$t.find('[name=prop]').replaceWith(data.propietario);
						p.$t.find('[name=ocup]').replaceWith(data.ocupante);
						if(data.ocupante2!=null)
							p.$t.find('[name=ocup2]').replaceWith(data.ocupante2);
						else
							p.$t.find('[name=ocup2]').replaceWith('');
						if(data.dniocupante!=null)
							p.$t.find('[name=dniocup]').replaceWith(data.dniocupante);
						else
							p.$t.find('[name=dniocup]').replaceWith('');
						if(data.dniocupante2!=null)
							p.$t.find('[name=dniocup2]').replaceWith(data.dniocupante2);
						else
							p.$t.find('[name=dniocup2]').replaceWith('');
						if(data.url_imagen!=null){
							var displayText	= 'Ver imagen';
							var url = data.url_imagen;
							p.$t.find('[name=url_imagen]').replaceWith("<tr><td><label><strong>Digitalizado: </strong></label></td><td><a href='" + url + "' target='_blank'><u>" + displayText + "</u></a></td></tr>");
						}else{
							p.$t.find('[name=url_imagen]').html("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
						}
					}else if(data.tipo=="TP"){
						p.$t.find('[name=fecoper]').replaceWith(ciHelper.dateFormatLong(data.fecoper));
						p.$t.find('[name=prop]').replaceWith(data.propietario);
						p.$t.find('[name=ocup]').replaceWith(data.ocupante);
						p.$t.find('[name=new_prop]').replaceWith(data.nuevo_propietario);
						if(data.url_imagen!=null){
							var displayText	= 'Ver imagen';
							var url = data.url_imagen;
							p.$t.find('[name=url_imagen]').replaceWith("<tr><td><label><strong>Digitalizado: </strong></label></td><td><a href='" + url + "' target='_blank'><u>" + displayText + "</u></a></td></tr>");
						}else{
							p.$t.find('[name=url_imagen]').html("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
						}
					}else if(data.tipo=="IN"){
						p.$t.find('[name=fecoper]').replaceWith(ciHelper.dateFormatLong(data.fecoper));
						p.$t.find('[name=prop]').replaceWith(data.propietario);
						p.$t.find('[name=ocup]').replaceWith(data.ocupante);
						if(data.ocupante2!=null)
							p.$t.find('[name=ocup2]').replaceWith(data.ocupante2);
						else
							p.$t.find('[name=ocup2]').replaceWith('');
						if(data.dniocupante!=null)
							p.$t.find('[name=dniocup]').replaceWith(data.dniocupante);
						else
							p.$t.find('[name=dniocup]').replaceWith('');
						if(data.dniocupante2!=null)
							p.$t.find('[name=dniocup2]').replaceWith(data.dniocupante2);
						else
							p.$t.find('[name=dniocup2]').replaceWith('');
						if(data.fecdef!='')
							p.$t.find('[name=fecdef]').replaceWith(ciHelper.dateFormatLong(data.fecdef));
						else
							p.$t.find('[name=fecdef]').replaceWith('');
						p.$t.find('[name=edad]').replaceWith(data.edad);
						p.$t.find('[name=causa]').replaceWith(data.causa);
						p.$t.find('[name=part]').replaceWith(data.partdef);
						p.$t.find('[name=muni]').replaceWith(data.municipalidad);
						p.$t.find('[name=fune]').replaceWith(data.funeraria);
						if(data.temporalidad!=null){
							if(data.temporalidad=='P')
								p.$t.find('[name=temporalidad]').replaceWith('Permanente');
							else
								p.$t.find('[name=temporalidad]').replaceWith('Temporal');
						}else
							p.$t.find('[name=temporalidad]').replaceWith('');
						if(data.empaste!=null){
							if(data.empaste=='0')
								p.$t.find('[name=empaste]').replaceWith('No');
							else
								p.$t.find('[name=empaste]').replaceWith('Si');
						}else
							p.$t.find('[name=empaste]').replaceWith('');
						if(data.fecven!=null){
							p.$t.find('[name=fecven]').replaceWith(data.fecven);
						}else p.$t.find('[name=fecven]').replaceWith('');
						if(data.feceje!=null){
							p.$t.find('[name=feceje]').replaceWith(data.feceje);
						}else p.$t.find('[name=feceje]').replaceWith('');
						if(data.url_imagen!=null){
							var displayText	= 'Ver imagen';
							var url = data.url_imagen;
							p.$t.find('[name=url_imagen]').replaceWith("<tr><td><label><strong>Digitalizado: </strong></label></td><td><a href='" + url + "' target='_blank'><u>" + displayText + "</u></a></td></tr>");
						}else{
							p.$t.find('[name=url_imagen]').html("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
						}
					}else if(data.tipo=="TI"){
						if(data.fecoper.sec!=null)
							p.$t.find('[name=fecoper]').replaceWith(ciHelper.dateFormatLong(data.fecoper));
						else
							p.$t.find('[name=fecoper]').replaceWith(data.fecoper);
						p.$t.find('[name=prop]').replaceWith(data.propietario);
						p.$t.find('[name=ocup]').replaceWith(data.ocupante);
						if(data.espacio_destino._id!=null){
							p.$w.find('[name=espacio_dest]').html(data.espacio_destino.nomb);
						}
						if(data.url_imagen!=null){
							var displayText	= 'Ver imagen';
							var url = data.url_imagen;
							p.$t.find('[name=url_imagen]').replaceWith("<tr><td><label><strong>Digitalizado: </strong></label></td><td><a href='" + url + "' target='_blank'><u>" + displayText + "</u></a></td></tr>");
						}else{
							p.$t.find('[name=url_imagen]').html("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
						}
					}else if(data.tipo=="TE"){
						if(data.fecoper.sec!=null)
							p.$t.find('[name=fecoper]').replaceWith(ciHelper.dateFormatLong(data.fecoper));
						else
							p.$t.find('[name=fecoper]').replaceWith(data.fecoper);
						p.$t.find('[name=prop]').replaceWith(data.propietario);
						p.$t.find('[name=ocup]').replaceWith(data.ocupante);
						p.$t.find('[name=ceme]').replaceWith(data.cementerio);
						p.$t.find('[name=ubic]').replaceWith(data.ubicacion);
						if(data.url_imagen!=null){
							var displayText	= 'Ver imagen';
							var url = data.url_imagen;
							p.$t.find('[name=url_imagen]').replaceWith("<tr><td><label><strong>Digitalizado: </strong></label></td><td><a href='" + url + "' target='_blank'><u>" + displayText + "</u></a></td></tr>");
						}else{
							p.$t.find('[name=url_imagen]').html("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
						}
					}else if(data.tipo=="CO"){
						if(data.fecoper.sec!=null)
							p.$t.find('[name=fecoper]').replaceWith(ciHelper.dateFormatLong(data.fecoper));
						else
							p.$t.find('[name=fecoper]').replaceWith(data.fecoper);
						p.$t.find('[name=prop]').replaceWith(data.propietario);
						p.$t.find('[name=ocup]').replaceWith(data.ocupante);
						if(data.ocupante2!=null)
							p.$t.find('[name=ocup2]').replaceWith(data.ocupante2);
						else
							p.$t.find('[name=ocup2]').replaceWith('');
						p.$t.find('[name=acce]').replaceWith(data.accesorios);
						if(data.url_imagen!=null){
							var displayText	= 'Ver imagen';
							var url = data.url_imagen;
							p.$t.find('[name=url_imagen]').replaceWith("<tr><td><label><strong>Digitalizado: </strong></label></td><td><a href='" + url + "' target='_blank'><u>" + displayText + "</u></a></td></tr>");
						}else{
							p.$t.find('[name=url_imagen]').html("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
						}
					}else if(data.tipo=="CV"){
						if(data.fecoper.sec!=null)
							p.$t.find('[name=fecoper]').replaceWith(ciHelper.dateFormatLong(data.fecoper));
						else
							p.$t.find('[name=fecoper]').replaceWith(data.fecoper);
						p.$t.find('[name=prop]').replaceWith(data.propietario);
						p.$t.find('[name=ocup]').replaceWith(data.ocupante);
						if(data.url_imagen!=null){
							var displayText	= 'Ver imagen';
							var url = data.url_imagen;
							p.$t.find('[name=url_imagen]').replaceWith("<tr><td><label><strong>Digitalizado: </strong></label></td><td><a href='" + url + "' target='_blank'><u>" + displayText + "</u></a></td></tr>");
						}else{
							p.$t.find('[name=url_imagen]').html("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
						}
					}else if(data.tipo=="TEO"){
						if(data.fecoper.sec!=null)
							p.$t.find('[name=fecoper]').replaceWith(ciHelper.dateFormatLong(data.fecoper));
						else
							p.$t.find('[name=fecoper]').replaceWith(data.fecoper);
						p.$t.find('[name=prop]').replaceWith(data.propietario);
						p.$t.find('[name=ocup]').replaceWith(data.ocupante);
						p.$t.find('[name=cement_orig]').replaceWith(data.cementerio);
						p.$t.find('[name=ubic_orig]').replaceWith(data.ubicacion);
						if(data.url_imagen!=null){
							var displayText	= 'Ver imagen';
							var url = data.url_imagen;
							p.$t.find('[name=url_imagen]').replaceWith("<tr><td><label><strong>Digitalizado: </strong></label></td><td><a href='" + url + "' target='_blank'><u>" + displayText + "</u></a></td></tr>");
						}else{
							p.$t.find('[name=url_imagen]').html("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
						}
					}else if(data.tipo=="CN"){
						if(data.fecoper.sec!=null)
							p.$t.find('[name=fecoper]').replaceWith(ciHelper.dateFormatLong(data.fecoper));
						else
							p.$t.find('[name=fecoper]').replaceWith(data.fecoper);
						p.$t.find('[name=prop]').replaceWith(data.propietario);
						p.$t.find('[name=recibo_origen]').replaceWith(data.recibo_origen);
						if(data.dniocupante!=null)
							p.$t.find('[name=fecoper]').replaceWith(data.dniocupante);
						else
							p.$t.find('[name=fecoper]').replaceWith('');
							if(data.url_imagen!=null){
								var displayText	= 'Ver imagen';
								var url = data.url_imagen;
								p.$t.find('[name=url_imagen]').replaceWith("<tr><td><label><strong>Digitalizado: </strong></label></td><td><a href='" + url + "' target='_blank'><u>" + displayText + "</u></a></td></tr>");
							}else{
								p.$t.find('[name=url_imagen]').html("<tr><td><label>No Digitalizado</label></td><td></td></tr>");
							}
					}
					if(data.referencia!=null){
						p.$t.find('[name=referencia]').replaceWith(data.referencia);
					}
					if(data.dni_prop!=null){
						p.$t.find('[name=dni_prop]').replaceWith(data.dni_prop);
					}else
						p.$t.find('[name=dni_prop]').replaceWith('');
					if(data.ocupante!=null){
						p.$t.find('[name=ocup]').replaceWith(data.ocupante);
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowNewTrasladoExt: function(p){
		if(p==null) p = {};
		p.cbCementerio = function(data){
			p.$w.find('[name=cementerio]').data('data',data);
			p.$w.find('[name=cementerio]').html( data.nomb );
		};
		p.cbGestor = function(data){
			p.$w.find('[name=section1] [name=nomb]').data('data',data);
			if(data.tipo_enti=='P'){
				p.$w.find('[name=section1] tr:eq(1)').show();
				p.$w.find('[name=section1] [name=nomb]').html( data.nomb );
				p.$w.find('[name=section1] [name=apell]').html( data.appat + ' ' + data.apmat );
			}else{
				p.$w.find('[name=section1] tr:eq(1)').hide();
				p.$w.find('[name=section1] [name=nomb]').html( data.nomb );
			}
		};
		p.cbOcup = function(data){
			p.$w.find('[name=section2] [name=nomb]').data('data',data);
			if(data.tipo_enti=='P'){
				p.$w.find('[name=section2] tr:eq(1)').show();
				p.$w.find('[name=section2] [name=nomb]').html( data.nomb );
				p.$w.find('[name=section2] [name=apell]').html( data.appat + ' ' + data.apmat );
			}else{
				p.$w.find('[name=section2] tr:eq(1)').hide();
				p.$w.find('[name=section2] [name=nomb]').html( data.nomb );
			}
		};
		p.cbEspacio = function(data){
			var tipo = '';
			var espacio = p.$w.find('[name=det]');
			if(data!=null){
				if(data.nicho!=null){
					tipo = "Nicho-"+cmOper.statesNicho[data.nicho.tipo].descr;
					espacio.append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='"+data.actual+"'>"+tipo+"</span></td></tr>");
					espacio.append("<tr><td><label>Nombre del Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.nomb+"</td></tr>");
					espacio.append("<tr><td><label>N&uacute;mero de Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.num+"</td></tr>");
					espacio.append("<tr><td><label>N&uacute;mero de Nicho:</label></td><td>"+data.nicho.num+"</td></tr>");
					espacio.append("<tr><td><label>Piso:</label></td><td>"+data.nicho.piso+"</td></tr>");
					espacio.append("<tr><td><label>Fila:</label></td><td>"+data.nicho.fila+"</td></tr>");
				}
				else if(data.tumba!=null){
						tipo = "Tumba";
						espacio.append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='"+data.actual+"'>"+tipo+"</span></td></tr>");
						espacio.append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.tumba.denominacion+"</td></tr>");
					 }
					 else {
						tipo = "Mausoleo";
						espacio.append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='"+data.actual+"'>"+tipo+"</span></td></tr>");
						espacio.append("<tr><td><label>Tipo de Zona:</label></td><td>"+cmOper.statesZonaMaus[data.mausoleo.zona].descr+"</td></tr>");
						espacio.append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.mausoleo.denominacion+"</td></tr>");
						espacio.append("<tr><td><label>N&uacute;mero de Lote:</label></td><td>"+data.mausoleo.lote+"</td></tr>");
					 }
				p.$w.find('[name=det]').data('data',data);
				p.payment.loadData();
			}
		};
		if(p.entidad!=null){
			p.windId = 'windowNewTrasladoExt'+p.entidad._id.$id;
		}
		else
			p.windId = 'windowNewTrasladoExt';
		new K.Modal({
			id: p.windId,
			title: 'Nuevo Traslado Externo (desde otro cementerio)',
			icon: 'ui-icon-arrowrefresh-1-w',
			width: 700,
			height: 410,
			contentURL: 'cm/oper/tras_ext',
			store: false,
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {};
					if(p.$w.find('[name=section1] [name=nomb]').html() == ""){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: "Seleccione un propietario!",
							type:"error"
						});
					}
					var propietario = p.$w.find('[name=section1] [name=nomb]').data('data');
					data.propietario = ciHelper.enti.dbRel(propietario);
					if(p.$w.find('[name=section2] [name=nomb]').html() == ""){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: "Seleccione un ocupante!",
							type:"error"
						});
					}
					var ocupante = p.$w.find('[name=section2] [name=nomb]').data('data');
					data.ocupante = ciHelper.enti.dbRel(ocupante);
					var espacio = p.$w.find('[name=det]').data('data');
					if(espacio==null){
						p.$w.find('[name=btnSelEspacio]').click();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un espacio de destino!',
							type: 'error'
						});
					}
					data.espacio = {
						_id: espacio._id.$id,
						nomb: espacio.nomb
					};
					data.traslado_ext = {
						destino: data.espacio,
						origen: {
							cementerio: {},
							ubicacion: p.$w.find('[name=ubi]').val()
						}
					};
					if(data.traslado_ext.origen.ubicacion==''){
						p.$w.find('[name=ubi]').focus();
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar una ubicaci&oacute;n de origen!',
							type: 'error'
						});
					}
					if(p.$w.find('[name=cementerio]').html()!=""){
						var cementerio = p.$w.find('[name=cementerio]').data('data');
						data.traslado_ext.origen.cementerio = ciHelper.enti.dbRel(cementerio);
					}else{
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un cementerio de origen!',
							type: 'error'
						});
					}
					if(p.$w.find('[name=fecprog]').val() == ""){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: "Ingrese fecha programada!",
							type:"error"
						});
					}
					data.programacion = {
						fecprog: p.$w.find('[name=fecprog]').val(),
						observ: p.$w.find('[name=observ]').val()
					};
					var extra = p.payment.getPay();
					if(extra!=false&&extra!=10) $.extend(data,extra);
					else if(extra==false) return false;
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post("cm/oper/save_tras_ext",data,function(){
						K.clearNoti();
						cmCuen.init();
						K.closeWindow(p.$w.attr('id'));
						K.notification({title: 'Operaci&oacute;n Guardada',text: "La Operaci&oacute;n se guard&oacute; exitosamente!"});
					},'json');
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#'+p.windId);
				K.block({
					$element: p.$w,
					onUnblock: function(){
						p.$mainPanel.css('z-index',$.ui.dialog.maxZ);
						p.$leftPanel.css('z-index',$.ui.dialog.maxZ);
					}
				});
				p.$w.find('[name=btnBusPro]').click(function(){
					/*ciSearch.windowSearchEnti({$window: p.$w,callback: p.cbGestor,filter: [
					    {nomb: 'roles.propietario',value: {$exists: true}}
					]});*/
					mgEnti.windowSelect({callback: p.cbGestor,filter: [
					    //{nomb: 'roles.propietario',value: {$exists: true}}
					]});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnAgrOcu]').remove();/*.click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbOcup});
				}).button({icons: {primary: 'ui-icon-plusthick'}});*/
				p.$w.find('[name=btnSelEspacio]').click(function(){
					//cmEspa.windowElegir({$parent: p.$w,callBack: p.cbEspacio,prop: true});
					if(p.$w.find('[name=nomb]').data('data')==null){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe seleccionar un propietario!',
							type: 'error'
						});
					}
					cmEspa.windowSelect({
						$window: p.$w,
						callback: p.cbEspacio,
						filter: [
						    //{nomb: 'propietario',value: {$exists: true}}
						    {nomb: 'propietario._id',value: p.$w.find('[name=nomb]').data('data')._id.$id}
						]
					});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnBusCem]').click(function(){
					ciSearch.windowSearchEmpresa({$window: p.$w,callback: p.cbCementerio});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnNewCem]').click(function(){
					ciCreate.windowNewEntidad({$window: p.$w,callBack: p.cbCementerio});
				}).button({icons: {primary: 'ui-icon-plusthick'}});
				p.$w.find('[name=fecprog]').datetimepicker({
					minuteGrid: 10
				});
				p.$w.find('[name=fecprog]').datepicker( "option", "dateFormat", 'yy-mm-dd' );
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
				p.payment = new cmOper.payment({
					$w: p.$w,
					getEspa: function(){
						return p.$w.find('[name=espaActual]').data('data');
					}
				});
				K.unblock({$element: p.$w});
			}
		});
	},
	windowRelacion: function(p){
		if(p==null) p = {};
		$.extend(p,{
			cbEspacio: function(data){
				p.$w.find('[name=rbtnConc]').button('enable',true);
				var tipo = '';
				p.$w.find('[name=det]').html("");
				p.$w.find('input:radio[name=rbtnConc]:eq(1)').attr('checked',true).click();
				p.$w.find('[name=rbtnConc]').button('refresh');
				if(data.nicho!=null){
					tipo = "Nicho-"+cmOper.statesNicho[data.nicho.tipo].descr;
					p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>Nombre del Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.nomb+"</td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Pabell&oacute;n:</label></td><td>"+data.nicho.pabellon.num+"</td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Nicho:</label></td><td>"+data.nicho.num+"</td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>Piso:</label></td><td>"+data.nicho.piso+"</td></tr>");
					p.$w.find('[name=det]').append("<tr><td><label>Fila:</label></td><td>"+data.nicho.fila+"</td></tr>");
				}
				else if(data.tumba!=null){
						tipo = "Tumba";
						p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
						p.$w.find('[name=det]').append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.tumba.denominacion+"</td></tr>");
					 }
					 else {
						tipo = "Mausoleo";
						p.$w.find('[name=det]').append("<tr><td><label>Tipo de Espacio:</label></td><td><span name='tipo'>"+tipo+"</span></td></tr>");
						p.$w.find('[name=det]').append("<tr><td><label>Tipo de Zona:</label></td><td>"+cmOper.statesZonaMaus[data.mausoleo.zona].descr+"</td></tr>");
						p.$w.find('[name=det]').append("<tr><td><label>Denominaci&oacute;n:</label></td><td>"+data.mausoleo.denominacion+"</td></tr>");
						p.$w.find('[name=det]').append("<tr><td><label>N&uacute;mero de Lote:</label></td><td>"+data.mausoleo.lote+"</td></tr>");
						p.$w.find('[name=rbtnConc]').button('disable',true);
					 }
				p.$w.find('[name=det]').append("</table>");
				p.$w.find('[name=tipo]').data('data',data);
			},
			cbOcupante: function(data){
				p.$w.find('[name=ocupante]').data('data',data);
				p.$w.find('[name=ocupante]').html( mgEnti.formatName(data) );
			},
			cbFuneraria: function(data){
				p.$w.find('[name=funeraria]').data('data',data);
				p.$w.find('[name=funeraria]').html( mgEnti.formatName(data) );
			},
			cbMunicipalidad: function(data){
				p.$w.find('[name=municipalidad]').data('data',data);
				p.$w.find('[name=municipalidad]').html( mgEnti.formatName(data) );
			}
		});
		new K.Modal({
			id: 'windowRegiOcup',
			title: 'Registro de ocupantes 2010-2012',
			width: 530,
			height: 370,
			contentURL: 'cm/oper/relacion',
			store: false,
			icon: 'ui-icon-person',
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {};
					data.ocupante = p.$w.find('[name=ocupante]').data('data');
					if(data.ocupante==null){
						return K.notification({
							title: ciHelper.titleMessages.infoReq,
							text: 'Debe ingresar los datos de al menos un ocupante!',
							type: 'error'
						});
					}else
						data.ocupante = mgEnti.dbRel(data.ocupante);
					data.espacio = {};
					var tmp = p.$w.find('[name=tipo]').data('data');
					if(tmp!=null){
						data.espacio._id = tmp._id.$id;
						data.espacio.nomb = tmp.nomb;
					}
					data.inh = true;
					data.inhumacion = {
						fecdef: p.$w.find('[name=fecdef]').val(),
						edad: p.$w.find('[name=edad]').val(),
						causa: p.$w.find('[name=causa]').val()
					};
					/*-if(data.inhumacion.fecdef==''){
						p.$w.find('[name=section3]').click();
						p.$w.find('[name=fecdef]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese la fecha de defunci&oacute;n!',type: 'error'});
					}*/
					/*if(data.inhumacion.edad==''){
						p.$w.find('[name=section3]').click();
						p.$w.find('[name=edad]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese la edad del ocupante al fallecer!',type: 'error'});
					}
					if(data.inhumacion.causa==''){
						p.$w.find('[name=section3]').click();
						p.$w.find('[name=causa]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese la causa de fallecimiento!',type: 'error'});
					}*/
					tmp = p.$w.find('[name=funeraria]').data('data');
					if(tmp==null){
						/*p.$w.find('[name=section3]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una funeraria!',type: 'error'});*/
					}else{
						data.inhumacion.funeraria = mgEnti.dbRel(tmp);
					}
					tmp = p.$w.find('[name=municipalidad]').data('data');
					if(tmp==null){
						/*p.$w.find('[name=section3]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una municipalidad!',type: 'error'});*/
					}else{
						data.inhumacion.municipalidad = mgEnti.dbRel(tmp);
					}
					data.inhumacion.partdef = p.$w.find('[name=partida]').val();
					/*if(data.inhumacion.partdef==''){
						p.$w.find('[name=section3]').click();
						p.$w.find('[name=partida]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese la partida de defunci&oacute;n!',type: 'error'});
					}*/
					data.inhumacion.fecinh = p.$w.find('[name=fecinh]').val();
					/*if(data.inhumacion.fecinh==''){
						p.$w.find('[name=section3]').click();
						p.$w.find('[name=fecinh]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese la fecha de inhumaci&oacute;n!',type: 'error'});
					}*/
					data.inhumacion.observ = p.$w.find('[name=observ]').val();
					data.inhumacion.recibo = p.$w.find('[name=recibo]').val();
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('cm/oper/save_relacion',data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'Operaci&oacute;n registrada con &eacute;xito'});
						K.closeWindow(p.$w.attr('id'));
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){p = null;},
			onContentLoaded: function(){
				p.$w = $('#windowRegiOcup');
				K.block({$element: p.$w});
				p.$w.find('[name=btnOcup]').click(function(){
					mgEnti.windowSelect({
						filter: [{nomb: 'tipo_enti',value: 'P'}],
						callback: p.cbOcupante
					});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnEspa]').click(function(){
					cmEspa.windowSelect({
						callback: p.cbEspacio,
						choose: true,
						filter: [{nomb: 'estado',value: 'D'}]
					});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=anos]').change(function(){
					var date = ciHelper.dateSumNow(parseInt(p.$w.find('[name=anos]').val()));
					p.$w.find('[name=spFecven]').data('date',K.dateTimeFormat(date));
					p.$w.find('[name=spFecven]').html(ciHelper.dateFormatLongS(date));
				}).numeric();
				p.$w.find('[name=fecdef]').datepicker();
				p.$w.find('[name=fecdef]').datepicker( "option", "dateFormat", 'yy-mm-dd' );
				p.$w.find('[name=btnFune]').click(function(){
					mgEnti.windowSelect({
						filter: [{nomb: 'tipo_enti',value: 'E'}],
						callback: p.cbFuneraria
					});
					//ciSearch.windowSearchEmpresa({$window: p.$w,callback: p.cbFuneraria});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnMuni]').click(function(){
					mgEnti.windowSelect({
						filter: [{nomb: 'tipo_enti',value: 'E'}],
						callback: p.cbMunicipalidad
					});
					//ciSearch.windowSearchEmpresa({$window: p.$w,callback: p.cbMunicipalidad});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=fecinh]').datepicker();
				p.$w.find('[name=fecinh]').datepicker( "option", "dateFormat", 'yy-mm-dd' );
				//p.$w.find('.ui-layout-center').css('overflow','hidden');
				K.unblock({$element: p.$w});
			}
		});
	},
	windowEditInhu: function(p){
		new K.Modal({
			id: 'windowEditInhu',
			title: 'Editar Inhumacion',
			width: 530,
			height: 370,
			contentURL: 'cm/oper/edit_inhu',
			store: false,
			icon: 'ui-icon-person',
			buttons: {
				"Guardar": function(){
					K.clearNoti();
					var data = {
						_id: p.id
					};
					$.extend(data,{
						fecdef: p.$w.find('[name=fecdef]').val(),
						edad: p.$w.find('[name=edad]').val(),
						causa: p.$w.find('[name=causa]').val()
					});
					/*-if(data.inhumacion.fecdef==''){
						p.$w.find('[name=section3]').click();
						p.$w.find('[name=fecdef]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese la fecha de defunci&oacute;n!',type: 'error'});
					}*/
					/*if(data.inhumacion.edad==''){
						p.$w.find('[name=section3]').click();
						p.$w.find('[name=edad]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese la edad del ocupante al fallecer!',type: 'error'});
					}
					if(data.inhumacion.causa==''){
						p.$w.find('[name=section3]').click();
						p.$w.find('[name=causa]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese la causa de fallecimiento!',type: 'error'});
					}*/
					tmp = p.$w.find('[name=funeraria]').data('data');
					if(tmp==null){
						/*p.$w.find('[name=section3]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una funeraria!',type: 'error'});*/
					}else{
						data.funeraria = mgEnti.dbRel(tmp);
					}
					tmp = p.$w.find('[name=muni]').data('data');
					if(tmp==null){
						/*p.$w.find('[name=section3]').click();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar una municipalidad!',type: 'error'});*/
					}else{
						data.municipalidad = mgEnti.dbRel(tmp);
					}
					data.folio = p.$w.find('[name=folio]').val();
					data.partdef = p.$w.find('[name=folio]').val();
					/*if(data.inhumacion.partdef==''){
						p.$w.find('[name=section3]').click();
						p.$w.find('[name=partida]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese la partida de defunci&oacute;n!',type: 'error'});
					}*/
					data.fecinh = p.$w.find('[name=fecinh]').val();
					/*if(data.inhumacion.fecinh==''){
						p.$w.find('[name=section3]').click();
						p.$w.find('[name=fecinh]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Ingrese la fecha de inhumaci&oacute;n!',type: 'error'});
					}*/
					data.observ = p.$w.find('[name=observ]').val();
					K.sendingInfo();
					p.$w.dialog('widget').find('.ui-dialog-buttonpane button').button('disable');
					$.post('cm/oper/save_edit_inhu',data,function(){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: 'Operaci&oacute;n registrada con &eacute;xito'});
						K.closeWindow(p.$w.attr('id'));
						$('#pageWrapperLeft .ui-state-highlight').click();
					});
				},
				"Cancelar": function(){
					K.closeWindow(p.$w.attr('id'));
				}
			},
			onClose: function(){p = null;},
			onContentLoaded: function(){
				p.$w = $('#windowEditInhu');
				K.block({$element: p.$w});
				p.$w.find('[name=fecdef]').datepicker();
				p.$w.find('[name=fecdef]').datepicker( "option", "dateFormat", 'yy-mm-dd' );
				p.$w.find('[name=btnFune]').click(function(){
					mgEnti.windowSelect({
						filter: [{nomb: 'tipo_enti',value: 'E'}],
						callback: function(data){
							p.$w.find('[name=funeraria]').html(data.nomb).data('data',data);
						}
					});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=btnMuni]').click(function(){
					mgEnti.windowSelect({
						filter: [{nomb: 'tipo_enti',value: 'E'}],
						callback: function(data){
							p.$w.find('[name=muni]').html(data.nomb).data('data',data);
						}
					});
				}).button({icons: {primary: 'ui-icon-search'}});
				p.$w.find('[name=fecinh]').datepicker();
				p.$w.find('[name=fecinh]').datepicker( "option", "dateFormat", 'yy-mm-dd' );
				$.post('cm/oper/get',{id: p.id},function(data){
					if(data.inhumacion.fecdef!=null)
						p.$w.find('[name=fecdef]').val(ciHelper.date.format.bd_ymd(data.inhumacion.fecdef));
					if(data.inhumacion.edad!=null)
						p.$w.find('[name=edad]').val(data.inhumacion.edad);
					if(data.inhumacion.folio!=null)
						p.$w.find('[name=folio]').val(data.inhumacion.folio);
					if(data.inhumacion.causa!=null)
						p.$w.find('[name=causa]').val(data.inhumacion.causa);
					if(data.inhumacion.funeraria!=null)
						p.$w.find('[name=funeraria]').html(data.inhumacion.funeraria.nomb).data('data',data.inhumacion.funeraria);
					if(data.inhumacion.municipalidad!=null)
						p.$w.find('[name=muni]').html(data.inhumacion.municipalidad.nomb).data('data',data.inhumacion.municipalidad);
					/*if(data.inhumacion.partdef!=null)
						p.$w.find('[name=partdef]').val(data.inhumacion.partdef);*/
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};
define(
	['cm/espa','cm/operpro','cm/operall','cm/cuen','mg/serv'],
	function(cmEspa,cmOperPro,cmOperAll,cmCuen,mgServ){
		return cmOper;
	}
);