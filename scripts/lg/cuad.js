/*******************************************************************************
Cuadro de necesidades */
lgCuad = {
	states: {
		P: {
			descr: "Aprobado",
			color: "green",
			label: '<span class="label label-success">Aprobado</span>'
		},
		A:{
			descr: "Pendiente",
			color: "#CCCCCC",
			label: '<span class="label label-default">Pendiente</span>'
		},
		E:{
			descr: "Enviado",
			color: "#CCCCCC",
			label: '<span class="label label-info">Enviado</span>'
		}
	},
	windowDetails: function(p){
		if(p.goBack!=null) K.history.push({f: p.goBack});
		new K.Panel({
			title: 'Cuadro de Necesidades '+p.nomb,
			contentURL: 'lg/cuad/details',
			buttons: {
				"Generar Excel": {
					icon: 'fa fa-file-excel-o',
					type: 'info',
					f: function(){
						window.open("lg/cuad/excel?_id="+p.id);
					}
				},
				"Cerrar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.goBack();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				p.widths = [80,100,270,100,270,100,80,80,80,80,80,80,80,80,80,80,80,80];
				p.$grid = new K.grid({
					$el: p.$w.find('[name=grid]'),
					search: false,
					pagination: false,
					height: 450,
					widths: p.widths,
					cols: ['N&deg;','Cod. Clas.','Descr. Clasificador','Cod.','Descr.','Unidad','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'],
					onlyHtml: true
				});
				$.post('lg/cuad/get',{_id:p.id},function(cuad){
					p.$w.find('[name=periodo]').html(cuad.periodo); 
					p.$w.find('[name=dependencia]').html(cuad.organizacion.nomb);
					p.$w.find('[name=trabajador]').html(ciHelper.enti.formatName(cuad.trabajador));
					p.$w.find('[name=fecreg]').html( ciHelper.date.format.bd_ymd(cuad.fecreg) );
					/*
					 * AQUI EL CARGO DEL TRABAJADOR
					 */
					//p.$w.find('[name=dependencia]').html(data.organizacion.nomb);
					/*
					 * AQUI EL CARGO DEL TRABAJADOR
					 */
					for(var i=0; i<cuad.items.length; i++){
						var data = cuad.items[i];
						if(data.tipo=='P') var $row = $('<tr class="item" data-prod="'+data.producto._id.$id+'">');
						else var $row = $('<tr class="item">');
						$row.append('<td>'+(parseFloat(p.$w.find('[name=grid] tbody .item').length)+1)+'</td>');
						$row.append('<td>'+data.clasif.cod+'</td>');
						if(data.clasif.descr!=null) $row.append('<td><span class="span_max" style="width: 250px;" data-toggle="tooltip" data-placement="bottom" title="'+data.clasif.descr+'">'+data.clasif.descr+'</span></td>');
						else $row.append('<td><span class="span_max" style="width: 250px;">--</span></td>');
						if(data.tipo=='P'){
							$row.append('<td>'+data.producto.cod+'</td>');
							$row.append('<td>'+data.producto.nomb+'</td>');
							$row.append('<td>'+data.producto.unidad.nomb+'</td>');
						}else{
							$row.append('<td>--</td>');
							$row.append('<td>'+data.servicio+'</td>');
							$row.append('<td>'+data.unidad.nomb+'</td>');
						}
						for(var ii=0; ii<12; ii++){
							$row.append('<td>'+data.entrega[ii]+'</td>');
						}
						$row.find('[data-toggle="tooltip"]').tooltip();
						$.each(p.$w.find('[name=grid] .table-header tr th'),function(index,th){
							$row.find('td').eq(index).width(p.widths[index]+'px');
						});
			        	p.$w.find("[name=grid] tbody").append( $row );
			        	p.$w.find('[name=grid] .table tbody tr:last td').outerHeight(p.$w.find('[name=grid] .table tbody tr:last').outerHeight()+'px');
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowDetailsProg: function(p){
		if(p.goBack!=null) K.history.push({f: p.goBack});
		new K.Panel({
			title: 'Cuadro de Necesidades '+p.nomb,
			contentURL: 'lg/cuad/details_prog',
			buttons: {
				"Generar Excel": {
					icon: 'fa fa-file-excel-o',
					type: 'info',
					f: function(){
						window.open("lg/cuad/excel?_id="+p.id);
					}
				},
				"Cerrar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.goBack();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				p.widths = [80,100,270,100,270,120,170,170,170,170,170,170,170,170,170,170,170,170,100,170,170,100,170];
				p.$grid = new K.grid({
					$el: p.$w.find('[name=grid]'),
					search: false,
					pagination: false,
					height: 450,
					widths: p.widths,
					cols: ['N&deg;','Cod. Clas.','Descr. Clasificador','Cod.','Descr.','Unidad','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre','Ajuste','Precio','SubTotal','Aceptado','Observaciones'],
					onlyHtml: true
				});
				$.post('lg/cuad/get',{_id:p.id,historico:true},function(cuad){
					p.data = cuad;
					p.$w.find('[name=periodo]').html(cuad.periodo); 
					p.$w.find('[name=dependencia]').html(cuad.organizacion.nomb);
					p.$w.find('[name=trabajador]').html(ciHelper.enti.formatName(cuad.trabajador));
					p.$w.find('[name=fecreg]').html( ciHelper.date.format.bd_ymd(cuad.fecreg) );
					if(cuad.fecvig!=null) p.$w.find('[name=fecvig]').html(ciHelper.date.format.bd_ymdhi(cuad.fecvig));
					/*
					 * AQUI EL CARGO DEL TRABAJADOR
					 */
					//p.$w.find('[name=dependencia]').html(data.organizacion.nomb);
					/*
					 * AQUI EL CARGO DEL TRABAJADOR
					 */
					var total = 0;
					for(var i=0; i<cuad.items.length; i++){
						var data = cuad.items[i];
						if(data.tipo=='P') var $row = $('<tr class="item" data-prod="'+data.producto._id.$id+'">');
						else var $row = $('<tr class="item">');
						$row.append('<td>'+(parseFloat(p.$w.find('[name=grid] tbody .item').length)+1)+'</td>');
						$row.append('<td>'+data.clasif.cod+'</td>');
						if(data.clasif.descr!=null) $row.append('<td><span class="span_max" style="width: 250px;" data-toggle="tooltip" data-placement="bottom" title="'+data.clasif.descr+'">'+data.clasif.descr+'</span></td>');
						else $row.append('<td><span class="span_max" style="width: 250px;">--</span></td>');
						if(data.tipo=='P'){
							$row.append('<td>'+data.producto.cod+'</td>');
							$row.append('<td>'+data.producto.nomb+'</td>');
							$row.append('<td>'+data.producto.unidad.nomb+'</td>');
						}else{
							$row.append('<td>--</td>');
							$row.append('<td>'+data.servicio+'</td>');
							$row.append('<td>'+data.unidad.nomb+'</td>');
						}
						for(var ii=0; ii<12; ii++){
							$row.append('<td>'+data.entrega[ii]+'</td>');
						}
						$row.append('<td>'+data.cant+'</td>');
						if(data.precio_unit!=null) $row.append('<td>'+ciHelper.formatMon(data.precio_unit)+'</td>');
						else $row.append('<td>S/.0.00</td>');
						if(data.precio_total!=null) $row.append('<td>'+ciHelper.formatMon(data.precio_total)+'</td>');
						else $row.append('<td>S/.0.00</td>');
						if(data.aceptado==true){
							//$row.append('<td><span class="fa fa-check"></span></td>');
							$row.append('<td><input class="form-control i-checks" type="checkbox" name="check" disabled="disabled" checked="checked"></td>');
							total = total + parseFloat(data.precio_total);
							$row.append('<td>--</td>');
						}else{
							//$row.append('<td><span class="fa fa-close"></span></td>');
							$row.append('<td><input class="form-control i-checks" type="checkbox" name="check" disabled="disabled"></td>');
							if(data.observ!=null) $row.append('<td>'+data.observ+'</td>');
							else $row.append('<td>--</td>');
						}
						$row.find('.i-checks').iCheck({
							checkboxClass: 'icheckbox_square-green',
							radioClass: 'iradio_square-green'
						});
						$row.find('[data-toggle="tooltip"]').tooltip();
						$.each(p.$w.find('[name=grid] .table-header tr th'),function(index,th){
							$row.find('td').eq(index).width(p.widths[index]+'px');
						});
			        	p.$w.find("[name=grid] tbody").append( $row );
			        	p.$w.find('[name=grid] .table tbody tr:last td').outerHeight(p.$w.find('[name=grid] .table tbody tr:last').outerHeight()+'px');
					}
					var $row = $('<tr class="item">');
					$row.append('<td width="3080" colspan="19"></td>');
					$row.append('<td width="170">Total</td>');
					$row.append('<td width="170">'+ciHelper.formatMon(total)+'</td>');
					$row.append('<td colspan="2" width="270"></td>');
			        p.$w.find("[name=grid] tbody").append( $row );
			        p.$w.find('[name=grid] .table tbody tr:last td').outerHeight(p.$w.find('[name=grid] .table tbody tr:last').outerHeight()+'px');
					if(cuad.historico!=null){
						p.$t2 = p.$w.find('#tab2');
						p.$grid = new K.grid({
							$el: p.$w.find('[name=gridHist]'),
							search: false,
							pagination: false,
							height: 450,
							widths: p.widths,
							cols: ['N&deg;','Cod. Clas.','Descr. Clasificador','Cod.','Descr.','Unidad','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre','Ajuste','Precio','SubTotal','Aceptado','Observaciones'],
							onlyHtml: true
						});
						var $cbo = p.$t2.find('[name=historico]');
						$cbo.append('<option value="">--</option>');
			        	for(var k=0,l=p.data.historico.length; k<l; k++){
			        		$cbo.append('<option value="'+k+'" data-id="'+p.data.historico[k]._id.$id+'">Estado del Cuadro de Necesidades para el '+ciHelper.date.format.long(p.data.historico[k].fecmod)+'</option>');
			        	}
			        	$cbo.change(function(){
			        		if($(this).find('option:selected').val()=='') return true;
			        		K.block();
			        		$.post('lg/cuad/get_hist',{_id: $(this).find('option:selected').attr('data-id')},function(data_hist){
				        		var result = data_hist;
								p.$t2.find('[name=periodo]').html(p.data.periodo); 
								p.$t2.find('[name=dependencia]').html(p.data.organizacion.nomb);
								p.$t2.find('[name=trabajador]').html(ciHelper.enti.formatName(result.trabajador));
								p.$t2.find('[name=estado]').html(lgCuad.states[result.estado].descr).css('color',lgCuad.states[result.estado].color);
				        		var total = 0;
				        		p.$t2.find("[name=gridHist] tbody").empty();
								for(var i=0; i<result.items.length; i++){
									var data = result.items[i];
									if(data.tipo=='P') var $row = $('<tr class="item" data-prod="'+data.producto._id.$id+'">');
									else var $row = $('<tr class="item">');
									$row.append('<td>'+(parseFloat(p.$w.find('[name=gridHist] tbody .item').length)+1)+'</td>');
									$row.append('<td>'+data.clasif.cod+'</td>');
									if(data.clasif.descr!=null) $row.append('<td><span class="span_max" style="width: 250px;" data-toggle="tooltip" data-placement="bottom" title="'+data.clasif.descr+'">'+data.clasif.descr+'</span></td>');
									else $row.append('<td><span class="span_max" style="width: 250px;">--</span></td>');
									if(data.tipo=='P'){
										$row.append('<td>'+data.producto.cod+'</td>');
										$row.append('<td>'+data.producto.nomb+'</td>');
										$row.append('<td>'+data.producto.unidad.nomb+'</td>');
									}else{
										$row.append('<td>--</td>');
										$row.append('<td>'+data.servicio+'</td>');
										$row.append('<td>'+data.unidad.nomb+'</td>');
									}
									for(var ii=0; ii<12; ii++){
										$row.append('<td>'+data.entrega[ii]+'</td>');
									}
									$row.append('<td>'+data.cant+'</td>');
									if(data.precio_unit!=null) $row.append('<td>'+ciHelper.formatMon(data.precio_unit)+'</td>');
									else $row.append('<td>S/.0.00</td>');
									if(data.precio_total!=null) $row.append('<td>'+ciHelper.formatMon(data.precio_total)+'</td>');
									else $row.append('<td>S/.0.00</td>');
									if(data.aceptado==true){
										//$row.append('<td><span class="fa fa-check"></span></td>');
										$row.append('<td><input class="form-control i-checks" type="checkbox" name="check" disabled="disabled" checked="checked"></td>');
										total = total + parseFloat(data.precio_total);
										$row.append('<td>--</td>');
									}else{
										//$row.append('<td><span class="fa fa-close"></span></td>');
										$row.append('<td><input class="form-control i-checks" type="checkbox" name="check" disabled="disabled"></td>');
										if(data.observ!=null) $row.append('<td>'+data.observ+'</td>');
										else $row.append('<td>--</td>');
									}
									$row.find('.i-checks').iCheck({
										checkboxClass: 'icheckbox_square-green',
										radioClass: 'iradio_square-green'
									});
									$row.find('[data-toggle="tooltip"]').tooltip();
									$.each(p.$w.find('[name=gridHist] .table-header tr th'),function(index,th){
										$row.find('td').eq(index).width(p.widths[index]+'px');
									});
						        	p.$w.find("[name=gridHist] tbody").append( $row );
				        			p.$w.find('[name=gridHist] tbody tr:last td').outerHeight(p.$w.find('[name=gridHist] tbody tr:last').outerHeight()+'px');
								}
								var $row = $('<tr class="item">');
								$row.append('<td width="3080" colspan="19"></td>');
								$row.append('<td width="170">Total</td>');
								$row.append('<td width="170">'+ciHelper.formatMon(total)+'</td>');
								$row.append('<td colspan="2" width="270"></td>');
						        p.$w.find("[name=gridHist] tbody").append( $row );
				    	    	p.$w.find('[name=gridHist] tbody tr:last td').outerHeight(p.$w.find('[name=gridHist] tbody tr:last').outerHeight()+'px');
			        			K.unblock();
				    	    },'json');
						});//.change();
					}else{
						p.$w.find('[href=#tab2]').removeAttr('data-toggle');
					}
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowNew: function(p){
		if(p.goBack!=null) K.history.push({f: p.goBack});
		if(K.session.enti.roles.trabajador==null){
			return K.notification({title: 'Acceso restringido',text: 'El usuario no tiene una organizaci&oacute;n asignada por lo que no puede crear un Cuadro de Necesidades!',type: 'error'});
		}
		if(p==null) p = {};
		new K.Panel({
			title: 'Nuevo Cuadro de Necesidades',
			contentURL: 'lg/cuad/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							estado: 'P',
							periodo: p.$w.find('[name=periodo]').val(),
							items: []
						};
						if(data.periodo==''){
							p.$w.find('[name=periodo]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el periodo del cuadro de necesidades!',type: 'error'}); 
						}
						if(p.$w.find('[name=grid] tbody .item').length<1){
							p.$w.find('[name=btnProd]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar al menos un producto para el cuadro de necesidades!',type: 'error'});
						}
						for(var i=0; i<p.$w.find('[name=grid] tbody .item').length; i++){
							var $row = p.$w.find('[name=grid] tbody .item').eq(i);
							if($row.data('tipo')=='P'){
								var prod = $row.data('data');
								var item = {
									item: i+1,
									clasif: prod.clasif._id.$id,
									unidad: prod.unidad._id.$id,
									tipo: 'P',
									entrega: []
								};
								item.producto = prod._id.$id;
								item.cuenta = prod.cuenta._id.$id;
								var tot = 0;
								for(var j=1; j<13; j++){
									var $inp =$row.find('[name=mes'+j+']');
									if($inp.val()==''){
										$inp.focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un valor para la entrega mensual!',type: 'error'});
									}
									item.entrega.push($inp.val());
									tot = tot + parseFloat($inp.val());
								}
								item.cant = tot;
								if(tot==0) return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se puede seleccionar un producto y luego no pedir stock!',type: 'error'});
								data.items.push(item);
							}else{
								var clasif = $row.data('data');
								var item = {
									item: i+1,
									clasif: clasif._id.$id,
									unidad: p.unid[$row.find('[name=unidad] option:selected').val()]._id.$id,
									tipo: 'S',
									entrega: []
								};
								item.servicio = $row.find('[name=descr]').val();
								var tot = 0;
								for(var j=1; j<13; j++){
									var $inp =$row.find('[name=mes'+j+']');
									if($inp.val()==''){
										$inp.focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un valor para la entrega mensual!',type: 'error'});
									}
									item.entrega.push($inp.val());
									tot = tot + parseFloat($inp.val());
								}
								item.cant = tot;
								if(tot==0){
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'No se puede seleccionar un servicio y luego no pedir stock!',
										type: 'error'
									});
								}
								data.items.push(item);
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('lg/cuad/save',data,function(){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiGua,text: 'El cuadro de necesidades fue registrado con &eacute;xito!'});
							K.goBack();
						});
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.goBack();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=dependencia]').html(K.session.enti.roles.trabajador.oficina.nomb);
				p.$w.find('[name=periodo]').numeric().val(ciHelper.date.getYear());
				p.widths = [80,100,270,100,270,120,170,170,170,170,170,170,170,170,170,170,170,170,70];
				p.cols = ['N&deg;','Cod. Clas.','Descr. Clasificador','Cod.','Descr.','Unidad','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre','&nbsp;'];
				p.$grid = new K.grid({
					$el: p.$w.find('[name=grid]'),
					search: false,
					pagination: false,
					height: 450,
					widths: p.widths,
					cols: p.cols,
					onlyHtml: true,
					toolbarHTML: '<button name="btnProd" class="btn btn-info"><i class="fa fa-shopping-cart"></i> Producto</button>&nbsp;'+
						'<button name="btnServ" class="btn btn-info"><i class="fa fa-wrench"></i> Servicio</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnProd]').click(function(){
							lgProd.windowSelect({
								callback: function(data){
									if(p.$w.find('[data-prod='+data._id.$id+']').length!=0){
										K.clearNoti();
										K.notification({
											title: 'Item repetido',
											text: 'El producto seleccionado ya ha sido escogido para el cuadro de necesidades previamente!',
											type: 'error'
										});
									}else{
										var $row = $('<tr class="item" data-prod="'+data._id.$id+'">');
										$row.append('<td>'+(parseFloat(p.$w.find('[name=grid] tbody .item').length)+1)+'</td>');
										$row.append('<td>'+data.clasif.cod+'</td>');
										$row.append('<td><span class="span_max" style="width: 250px;" data-toggle="tooltip" data-placement="bottom" title="'+data.clasif.descr+'">'+data.clasif.descr+'</span></td>');
										$row.append('<td>'+data.cod+'</td>');
										$row.append('<td>'+data.nomb+'</td>');
										$row.append('<td>'+data.unidad.nomb+'</td>');
										for(var i=1; i<=12; i++){
											$row.append('<td><input type="number" name="mes'+i+'" value="0" /></td>');
										}
										$row.append('<td><button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
										$row.find('[name^=mes]').numeric();
										$row.find('[name=btnEli]').click(function(){
											$(this).closest('.item').remove();
											for(var i=0; i<p.$w.find('[name=grid] tbody .item').length; i++){
												p.$w.find('[name=grid] tbody .item').eq(i).find('td:first').html(i+1);
											}
										});
										$row.data('data',data).data('tipo','P');
										$row.find('[data-toggle="tooltip"]').tooltip();
										$.each(p.$w.find('[name=grid] .table-header tr th'),function(index,th){
											$row.find('td').eq(index).width(p.widths[index]+'px');
										});
							        	p.$w.find("[name=grid] tbody").append( $row );
			        					p.$w.find('[name=grid] .table tbody tr:last td').outerHeight(p.$w.find('[name=grid] .table tbody tr:last').outerHeight()+'px');
									}
								}
							});
						});
						$el.find('[name=btnServ]').click(function(){
							prClas.windowSelect({
								bootstrap: true,
								tipo: 'G',
								callback: function(data){
									var $row = $('<tr class="item">');
									$row.append('<td>'+(parseFloat(p.$w.find('[name=grid] tbody .item').length)+1)+'</td>');
									$row.append('<td>'+data.cod+'</td>');
									$row.append('<td><span class="span_max" style="width: 250px;" data-toggle="tooltip" data-placement="bottom" title="'+data.descr+'">'+data.descr+'</span></td>');
									$row.append('<td>--</td>');
									$row.append('<td><input type="text" name="descr" /></td>');
									$row.append('<td><select name="unidad"></select></td>');
									for(var i=0; i<p.unid.length; i++){
										$row.find('[name=unidad]').append('<option value="'+i+'">'+p.unid[i].nomb+'</option>');
									}
									for(var i=1; i<=12; i++){
										$row.append('<td><input type="number" name="mes'+i+'" value="0" /></td>');
									}
									$row.append('<td><button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
									$row.find('[name^=mes]').numeric();
									$row.find('[name=btnEli]').click(function(){
										$(this).closest('.item').remove();
										for(var i=0; i<p.$w.find('[name=grid] tbody .item').length; i++){
											p.$w.find('[name=grid] tbody .item').eq(i).find('td:first').html(i+1);
										}
									});
									$row.data('data',data).data('tipo','S');
									$row.find('[data-toggle="tooltip"]').tooltip();
									$.each(p.$w.find('[name=grid] .table-header tr th'),function(index,th){
										$row.find('td').eq(index).width(p.widths[index]+'px');
									});
						        	p.$w.find("[name=grid] tbody").append( $row );
						        	p.$w.find('[name=grid] .table tbody tr:last td').outerHeight(p.$w.find('[name=grid] .table tbody tr:last').outerHeight()+'px');
								}
							});
						});
					}
				});
				K.block();
				$.post('lg/cuad/get_new',function(data){
					p.unid = data.unidades;
					if(data.cuadro!=null){
						cuad = data.cuadro;
						for(var i=0; i<cuad.items.length; i++){
							var data = cuad.items[i];
							if(data.tipo=='P') var $row = $('<tr class="item" data-prod="'+data.producto._id.$id+'">');
							else var $row = $('<tr class="item">');
							$row.append('<td>'+(parseFloat(p.$w.find('[name=grid] tbody .item').length)+1)+'</td>');
							$row.append('<td>'+data.clasif.cod+'</td>');
							if(data.clasif.descr!=null) $row.append('<td><span class="span_max" style="width: 250px;" data-toggle="tooltip" data-placement="bottom" title="'+data.clasif.descr+'">'+data.clasif.descr+'</span></td>');
							else $row.append('<td><span class="span_max" style="width: 250px;">--</span></td>');
							if(data.tipo=='P'){
								data.producto.clasif = data.clasif;
								data.producto.cuenta = data.cuenta;
								$row.append('<td>'+data.producto.cod+'</td>');
								$row.append('<td>'+data.producto.nomb+'</td>');
								$row.append('<td>'+data.producto.unidad.nomb+'</td>');
								$row.data('data',data.producto).data('tipo','P');
							}else{
								$row.append('<td>--</td>');
								$row.append('<td><input type="text" name="descr" value="'+data.servicio+'" /></td>');
								$row.append('<td><select name="unidad"></select></td>');
								for(var ii=0; ii<p.unid.length; ii++){
									$row.find('[name=unidad]').append('<option value="'+ii+'">'+p.unid[ii].nomb+'</option>');
									if(data.unidad._id.$id==p.unid[ii]._id.$id)
										$row.find('[name=unidad] option:last').attr('selected','selected');
								}
								$row.data('data',data.clasif).data('tipo','S');
							}
							for(var ii=0; ii<12; ii++){
								$row.append('<td><input type="number" name="mes'+(ii+1)+'" value="'+data.entrega[ii]+'" step="0.5" lang="en" /></td>');
							}
							$row.append('<td><button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
							//$row.find('[name^=mes]').numeric();
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
								for(var i=0; i<p.$w.find('[name=grid] tbody .item').length; i++){
									p.$w.find('[name=grid] tbody .item').eq(i).find('td:first').html(i+1);
								}
							});
							$row.find('[data-toggle="tooltip"]').tooltip();
							$.each(p.$w.find('[name=grid] .table-header tr th'),function(index,th){
								$row.find('td').eq(index).width(p.widths[index]+'px');
							});
				        	p.$w.find("[name=grid] tbody").append( $row );
			        		p.$w.find('[name=grid] .table tbody tr:last td').outerHeight(p.$w.find('[name=grid] .table tbody tr:last').outerHeight()+'px');
						}
						K.msg({text: 'SE USARA DE BASE EL CUADRO DE NECESIDADES DEL AÑO PASADO'});
					}
					K.unblock();
				},'json');
			}
		});
	},
	windowEdit: function(p){
		if(p.goBack!=null) K.history.push({f: p.goBack});
		new K.Panel({
			title: 'Editar Cuadro de Necesidades',
			contentURL: 'lg/cuad/edit',
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							periodo: p.$w.find('[name=periodo]').val(),
							items: []
						};
						if(data.periodo==''){
							p.$w.find('[name=periodo]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar el periodo del cuadro de necesidades!',type: 'error'}); 
						}
						if(p.$w.find('[name=grid] tbody .item').length<1){
							p.$w.find('[name=btnProd]').click();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe seleccionar al menos un producto para el cuadro de necesidades!',type: 'error'});
						}
						for(var i=0; i<p.$w.find('[name=grid] tbody .item').length; i++){
							var $row = p.$w.find('[name=grid] tbody .item').eq(i);
							if($row.data('tipo')=='P'){
								var prod = $row.data('data');
								var item = {
									item: i+1,
									unidad: prod.unidad._id.$id,
									clasif: prod.clasif._id.$id,
									tipo: 'P',
									entrega: []
								};
								item.producto = prod._id.$id;
								item.cuenta = prod.cuenta._id.$id;
								var tot = 0;
								for(var j=1; j<13; j++){
									var $inp =$row.find('[name=mes'+j+']');
									if($inp.val()==''){
										$inp.focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un valor para la entrega mensual!',type: 'error'});
									}
									item.entrega.push($inp.val());
									tot = tot + parseFloat($inp.val());
								}
								item.cant = tot;
								if(tot==0) return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se puede seleccionar un producto y luego no pedir stock!',type: 'error'});
								data.items.push(item);
							}else{
								var clasif = $row.data('data');
								var item = {
									item: i+1,
									clasif: clasif._id.$id,
									unidad: p.unid[$row.find('[name=unidad] option:selected').val()]._id.$id,
									tipo: 'S',
									entrega: []
								};
								item.servicio = $row.find('[name=descr]').val();
								var tot = 0;
								for(var j=1; j<13; j++){
									var $inp =$row.find('[name=mes'+j+']');
									if($inp.val()==''){
										$inp.focus();
										return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un valor para la entrega mensual!',type: 'error'});
									}
									item.entrega.push($inp.val());
									tot = tot + parseFloat($inp.val());
								}
								item.cant = tot;
								if(tot==0){
									return K.notification({
										title: ciHelper.titleMessages.infoReq,
										text: 'No se puede seleccionar un servicio y luego no pedir stock!',
										type: 'error'
									});
								}
								data.items.push(item);
							}
						}
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('lg/cuad/save',data,function(){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'El cuadro de necesidades fue actualizado con &eacute;xito!'});
							K.goBack();
						});
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.goBack();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$w.find('[name=dependencia]').html(K.session.enti.roles.trabajador.oficina.nomb);
				p.$w.find('[name=periodo]').numeric().val(ciHelper.date.getYear());
				p.widths = [80,100,270,100,270,120,170,170,170,170,170,170,170,170,170,170,170,170,70];
				p.$grid = new K.grid({
					$el: p.$w.find('[name=grid]'),
					search: false,
					pagination: false,
					height: 450,
					widths: p.widths,
					cols: ['N&deg;','Cod. Clas.','Descr. Clasificador','Cod.','Descr.','Unidad','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre','&nbsp;'],
					onlyHtml: true,
					toolbarHTML: '<button name="btnProd" class="btn btn-info"><i class="fa fa-shopping-cart"></i> Producto</button>&nbsp;'+
						'<button name="btnServ" class="btn btn-info"><i class="fa fa-wrench"></i> Servicio</button>',
					onContentLoaded: function($el){
						$el.find('[name=btnProd]').click(function(){
							lgProd.windowSelect({
								callback: function(data){
									if(p.$w.find('[data-prod='+data._id.$id+']').length!=0){
										K.clearNoti();
										K.notification({
											title: 'Item repetido',
											text: 'El producto seleccionado ya ha sido escogido para el cuadro de necesidades previamente!',
											type: 'error'
										});
									}else{
										var $row = $('<tr class="item" data-prod="'+data._id.$id+'">');
										$row.append('<td>'+(parseFloat(p.$w.find('[name=grid] tbody .item').length)+1)+'</td>');
										$row.append('<td>'+data.clasif.cod+'</td>');
										$row.append('<td><span class="span_max" style="width: 250px;" data-toggle="tooltip" data-placement="bottom" title="'+data.clasif.descr+'">'+data.clasif.descr+'</span></td>');
										$row.append('<td>'+data.cod+'</td>');
										$row.append('<td>'+data.nomb+'</td>');
										$row.append('<td>'+data.unidad.nomb+'</td>');
										for(var i=1; i<=12; i++){
											$row.append('<td><input type="number" name="mes'+i+'" value="0" step="0.5" lang="en" /></td>');
										}
										$row.append('<td><button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
										//$row.find('[name^=mes]').numeric();
										$row.find('[name=btnEli]').click(function(){
											$(this).closest('.item').remove();
											for(var i=0; i<p.$w.find('[name=grid] tbody .item').length; i++){
												p.$w.find('[name=grid] tbody .item').eq(i).find('td:first').html(i+1);
											}
										});
										$row.data('data',data).data('tipo','P');
										$row.find('[data-toggle="tooltip"]').tooltip();
										$.each(p.$w.find('[name=grid] .table-header tr th'),function(index,th){
											$row.find('td').eq(index).width(p.widths[index]+'px');
										});
							        	p.$w.find("[name=grid] tbody").append( $row );
			        					p.$w.find('[name=grid] .table tbody tr:last td').outerHeight(p.$w.find('[name=grid] .table tbody tr:last').outerHeight()+'px');
									}
								}
							});
						});
						$el.find('[name=btnServ]').click(function(){
							prClas.windowSelect({
								bootstrap: true,
								tipo: 'G',
								callback: function(data){
									var $row = $('<tr class="item">');
									$row.append('<td>'+(parseFloat(p.$w.find('[name=grid] tbody .item').length)+1)+'</td>');
									$row.append('<td>'+data.cod+'</td>');
									$row.append('<td><span class="span_max" style="width: 250px;" data-toggle="tooltip" data-placement="bottom" title="'+data.descr+'">'+data.descr+'</span></td>');
									$row.append('<td>--</td>');
									$row.append('<td><input type="text" name="descr" /></td>');
									$row.append('<td><select name="unidad"></select></td>');
									for(var i=0; i<p.unid.length; i++){
										$row.find('[name=unidad]').append('<option value="'+i+'">'+p.unid[i].nomb+'</option>');
									}
									for(var i=1; i<=12; i++){
										$row.append('<td><input type="number" name="mes'+i+'" value="0" step="0.5" lang="en" /></td>');
									}
									$row.append('<td><button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
									//$row.find('[name^=mes]').numeric();
									$row.find('[name=btnEli]').click(function(){
										$(this).closest('.item').remove();
										for(var i=0; i<p.$w.find('[name=grid] tbody .item').length; i++){
											p.$w.find('[name=grid] tbody .item').eq(i).find('td:first').html(i+1);
										}
									});
									$row.data('data',data).data('tipo','S');
									$row.find('[data-toggle="tooltip"]').tooltip();
									$.each(p.$w.find('[name=grid] .table-header tr th'),function(index,th){
										$row.find('td').eq(index).width(p.widths[index]+'px');
									});
						        	p.$w.find("[name=grid] tbody").append( $row );
			        				p.$w.find('[name=grid] .table tbody tr:last td').outerHeight(p.$w.find('[name=grid] .table tbody tr:last').outerHeight()+'px');
								}
							});
						});
					}
				});
				K.block({$element: p.$w});
				$.post('lg/unid/all',function(data){
					p.unid = data;
					$.post('lg/cuad/get',{_id:p.id},function(cuad){
						p.$w.find('[name=periodo]').val(cuad.periodo); 
						p.$w.find('[name=dependencia]').html(cuad.organizacion.nomb);
						for(var i=0; i<cuad.items.length; i++){
							var data = cuad.items[i];
							if(data.tipo=='P') var $row = $('<tr class="item" data-prod="'+data.producto._id.$id+'">');
							else var $row = $('<tr class="item">');
							$row.append('<td>'+(parseFloat(p.$w.find('[name=grid] tbody .item').length)+1)+'</td>');
							$row.append('<td>'+data.clasif.cod+'</td>');
							if(data.clasif.descr!=null) $row.append('<td><span class="span_max" style="width: 250px;" data-toggle="tooltip" data-placement="bottom" title="'+data.clasif.descr+'">'+data.clasif.descr+'</span></td>');
							else $row.append('<td><span class="span_max" style="width: 250px;">--</span></td>');
							if(data.tipo=='P'){
								data.producto.clasif = data.clasif;
								data.producto.cuenta = data.cuenta;
								$row.append('<td>'+data.producto.cod+'</td>');
								$row.append('<td>'+data.producto.nomb+'</td>');
								$row.append('<td>'+data.producto.unidad.nomb+'</td>');
								$row.data('data',data.producto).data('tipo','P');
							}else{
								$row.append('<td>--</td>');
								$row.append('<td><input type="text" name="descr" value="'+data.servicio+'" /></td>');
								$row.append('<td><select name="unidad"></select></td>');
								for(var ii=0; ii<p.unid.length; ii++){
									$row.find('[name=unidad]').append('<option value="'+ii+'">'+p.unid[ii].nomb+'</option>');
									if(data.unidad._id.$id==p.unid[ii]._id.$id)
										$row.find('[name=unidad] option:last').attr('selected','selected');
								}
								$row.data('data',data.clasif).data('tipo','S');
							}
							for(var ii=0; ii<12; ii++){
								$row.append('<td><input type="number" name="mes'+(ii+1)+'" value="'+data.entrega[ii]+'" step="0.5" lang="en" /></td>');
							}
							$row.append('<td><button name="btnEli" class="btn btn-danger"><i class="fa fa-trash-o"></i></button></td>');
							//$row.find('[name^=mes]').numeric();
							$row.find('[name=btnEli]').click(function(){
								$(this).closest('.item').remove();
								for(var i=0; i<p.$w.find('[name=grid] tbody .item').length; i++){
									p.$w.find('[name=grid] tbody .item').eq(i).find('td:first').html(i+1);
								}
							});
							$row.find('[data-toggle="tooltip"]').tooltip();
							$.each(p.$w.find('[name=grid] .table-header tr th'),function(index,th){
								$row.find('td').eq(index).width(p.widths[index]+'px');
							});
				        	p.$w.find("[name=grid] tbody").append( $row );
			        		p.$w.find('[name=grid] .table tbody tr:last td').outerHeight(p.$w.find('[name=grid] .table tbody tr:last').outerHeight()+'px');
						}
						K.unblock({$element: p.$w});
					},'json');
				},'json');
			}
		});
	},
	windowEditProg: function(p){
		if(p.goBack!=null) K.history.push({f: p.goBack});
		$.extend(p,{
			calcTot: function($row){
				var cant = 0;
				$row.find('[name^=mes]').each(function(){
					cant = cant + parseFloat($(this).val());
				});
				cant = K.round(cant,2);
				$row.find('td:eq(18)').html( cant );
				var precio = K.round(parseFloat($row.find('[name=precio]').val()),2);
				$row.find('td:eq(20)').html(K.round(cant*precio,2));
				var total = 0;
				for(var i=0; i<p.$w.find('[name=grid] tbody .item').length; i++){
					var $row = p.$w.find('[name=grid] tbody .item').eq(i);
					if($row.find('[name=check]').is(':checked')==true){
						total = total + parseFloat($row.find('td:eq(20)').html());
						$row.find('[name^=mes]').removeAttr('disabled');
						$row.find('[name^=precio]').removeAttr('disabled');
						$row.find('[name=observ]').hide();
					}else{
						$row.find('[name^=mes]').attr('disabled','disabled');
						$row.find('[name^=precio]').attr('disabled','disabled');
						$row.find('[name=observ]').show();
					}
				}
				p.$w.find('[name=grid] tbody .result td:eq(20)').html('<b>'+K.round(total,2)+'</b>');
			}
		});
		new K.Panel({
			title: 'Editar Cuadro de Necesidades de Programa '+p.nomb,
			contentURL: 'lg/cuad/edit_prog',
			buttons: {
				'Guardar': {
					icon: 'fa-save',
					type: 'info',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							fecvig: p.$w.find('[name=fecvig]').val(),
							items: [],
							totales_clasif: []
						};
						if(data.fecvig==""){
							p.$w.find('[name=fecvig]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha de entrada en vigencia!',type: 'error'});
						}
						var total = 0;
						var clasif = [];
						for(var i=0; i<p.$w.find('[name=grid] tbody .item').length; i++){
							var $row = p.$w.find('[name=grid] tbody .item').eq(i);
							if($row.data('tipo')=='P'){
								var prod = $row.data('data');
								var item = {
									item: i+1,
									unidad: prod.unidad._id.$id,
									clasif: (prod.clasif!=null)?prod.clasif._id.$id:null,
									tipo: 'P',
									entrega: []
								};
								item.producto = prod._id.$id;
								if(prod.cuenta!=null)
									item.cuenta = prod.cuenta._id.$id;
							}else{
								var clasi = $row.data('data'),
								unid = $row.data('unidad'),
								prod = {clasif:clasi};
								var item = {
									item: i+1,
									clasif: clasi._id.$id,
									unidad: unid._id.$id,
									tipo: 'S',
									entrega: []
								};
								item.servicio = $row.data('serv');
							}
							var tot = 0;
							for(var j=1; j<13; j++){
								var $inp =$row.find('[name=mes'+j+']');
								if($inp.val()==''){
									$inp.focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un valor para la entrega mensual!',type: 'error'});
								}
								item.entrega.push($inp.val());
								tot = tot + parseFloat($inp.val());
							}
							if(tot==0) return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se puede seleccionar un concepto y luego no pedir stock!',type: 'error'});
							item.cant = K.round(tot);
							item.precio_unit = K.round(parseFloat($row.find('[name=precio]').val()),2);
							item.precio_total = K.round(item.cant*item.precio_unit,2);
							if($row.find('[name=check]').is(':checked')==true){
								item.aceptado = true;
								if(item.precio_total==0) return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se puede aceptar un producto y luego no ponerle precio!',type: 'error'});
								total = total + parseFloat(item.precio_total);
								var index = $.inArray(item.clasif,clasif);
								if(index==-1){
									clasif.push(item.clasif);
									data.totales_clasif.push({
										precio_total: item.precio_total,
										clasif: {
											_id: prod.clasif._id.$id,
											cod: prod.clasif.cod,
											descr: prod.clasif.descr
										}
									});
								}else{
									data.totales_clasif[index].precio_total = K.round(item.precio_total + data.totales_clasif[index].precio_total,2);
								}
							}else{
								item.aceptado = false;
								item.observ = $row.find('[name=observ]').val();
								if(item.observ==''){
									$row.find('[name=observ]').focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe explicar el por qu&eacute; no se acepta el item!',type: 'error'});
								}
							}
							data.items.push(item);
						}
						data.precio_total = total;
						if(data.precio_total==0) return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe aceptar al menos un producto!',type: 'error'});
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('lg/cuad/save',data,function(){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'El cuadro de necesidades fue actualizado con &eacute;xito!'});
							K.goBack();
						});
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.goBack();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				p.$w.find('[name=fecvig]').datepicker();
				p.widths = [80,100,270,100,270,120,170,170,170,170,170,170,170,170,170,170,170,170,100,170,170,100,170];
				p.$grid = new K.grid({
					$el: p.$w.find('[name=grid]'),
					search: false,
					pagination: false,
					height: 450,
					widths: p.widths,
					cols: ['N&deg;','Cod. Clas.','Descr. Clasificador','Cod.','Descr.','Unidad','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre','Ajuste','Precio','SubTotal','Aceptado <input class="form-control i-checks" type="checkbox" name="all" value="1">','Observaciones'],
					onlyHtml: true
				});
				p.$w.find('.i-checks').iCheck({
					checkboxClass: 'icheckbox_square-green',
					radioClass: 'iradio_square-green'
				});
				p.$w.find('[name=grid] thead [name=all]').on('ifChecked', function(event){
					p.$w.find('[name=grid] tbody [name^=check]').iCheck('check');
					console.log('si!');
				});
				p.$w.find('[name=grid] thead [name=all]').on('ifUnchecked', function(event){
					p.$w.find('[name=grid] tbody [name^=check]').iCheck('uncheck');
					console.log('no!');
				});
				$.post('lg/cuad/get',{_id:p.id},function(cuad){
					p.$w.find('[name=periodo]').html(cuad.periodo);
					p.$w.find('[name=dependencia]').html(cuad.organizacion.nomb);
					if(cuad.fecvig!=null){
						p.$w.find('[name=fecvig]').val(ciHelper.date.format.bd_ymd(cuad.fecvig));
					}
					for(var i=0; i<cuad.items.length; i++){
						var data = cuad.items[i];
						if(data.tipo=='P') var $row = $('<tr class="item" data-prod="'+data.producto._id.$id+'">');
						else var $row = $('<tr class="item">');
						$row.append('<td>'+(parseFloat(p.$w.find('[name=grid] tbody .item').length)+1)+'</td>');
						$row.append('<td><span>'+data.clasif.cod+'</span> <button class="btn btn-success" name="btnSeleccionarClasi"><i class="fa fa-search"></i></button></td>');
						$row.find('[name=btnSeleccionarClasi]').click(function(){
							var $this = $(this);
							prClas.windowSelect({
								bootstrap: true,
								tipo: 'G',
								callback: function(data){
									var $_row = $this.closest('tr');
									if($_row.data('tipo')=='P'){
										var _data = $_row.data('data');
										_data.clasif = {
											_id: data._id,
											descr: data.descr,
											cod: data.cod
										}
									}else{
										var _data = $_row.data('data');
										_data._id = data._id;
										_data.descr = data.descr;
										_data.cod = data.cod;
									}
									$_row.find('td').eq(1).find('span').html (data.cod);
									$_row.find('td').eq(2).find('span').html(data.descr).attr('data-original-title', data.descr);
								}

							});
						});
						if(data.clasif.descr!=null) $row.append('<td><span class="span_max" style="width: 250px;" data-toggle="tooltip" data-placement="bottom" title="'+data.clasif.descr+'">'+data.clasif.descr+'</span></td>');
						else $row.append('<td><span class="span_max" style="width: 250px;">--</span></td>');
						if(data.tipo=='P'){
							$row.append('<td>'+data.producto.cod+'</td>');
							$row.append('<td>'+data.producto.nomb+'</td>');
							$row.append('<td>'+data.producto.unidad.nomb+'</td>');
							$row.data('data',data.producto).data('tipo','P');
						}else{
							$row.append('<td>--</td>');
							$row.append('<td>'+data.servicio+'</td>');
							$row.append('<td>'+data.unidad.nomb+'</td>');
							$row.data('data',data.clasif).data('unidad',data.unidad).data('serv',data.servicio).data('tipo','S');
						}
						for(var ii=0; ii<12; ii++){
							$row.append('<td><input type="number" name="mes'+(ii+1)+'" value="'+data.entrega[ii]+'" /></td>');
						}
						$row.find('[name^=mes]').numeric();
						$row.append('<td>'+data.cant+'</td>');
						$row.append('<td><input type="number" name="precio" value="0.00"></td>');
						$row.append('<td></td>');
						$row.append('<td><input class="form-control i-checks" type="checkbox" name="check" value="1"></td>');
						$row.append('<td><input class="form-control" type="text" name="observ" size="24" style="width: 160px" placeholder="Por qu&eacute; no fue aceptado"></td>');
						$row.find('[name=precio]').keyup(function(){
							p.calcTot($(this).closest('.item'));
						}).numeric();
						if(data.precio_unit!=null) $row.find('[name=precio]').val( data.precio_unit );
						if(data.precio_total!=null) $row.find('td').eq(20).html( data.precio_total );
						else $row.find('td').eq(20).html('0.00');
						if(data.aceptado==true) $row.find('[name=check]').attr('checked','checked');
						else if(data.observ!=null) $row.find('[name=observ]').val( data.observ );
						$row.find('.i-checks').iCheck({
							checkboxClass: 'icheckbox_square-green',
							radioClass: 'iradio_square-green'
						});
						$row.find('[name=check]').on('ifToggled', function(event){
							p.calcTot($(this).closest('.item'));
						});
						$row.find('[data-toggle="tooltip"]').tooltip();
						$.each(p.$w.find('[name=grid] .table-header tr th'),function(index,th){
							$row.find('td').eq(index).width(p.widths[index]+'px');
						});
			        	p.$w.find("[name=grid] tbody").append( $row );
			        	p.$w.find('[name=grid] .table tbody tr:last td').outerHeight(p.$w.find('[name=grid] .table tbody tr:last').outerHeight()+'px');
					}
					p.calcTot(p.$w.find('.item:last'));
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	},
	windowAmpli: function(p){
		if(p.goBack!=null) K.history.push({f: p.goBack});
		$.extend(p,{
			calcTot: function($row){
				var cant = 0;
				$row.find('[name^=mes]').each(function(){
					cant = cant + parseFloat($(this).val());
				});
				cant = K.round(cant,2);
				$row.find('td:eq(18)').html( cant );
				var precio = K.round(parseFloat($row.find('[name=precio]').val()),2);
				$row.find('td:eq(20)').html(K.round(cant*precio,2));
				var total = 0;
				for(var i=0; i<p.$w.find('[name=grid] tbody .item').length; i++){
					var $row = p.$w.find('[name=grid] tbody .item').eq(i);
					total = total + parseFloat($row.find('td:eq(20)').html());
				}
				p.$w.find('[name=grid] tbody .result td:eq(20)').html('<b>'+K.round(total,2)+'</b>');
			}
		});
		new K.Panel({
			title: 'Editar Cuadro de Necesidades de Programa '+p.nomb,
			contentURL: 'lg/cuad/edit_prog',
			buttons: {
				'Guardar': {
					icon: 'fa-save',
					type: 'info',
					f: function(){
						K.clearNoti();
						var data = {
							_id: p.id,
							fecvig: p.$w.find('[name=fecvig]').val(),
							items: [],
							totales_clasif: []
						};
						if(data.fecvig==""){
							p.$w.find('[name=fecvig]').focus();
							return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar una fecha de entrada en vigencia!',type: 'error'});
						}
						var total = 0;
						var clasif = [];
						for(var i=0; i<p.$w.find('[name=grid] tbody .item').length; i++){
							var $row = p.$w.find('[name=grid] tbody .item').eq(i),
							ite = $row.data('item');
							if($row.data('tipo')=='P'){
								var prod = $row.data('data');
								var item = {
									item: i+1,
									unidad: prod.unidad._id.$id,
									clasif: prod.clasif._id.$id,
									tipo: 'P',
									entrega: []
								};
								item.producto = prod._id.$id;
								item.cuenta = prod.cuenta._id.$id;
							}else{
								var clasi = $row.data('data'),
								unid = $row.data('unidad'),
								prod = {clasif:clasi};
								var item = {
									item: i+1,
									clasif: clasi._id.$id,
									unidad: unid._id.$id,
									tipo: 'S',
									entrega: []
								};
								item.servicio = $row.data('serv');
							}
							item.aceptado = ite.aceptado;
							item.saldo = ite.saldo;
							item.observ = ite.observ;
							var tot = 0;
							for(var j=1; j<13; j++){
								var $inp =$row.find('[name=mes'+j+']');
								if($inp.val()==''){
									$inp.focus();
									return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe ingresar un valor para la entrega mensual!',type: 'error'});
								}
								item.entrega.push($inp.val());
								tot = tot + parseFloat($inp.val());
							}
							if(tot==0) return K.notification({title: ciHelper.titleMessages.infoReq,text: 'No se puede seleccionar un concepto y luego no pedir stock!',type: 'error'});
							item.cant = K.round(tot);
							item.precio_unit = K.round(parseFloat($row.find('[name=precio]').val()),2);
							item.precio_total = K.round(item.cant*item.precio_unit,2);
							if(item.aceptado==true){
								total = total + parseFloat(item.precio_total);
								var index = $.inArray(item.clasif,clasif);
								if(index==-1){
									clasif.push(item.clasif);
									data.totales_clasif.push({
										precio_total: item.precio_total,
										clasif: {
											_id: prod.clasif._id.$id,
											cod: prod.clasif.cod,
											descr: prod.clasif.descr
										}
									});
								}else{
									data.totales_clasif[index].precio_total = K.round(item.precio_total + data.totales_clasif[index].precio_total,2);
								}
							}
							data.items.push(item);
						}
						data.precio_total = total;
						if(data.precio_total==0) return K.notification({title: ciHelper.titleMessages.infoReq,text: 'Debe aceptar al menos un producto!',type: 'error'});
						K.sendingInfo();
						p.$w.find('#div_buttons button').attr('disabled','disabled');
						$.post('lg/cuad/save',data,function(){
							K.clearNoti();
							K.notification({title: ciHelper.titleMessages.regiAct,text: 'El cuadro de necesidades fue actualizado con &eacute;xito!'});
							K.goBack();
						});
					}
				},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.goBack();
					}
				}
			},
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				K.block({$element: p.$w});
				p.$w.find('[name=fecvig]').closest('.form-group').remove();
				p.widths = [80,100,270,100,270,120,170,170,170,170,170,170,170,170,170,170,170,170,100,170,170];
				p.$grid = new K.grid({
					$el: p.$w.find('[name=grid]'),
					search: false,
					pagination: false,
					height: 450,
					widths: p.widths,
					cols: ['N&deg;','Cod. Clas.','Descr. Clasificador','Cod.','Descr.','Unidad','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre','Ajuste','Precio','SubTotal'],
					onlyHtml: true
				});
				var date = new Date();
				p.mes_actual = date.getMonth();
				p.ano_actual = date.getYear();
				$.post('lg/cuad/get',{_id:p.id},function(cuad){
					p.$w.find('[name=periodo]').html(cuad.periodo);
					p.$w.find('[name=dependencia]').html(cuad.organizacion.nomb);
					if(cuad.fecvig!=null){
						p.$w.find('[name=fecvig]').val(ciHelper.date.format.bd_ymd(cuad.fecvig));
					}
					for(var i=0; i<cuad.items.length; i++){
						var data = cuad.items[i];
						if(data.tipo=='P') var $row = $('<tr class="item" data-prod="'+data.producto._id.$id+'">');
						else var $row = $('<tr class="item">');
						$row.append('<td>'+(parseFloat(p.$w.find('[name=grid] tbody .item').length)+1)+'</td>');
						$row.append('<td>'+data.clasif.cod+'</td>');
						if(data.clasif.descr!=null) $row.append('<td><span class="span_max" style="width: 250px;" data-toggle="tooltip" data-placement="bottom" title="'+data.clasif.descr+'">'+data.clasif.descr+'</span></td>');
						else $row.append('<td><span class="span_max" style="width: 250px;">--</span></td>');
						if(data.tipo=='P'){
							$row.append('<td>'+data.producto.cod+'</td>');
							$row.append('<td>'+data.producto.nomb+'</td>');
							$row.append('<td>'+data.producto.unidad.nomb+'</td>');
							$row.data('data',data.producto).data('tipo','P');
						}else{
							$row.append('<td>--</td>');
							$row.append('<td>'+data.servicio+'</td>');
							$row.append('<td>'+data.unidad.nomb+'</td>');
							$row.data('data',data.clasif).data('unidad',data.unidad).data('serv',data.servicio).data('tipo','S');
						}
						for(var ii=0; ii<12; ii++){
							$row.append('<td><input type="number" name="mes'+(ii+1)+'" value="'+data.entrega[ii]+'" /></td>');
						}
						$row.find('[name^=mes]').numeric();
						$row.append('<td>'+data.cant+'</td>');
						$row.append('<td><input type="number" name="precio" value="0.00"></td>');
						$row.append('<td></td>');
						$row.find('input').keyup(function(){
							p.calcTot($(this).closest('.item'));
						}).numeric();
						if(data.precio_unit!=null) $row.find('[name=precio]').val( data.precio_unit );
						if(data.precio_total!=null) $row.find('td').eq(20).html( data.precio_total );
						else $row.find('td').eq(20).html('0.00');
						$row.find('[data-toggle="tooltip"]').tooltip();
						$.each(p.$w.find('[name=grid] .table-header tr th'),function(index,th){
							$row.find('td').eq(index).width(p.widths[index]+'px');
						});
						$row.data('item',data);
			        	p.$w.find("[name=grid] tbody").append( $row );
			        	p.$w.find('[name=grid] .table tbody tr:last td').outerHeight(p.$w.find('[name=grid] .table tbody tr:last').outerHeight()+'px');
			        	if(data.aceptado==false){
			        		p.$w.find('[name=grid] .table tbody tr:last input').attr('disabled','disabled');
				       }
					}
					p.calcTot(p.$w.find('.item:last'));
					K.unblock({$element: p.$w});
				},'json');
			}
		});
	}
};
define(
	['pr/clas','lg/prod'],
	function(prClas,lgProd){
		return lgCuad;
	}
);