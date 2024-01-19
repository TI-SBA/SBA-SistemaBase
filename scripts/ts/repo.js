tsRepo = {
	init: function(p){
		if(p==null) p = {};
		K.initMode({
			mode: 'ts',
			action: 'tsRepo',
			titleBar: {
				title: 'Reportes de Recibos'
			}
		});
		
		new K.Panel({
			contentURL: 'ts/repo/index2',
			store: false,
			onContentLoaded: function(){
				p.$w = $('#mainPanel');
				p.$section2 = p.$w.find('#section2');

                $.post('mg/prog/all',function(data){
                    var $cbo = p.$section2.find('[name=oficina]');
                        $cbo.append('<option value="Todos">Todos</option>');    
                    for (var i=0;i<data.length;i++){
                            $cbo.append('<option value="'+data[i].nomb+'">'+data[i].nomb+'</option>');
                        }$cbo.change();
                    },'json');


				p.$section2.find('[name=btnImprimir]').click(function(){
					var params = new Object;
					params.ano = p.$section2.find('[name=ano] :selected').val();
					params.mes = p.$section2.find('[name=mes] :selected').val();
                    params.oficina = p.$section2.find('[name=oficina] :selected').val();
					//var url = 'ts/reca/get_reporte?'+$.param(params);
                    
                    //

                    new K.grid({
                        $el: p.$w.find('[name=gridRecibos]'),
                        search: false,
                        data: "ts/reca/get_reporte?"+$.param(params),
                        pagination: false,
                        cols:['','Recibo','Tipo','Estado','Fecha','Pagar a','Oficina','Concepto','Monto'],
                        onlyHtml:true,
                        toolbarHTML: '<h3>Lista de recibos</h3>',
                        onLoading: function(){ K.block(); },
                        onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+350+'px');
					    K.unblock(); },
                    });
                    K.block();
                    var aprobado = 0;
                    var registrado = 0;
                    $.post("ts/reca/get_reporte?"+$.param(params),function(data){
                        if(data!=null){
                            for (var i=0;i<data.length;i++){
                                console.log(data[i]);
                                var $row = $('<tr class="item" />');
                                
                                if(data[i].estado =='R'){
                                    $row.append('<td><button name="registrado" class="btn btn-warning"><i class="fa fa-pencil-square-o"></i></button></td>');
                                }else{
                                    $row.append('<td><button class="btn btn-success"><i class="fa fa-check-square"></i></button></td>');
                                }
                                $row.append('<td>'+data[i].num+'</td>');
                                $row.append('<td>'+tsReca.tipo[data[i].tipo].label+'</td>');
                                $row.append('<td>'+tsReca.states[data[i].estado].label+'</td>');
                                $row.append('<td>'+ciHelper.date.format.bd_ymd(data[i].fecreg)+'</td>');
                                $row.append('<td>'+mgEnti.formatName(data[i].autor)+'</td>');
                                $row.append('<td>'+data[i].cargo.oficina.nomb+'</td>');
                                $row.append('<td>'+data[i].concepto+'</td>');
                                $row.append('<td>'+data[i].monto+'</td>');
                                p.$w.find('[name=gridRecibos] tbody').append($row);
                                if(data[i].estado == 'A'){
                                    aprobado += parseInt(data[i].monto);
                                }else{
                                    registrado += parseInt(data[i].monto);
                                }
                                $row.data('id',data[i]._id.$id).data('data',data).data('estado',data.estado).contextMenu('conMenTsApr',{
                                    onShowMenu: function($row, menu) {
                                        if($row.data('data')[i].estado == 'A'){
                                            $('#conMenTsApr_apro',menu).remove();
                                        }
                                        return menu;
                                    },
                                    bindings: {
                                        'conMenTsApr_apro': function(t) {
                                            ciHelper.confirm('&#191;Desea <b>Aprobar</b> el Recibo Definitivo <b>'+K.tmp.find('td:eq(2)').html()+'</b>&#63;',
                                            function(){
                                                K.sendingInfo();
                                                $.post('ts/reca/save',{_id: K.tmp.data('id'),estado: 'A'},function(){
                                                    K.clearNoti();
                                                    K.msg({title: 'Recibo Aprobado',text: 'La aprobaci&oacute;n se realiz&oacute; con &eacute;xito!'});
                                                    tsRepo.init();
                                                });
                                            },function(){
                                                $.noop();
                                            },'Aprobaci&oacute;n de Recibo Definitivo');
                                        }
                                    }           
                                })
                            }
                            
                        }
                        p.$w.find('[name=aprobado]').val('S/. '+aprobado);
                        p.$w.find('[name=registrado]').val('S/. '+registrado);

                        


                        K.unblock();
                    },'json');

                    
                    
					
				});

				
			}
		});
	}
};
define(
	['mg/enti','ts/reca'],
	function(mgEnti,tsReca){
		return tsRepo;
	}
);