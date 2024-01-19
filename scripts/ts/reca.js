tsReca = {
	states: {
		R: {
			descr: "Registrado",
			color: "green",
			label: '<span class="label label-success">Registrado</span>'
		},
		A:{
			descr: "Aprobado",
			color: "#CCCCCC",
			label: '<span class="label label-info">Aprobado</span>'
		}
	},
    tipo: {
		D: {
			descr: "Definitivo",
			color: "green",
			label: '<span class="label label-primary">Definitivo</span>'
		},
		P:{
			descr: "Provisional",
			color: "#CCCCCC",
			label: '<span class="label label-warning">Provisional</span>'
		}
	},
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'ts',
			action: 'tsReca',
			titleBar: { title: 'Recibos de Caja'}
		});
		
		new K.Panel({
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
		   		var $grid = new K.grid({
		   			$el: p.$w,
					cols: ['','Recibo','Tipo','Estado','Fecha','Pagar a','Concepto','Monto'],
					data: 'ts/reca/lista',
					params: {type_fields: 'programa_lista'},
                    itemdescr: 'recibo(s) de Caja',
					toolbarHTML: 
                    '<button name="btnDefinitivo" class="btn btn-success"><i class="fa fa-plus"></i> Recibo Definitivo</button>'+
                    '<button name="btnProvisional" class="btn btn-success"><i class="fa fa-plus"></i> Recibo Provisional</button>' +
                    '<span class="form-control col-sm-4" name="programa"></span>&nbsp;',
                    onContentLoaded: function($el){
                        $el.find('[name=btnDefinitivo]').click(function(){
							tsReca.NewDefinitivo();
						}).button({icons: {primary: 'ui-icon-plusthick'}});
                        
                        $el.find('[name=btnProvisional]').click(function(){
							tsReca.NewProvisional();
						}).button({icons: {primary: 'ui-icon-plusthick'}});
                        
                        /*OBTENER DATOS DE LA SESSION*/
                        //console.log(K.session.enti);
                        p.$w.find('[name=programa]').html(K.session.enti.roles.trabajador.oficina.nomb);
                    },
					onLoading: function(){ K.block(); },
					onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
					 K.unblock(); },
					fill: function(data,$row){
                        
                        $row.append('<td>');
                        $row.append('<td>'+data.num+'</td>');
                        $row.append('<td>'+tsReca.tipo[data.tipo].label+'</td>');
                        $row.append('<td>'+tsReca.states[data.estado].label+'</td>');
                        $row.append('<td>'+ciHelper.date.format.bd_ymd(data.fec)+'</td>');
						if(data.autor!=null)
							$row.append('<td>'+mgEnti.formatName(data.autor)+'</td>');
						else{
                            $row.append('<td>--</td>');
                        }
						$row.append('<td>'+data.concepto+'</td>');
						$row.append('<td>'+ciHelper.formatMon(data.monto)+'</td>');
                        $row.data('id',data._id.$id).data('data',data).data('estado',data.estado).contextMenu("conMenTsReci", {
                            onShowMenu: function($row, menu) {
                                if($row.data('data').estado == 'A'){
                                    $('#conMenTsReci_edit_d',menu).remove();
                                    $('#conMenTsReci_eli',menu).remove();
                                }
                                return menu;
                            },
						bindings: {
                            'conMenTsReci_eli': function(t) {
                                ciHelper.confirm('&#191;Desea <b>Eliminar</b> el Recibo Provisional:  <b>'+K.tmp.find('td:eq(1)').html()+'</b>&#63;',
                                function(){
                                    K.sendingInfo();
                                    $.post('ts/reca/delete',{_id: K.tmp.data('id')},function(){
                                        K.clearNoti();
                                        K.notification({title: 'Recibo provisional Eliminado',text: 'La eliminaci&oacute;n se realiz&oacute; con &eacute;xito!'});
                                        tsReca.init();
                                    });
                                },function(){
                                    $.noop();
                                },'Eliminaci&oacute;n de Recibo Provisional');
                            },
                            'conMenTsReci_apro': function(t) {
                                ciHelper.confirm('&#191;Desea <b>Aprobar</b> el Recibo Definitivo <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
                                function(){
                                    K.sendingInfo();
                                    $.post('ts/reca/save',{_id: K.tmp.data('id'),estado: 'A'},function(){
                                        K.clearNoti();
                                        K.msg({title: 'Recibo Aprobado',text: 'La aprobaci&oacute;n se realiz&oacute; con &eacute;xito!'});
                                        tsReca.init();
                                    });
                                },function(){
                                    $.noop();
                                },'Aprobaci&oacute;n de Recibo Definitivo');
                            },
                            'conMenTsReci_edit_d':function(t){
                                console.log(data);
                                if(data.tipo=='P'){
                                    tsReca.EditProvisional({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
                                }else{
                                    tsReca.EditDefinitivo({id: K.tmp.data('id'),nom: K.tmp.find('td:eq(2)').html()});
                                }
                                
                            },
                            'conMenTsReci_imp': function(t){
                                K.windowPrint({
                                    id:'windowPrint',
                                    title:'Recibo de Caja',
                                    url:'ts/reca/print?_id='+K.tmp.data('id')
                                });
                            }
                        }
                    });
						return $row;
					}
				});
			}
		});
	},
	NewDefinitivo: function(p){
		if(p==null) p = {};
		new K.Modal({
			id: 'windowNew',
			title: 'Nuevo Recibo Definitivo',
			contentURL: 'ts/reca/prov',
			store: false,
			width: 450,
			height: 450,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
					var data = {
						num: p.$w.find('[name=num]').val(),
                        fec: p.$w.find('[name=fec]').val(),
						monto: p.$w.find('[name=monto]').val(),
						concepto: p.$w.find('[name=concepto]').val(),
                        tipo: "D"
					};
					if(data.num == ""){
						p.$w.find('[name=num]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese un número!",type:"error"});
					}
                    if(data.fec == ""){
						p.$w.find('[name=fec]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione una fecha para el cheque!",type:"error"});
					}
					if(data.monto == ""){
						p.$w.find('[name=monto]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el monto del cheque!",type:"error"});
					}
					if(data.concepto == ""){
						p.$w.find('[name=concepto]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese una descripcion para el cheque!",type:"error"});
					}
					K.sendingInfo();
					p.$w.find('#div_buttons button').attr('disabled','disabled');
					$.post("ts/reca/save",data,function(rpta){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: "Recibo Definitivo agregado con &eacute;xito!"});
						tsReca.init();
						K.closeWindow(p.$w.attr('id'));
					},'json');
				}},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowNew');
                $.post('ts/reca/get_num_d',function(data){
                    console.log(data);
                    var number=0;
                    if(data== null){
                        number = 0;
                    }else{
                        number = parseFloat(data[0]['num']) +1;
                    }
                    p.$w.find('[name=num]').val(number);
                },'json');
				p.$w.find('[name=monto]').numeric();
				p.$w.find('[name=fec]').val(K.date()).datepicker();
                

			}
		});
	},
    EditDefinitivo: function(p){
		if(p==null) p = {};
        console.log(p);
		new K.Modal({
			id: 'windowEdit',
			title: 'Editar Recibo Definitivo',
			contentURL: 'ts/reca/prov',
			store: false,
			width: 450,
			height: 450,
			buttons: {
				"Guardar": {
					icon: 'fa-save',
					type: 'success',
					f: function(){
					var data = {
                        _id: p.id,
						num: p.$w.find('[name=num]').val(),
                        fec: p.$w.find('[name=fec]').val(),
						monto: p.$w.find('[name=monto]').val(),
						concepto: p.$w.find('[name=concepto]').val(),
                        tipo: "D"
					};
					if(data.num == ""){
						p.$w.find('[name=num]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese un número!",type:"error"});
					}
                    if(data.fec == ""){
						p.$w.find('[name=fec]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Seleccione una fecha para el cheque!",type:"error"});
					}
					if(data.monto == ""){
						p.$w.find('[name=monto]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese el monto del cheque!",type:"error"});
					}
					if(data.concepto == ""){
						p.$w.find('[name=concepto]').focus();
						return K.notification({title: ciHelper.titleMessages.infoReq,text: "Ingrese una descripcion para el cheque!",type:"error"});
					}
					K.sendingInfo();
					p.$w.find('#div_buttons button').attr('disabled','disabled');
					$.post("ts/reca/save",data,function(rpta){
						K.clearNoti();
						K.notification({title: ciHelper.titleMessages.regiGua,text: "Recibo Definitivo agregado con &eacute;xito!"});
						tsReca.init();
						K.closeWindow(p.$w.attr('id'));
					},'json');
				}},
				"Cancelar": {
					icon: 'fa-ban',
					type: 'danger',
					f: function(){
						K.closeWindow(p.$w.attr('id'));
					}
				}
			},
			onClose: function(){ p = null; },
			onContentLoaded: function(){
				p.$w = $('#windowEdit');
                $.post('ts/reca/get',{_id: p.id},function(data){
                    p.$w.find('[name=monto]').val(data.monto);
                    p.$w.find('[name=num]').val(data.num);
                    p.$w.find('[name=concepto]').val(data.concepto);
                    p.$w.find('[name=fec]').val(moment(data.fec.sec,'X').format('YYYY-MM-DD'));
                },'json');
                
                p.$w.find('[name=monto]').numeric();
                p.$w.find('[name=num]').numeric();
				p.$w.find('[name=fec]').val(K.date()).datepicker();

			}
		});
	},
    NewProvisional: function(p){
		if(p==null) p = {};
        
            p.calcularTotal = function(){
                
                var saldo = 0;

                for(var i=0;i<p.$w.find('[name=gridDefi] tbody tr').length;i++){
                 var $row = p.$w.find('[name=gridDefi] tbody tr').eq(i);
                var precio = $row.find('[name=precio]').val();
                if(i==0) $row.find('[name=saldo]').val(saldo);
                  if(!parseFloat(precio)){
                      precio = 0;
                  }else{
                      precio = parseFloat(precio);
                  }
                  
                saldo+=precio;
                
        }
      };

		new K.Panel({
            title: 'Recibo Provisional',
            contentURL: 'ts/reca/defi',
            store:false,
            buttons: {
                "Guardar": {
                    icon: 'fa-save',
                    type: 'success',
                    f: function(){
                        K.clearNoti();
                        var form = ciHelper.validator(p.$w.find('form'),{
                            onSuccess: function(){
                                K.sendingInfo();
                                var data = {
                                        num:p.$w.find('[name=num]').val(),
                                        comp:p.$w.find('[name=comp]').val(),
                                        fec:p.$w.find('[name=fec]').val(),
                                        concepto:p.$w.find('[name=concepto]').val(),
                                        monto:p.$w.find('[name=monto]').val(),
                                        tipo: 'P',
                                        conceptos:[]	
                                };
                                if(p.$w.find('[name=gridDefi] tbody tr').length>0){
                                    for(var i=0;i< p.$w.find('[name=gridDefi] tbody tr').length;i++){
                                        var $row = p.$w.find('[name=gridDefi] tbody tr').eq(i);
                                        var _conceptos ={
                                            concepto: $row.find('[name=concepto]').val(),
                                            precio: $row.find('[name=precio]').val(),
                                            
                                        }
                                        data.conceptos.push(_conceptos);
                                        
                                    }
                                }

                                p.$w.find('#div_buttons button').attr('disabled','disabled');
                                $.post("ts/reca/save",data,function(result){
                                        K.clearNoti();
                                        K.msg({title: ciHelper.titles.regiGua,text: "Recibo Provisional Agregado!"});
                                        tsReca.init();
                                    
                                },'json');
                            }
                        }).submit();
                    }
                },
                "Cancelar": {
                    icon: 'fa-ban',
                    type: 'danger',
                    f: function(){
                        tsReca.init();
                    }
                }
            },

            onContentLoaded: function(){
                p.$w = $('#mainPanel');
                $.post('ts/reca/get_num',function(data){
                    var number=0;
                    if(data== null){
                        number = 0;
                    }else{
                        number = parseFloat(data[0]['num']) + 1;
                    }
                    p.$w.find('[name=num]').val(number);
                },'json');
                p.$w.find('[name=monto]').numeric();
				p.$w.find('[name=fec]').val(K.date()).datepicker();
                new K.grid({
                    $el: p.$w.find('[name=gridDefi]'),
                    cols: ['Concepto','Precio','Saldo','Eliminar'],
                    stopLoad: true,
                    pagination: false,
                    search: false,
                    store:false,
                    toolbarHTML: '<button type = "button" name="btnAddReci" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Comprobante</button >',
                    onContentLoaded: function($el){
                        $el.find('button').click(function(){
                            var $row = $('<tr class="item">');
                            $row.append('<td><textarea type="text" class="form-control" name="concepto"></textarea></td>');
                            $row.append('<td>S/.<input type="text" name="precio" size="7"/></td>');
                            $row.find('[name=precio],[name=saldo]').keyup(function(){
                                p.calcularTotal();
                            });
                            $row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
                            $row.find('[name=btnEli]').click(function(){
                                $(this).closest('.item').remove();
                            });
                            p.$w.find('[name=gridDefi] tbody').append($row);
                            
                        });
                            
                    }
                });
                            
            }
        });
	},
    EditProvisional: function(p){
		if(p==null) p = {};
        
            p.calcularTotal = function(){
                
                var saldo = 0;

                for(var i=0;i<p.$w.find('[name=gridDefi] tbody tr').length;i++){
                 var $row = p.$w.find('[name=gridDefi] tbody tr').eq(i);
                var precio = $row.find('[name=precio]').val();
                if(i==0) $row.find('[name=saldo]').val(saldo);
                  if(!parseFloat(precio)){
                      precio = 0;
                  }else{
                      precio = parseFloat(precio);
                  }
                  
                saldo+=precio;
                $row.find('[name=saldo]').val(saldo);
        }
      };

		new K.Panel({
            title: 'Recibo Provisional',
            contentURL: 'ts/reca/defi',
            store:false,
            buttons: {
                "Guardar": {
                    icon: 'fa-save',
                    type: 'success',
                    f: function(){
                        K.clearNoti();
                        var form = ciHelper.validator(p.$w.find('form'),{
                            onSuccess: function(){
                                K.sendingInfo();
                                var data = {
                                        _id: p.id,
                                        num:p.$w.find('[name=num]').val(),
                                        comp:p.$w.find('[name=comp]').val(),
                                        fec:p.$w.find('[name=fec]').val(),
                                        concepto:p.$w.find('[name=concepto]').val(),
                                        monto:p.$w.find('[name=monto]').val(),
                                        tipo: 'P',
                                        conceptos:[]	
                                };
                                if(p.$w.find('[name=gridDefi] tbody tr').length>0){
                                    for(var i=0;i< p.$w.find('[name=gridDefi] tbody tr').length;i++){
                                        var $row = p.$w.find('[name=gridDefi] tbody tr').eq(i);
                                        var _conceptos ={
                                            concepto: $row.find('[name=concepto]').val(),
                                            precio: $row.find('[name=precio]').val(),
                                            
                                        }
                                        data.conceptos.push(_conceptos);
                                        
                                    }
                                }

                                p.$w.find('#div_buttons button').attr('disabled','disabled');
                                $.post("ts/reca/save",data,function(result){
                                        K.clearNoti();
                                        K.msg({title: ciHelper.titles.regiGua,text: "Recibo Provisional Agregado!"});
                                        tsReca.init();
                                    
                                },'json');
                            }
                        }).submit();
                    }
                },
                "Cancelar": {
                    icon: 'fa-ban',
                    type: 'danger',
                    f: function(){
                        tsReca.init();
                    }
                }
            },

            onContentLoaded: function(){
                p.$w = $('#mainPanel');

                

                p.$w.find('[name=monto]').numeric();
				p.$w.find('[name=fec]').val(K.date()).datepicker();
                new K.grid({
                    $el: p.$w.find('[name=gridDefi]'),
                    cols: ['Concepto','Precio','Eliminar'],
                    stopLoad: true,
                    pagination: false,
                    search: false,
                    store:false,
                    toolbarHTML: '<button type = "button" name="btnAddReci" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar Comprobante</button >',
                    onContentLoaded: function($el){
                        $el.find('button').click(function(){
                            var $row = $('<tr class="item">');
                            $row.append('<td><textarea type="text" class="form-control" name="concepto"></textarea></td>');
                            $row.append('<td>S/.<input type="text" name="precio" size="7"/></td>');
                            $row.find('[name=precio],[name=saldo]').keyup(function(){
                                p.calcularTotal();
                            });
                            $row.append('<td><button class="btn btn-xs btn-danger" name="btnEli"><i class="fa fa-trash-o"></i></button></td>');
                            $row.find('[name=btnEli]').click(function(){
                                $(this).closest('.item').remove();
                            });
                            p.$w.find('[name=gridDefi] tbody').append($row);
                            
                        });
                            
                    }
                });
                K.block();
                $.post('ts/reca/get',{_id:p.id},function(data){
                    p.$w.find('[name=num]').val(data.num);
                    p.$w.find('[name=comp]').val(data.comp);
                    p.$w.find('[name=monto]').val(data.monto);
                    p.$w.find('[name=concepto]').val(data.concepto);
                    p.$w.find('[name=fec]').val(moment(data.fec.sec,'X').format('YYYY-MM-DD'));
                    
                    if(data.conceptos != null){
                        if(data.conceptos.length>0){
                            for(var i=0;i<data.conceptos.length;i++){
                                p.$w.find('[name=btnAddReci]').click();
                                var $row = p.$w.find('[name=gridDefi] tbody tr:last');
                                $row.find('[name=concepto]').val(data.conceptos[i].concepto);
                                $row.find('[name=precio]').val(data.conceptos[i].precio);
                            }
                        }
                    }
                    K.unblock();
                    
                },'json');
            }
        });
	}
};
define(
	['mg/enti'],
	function(mgEnti){
		return tsReca;
	}
);