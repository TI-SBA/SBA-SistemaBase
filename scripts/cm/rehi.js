cmRehi = {
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
			color: "green",
			label: '<span class="label label-primary">Disponible</span>'
		},
		'C': {
			descr: 'Concedido',
			color: "blue",
			label: '<span class="label label-success">Concedido</span>'
		},
		R: {
			descr: "Con Registro Hist&oacute;rico",
			color: "pink"
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
		'CS':'Concesi&oacute;n',
		'CT':'Construcci&oacute;n',
		'AS':'Concesi&oacute;n de Nicho en Vida',
		'AD':'Adjuntaci&oacute;n',
		'TP':'Traspaso',
		'IN':'Inhumacion',
		'TI':'Traslado Interno',
		'TE':'Traslado Externo (hacia otro cementerio)',
		'TEO':'Traslado Externo (desde otro cementerio)',
		'CO':'Colocaci&oacute;n',
		'CV':'Conversi&oacute;n',
		'CN':'Cambio de Nombre en Recibo de Caja'
	},

    dbRel: function(item){
    return {
        _id: item._id.$id
        
    };
},
init: function(){
    K.initMode({
        mode: 'cm',
        action: 'cmRehi',
        titleBar: {
            title: 'Registro Historico'
        }
    });
    
    new K.Panel({
        onContentLoaded: function(){
               var $grid = new K.grid({
                cols: ['',{n:'Tipo',f:'tipo'},{n:'Nombre',f:'nomb'},'Ocupantes',{n:'Sector',f:'sector'}],
                data: 'cm/rehi/search',
                params: {},
                itemdescr: 'tipo(s)',
                toolbarHTML: '<p>Tipo de Espacio: <select name="tipo">'+
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
				 		'</select></p><p>'+
				 		'N&uacute;mero: <input type="text" class="form-control" size="6" name="num"/><button name=btnNum class="btn btn-primary" ><i class="fa fa-search"></i></button>'+
						'Fila <input type="text" class="form-control" size="6" name="fila" /><button name=btnFila class="btn btn-primary"><i class="fa fa-search"></i></button>&nbsp;'+
						'Piso: <input type="text" class="form-control" size="6" name="piso" /><button name=btnPiso class="btn btn-primary"><i class="fa fa-search"></i></button></p></td></tr></table>',
                onContentLoaded: function($el){
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
						$el.find('[name=btnNum],[name=btnFila],[name=btnPiso]').click(function(){
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
                onLoading: function(){ K.block(); },
                onComplete: function(){ $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
                 K.unblock(); },
                fill: function(data,$row){
                    $row.append('<td>');
                    $row.append('<td>'+cmRehi.estados[data.estado].descr+'</td>');
                    $row.append('<td>'+data.nomb+'</td>');
                    if(data.ocupantes!=null){
                        $row.append('<td></td>');
                        for(var i=0;i<data.ocupantes.length;i++){
                            if($row.find('td:last').html()!=''){
                                $row.find('td:last').append('<br /><hr />');
                            }
                            $row.find('td:last').append(''+mgEnti.formatName(data.ocupantes[i]));
                        }
                    }else{
                        $row.append('<td>')
                    }
                    $row.data('id',data._id.$id).dblclick(function(){
                        cmRehi.windowSubirArchivo({id: data._id.$id});
                    }).data('estado',data.estado).contextMenu("conMenCmRegi", {
                        /*onShowMenu: function($row, menu) {
                            $('#conMenCmRegi_ver',menu).remove();
                            if($row.data('estado')=='D') $('#conMenCmRegi_hab',menu).remove();
                            else $('#conMenCmRegi_edi,#conMenCmRegi_des',menu).remove();
                            return menu;
                        },*/
                        bindings: {
                            
                            /*'conMenCmEspa_upl': function(t) {
                                cmRehi.windowSubirArchivo({id: K.tmp.data('id')});
                            },*/
                            'conMenCmEspa_upl': function(t) {
                                cmRehi.windowSubirArchivo({id: K.tmp.data('id')});
                            },
                            'conMenCmEspa_vie': function(t) {
                                cmRehi.windowView({id: K.tmp.data('id')});
                            }
                        }
                    });
                    return $row;
                }
            });
        }
    });
},
windowSubirArchivo_: function(p){
    new K.Modal({
        id: 'windowSubirArchivo',
        contentURL: 'cm/rehi/upload',
        width: 750,
        height: 650,
        store: false,
        title: 'Subir Archivo',
        buttons: {
            "Cerrar": {
                icon: 'fa-ban',
                type: 'danger',
                f: function(){
                    K.closeWindow(p.$w.attr('id'));
                }
            }
        },
        onClose: function(){ p = null; },
        onContentLoaded: function(){
            p.$w = $('#windowSubirArchivo');
            p.$w.find("#file_upload").fileinput({
                language: "es",
                uploadUrl: "cm/rehi/subir",
                fileType: "any",
                previewFileIcon: "<i class='fa fa-king'></i>",
                uploadExtraData: function(){
                    return{
                        operacion: 'PE_PLAN_MAESTRO'
                    };
                },
                allowedFileExtensions: ["JPG","PNG","JPEG"]
            });

            p.$w.find('#file_upload').on('fileuploaded', function(event,params){
                K.clearNoti();
                //K.block();
                K.sendingInfo();
                var fila_id = p.fila_id;
                var storage = 'https://storage.googleapis.com/cementerio-storage/';
                var url_imagen = params.response.mediaLink;
                var preurl = url_imagen.replace(/.*\/o\//, "");
                var posturl = preurl.replace(/\.jpg.*/, ".jpg");
                url_imagen_ = storage+posturl;
                

                $.post('cm/rehi/save_digi',{_id:p.fila_id,url_imagen:url_imagen_},function(){
                    K.unblock();
                    K.notification({title: 'Documento esta Digitalizado',text: 'La digitalizaci&oacute;n se realiz&oacute; con &eacute;xito!'});
                    K.closeWindow(p.$w.attr('id'));
                },'json');
            });


            
        }
    });
},
windowSubirArchivo: function(p){
    if(p==null) p={};
    new K.Panel({
        contentURL: 'cm/rehi/view',
        store: false,
        title: 'Subir Archivo',
        buttons: {
            "Cerrar": {
                icon: 'fa-ban',
                type: 'danger',
                f: function(){
                    cmRehi.init();
                }
            }
        },
        onClose: function(){ p = null; },
        onContentLoaded: function(){
            p.$w = $('#mainPanel');
            $.post('cm/espa/get',{_id: p.id},function(data){
                p.$w.find('[name=nomb]').html(data.nomb);
                p.$w.find('[name=sector]').html(data.sector);
                p.$w.find('[name=capa]').html(data.capacidad);
                p.$w.find('[name=estado]').html(cmRehi.estados[data.estado].descr);

                if(data.nicho !=null){
                    /*p.$w.find('[name=pisos]').val(data.nicho.piso);
                    p.$w.find('[name=num]').val(data.nicho.num);
                    p.$w.find('[name=pabellon]').html(data.nicho.pabellon.nomb).data('data',data.nicho.pabellon);
                    p.$w.find('[name=sector]').html('Cuadrante '+data.sector);*/
                }else {
                    p.$w.find('[name=zona]').html(cmRehi.zonas[data.mausoleo.zona].descr);
                    
                }
                new K.grid({
                    $el: p.$w.find('[name=gridHis]'),
                    cols: ['ID','Tipo','Fecha','Digitalizar Recibo'],
                    stopLoad: true,
                    pagination: false,
                    search: false,
                    store:false,
                    toolbarHTML: '<button type="button" name="btnAddReci" class="btn btn-primary"></button >',
                    onContentLoaded: function($el){
                        $el.find('button').click(function(){
                            var $row = $('<tr class="item">');
                            $row.append('<td><span class="form-control" name="id"></span></td>');
                            $row.append('<td><span class="form-control" name="tipo"></span></td>');
                            $row.append('<td><span class="form-control" name="fecha"></span></td>');
                            /*$row.append('<td style="width:300px"><span class="form-control" name="trabajador"></span></td>');
                            $row.append('<td><span class="form-control" name="obser"></span></td>');*/
                            $row.append('<td><button type="button" name="btnDigi" class="btn btn-success"><i class="fa fa-plus"></i></button ></td>');
                            p.$w.find('[name=gridHis] tbody').append($row);
                        });
                    },
                    onComplete: function(){
                        //$.post('cm/oper/all_hist',espacio: p.id,function(data){
                            $.post('cm/oper/all_hist',{espacio:p.id},function(data){
                            if(data!=null){
                               //if(data.opers!=null){
                                    for(var i=0;i<data.length;i++){
                                        p.$w.find('[name=btnAddReci]').click();
                                        var $row = p.$w.find('[name=gridHis] tbody tr:last');
                                        var result = data[i];
                                        console.log(result);         
                                        $row.attr('data-id', result._id.$id);
                                        //$row.find('[name=estado_]').html('Registrado');
                                        $row.find('[name=id]').html(result._id.$id);
                                        
                                        if(result.tipo != null){
                                            $row.find('[name=tipo]').html(cmRehi.tipo_oper[result.tipo]);
                                        }
                                        if(result.fecoper != null){
                                            $row.find('[name=fecha]').html(moment(result.fecoper.sec,'X').format('YYYY-MM-DD'));
                                        }
                                       
                                       /* if(result.concesion !=null){
                                            $row.find('[name=tipo]').html('Concesi&oacute;n');
                                        }else if(result.construccion != null){
                                            $row.find('[name=tipo]').html('Construcci&oacute;n');
                                        }else if(result.asignacion != null){                           
                                            $row.find('[name=tipo]').html( 'Asignaci&oacute;n' );
                                        }else if(result.adjuntacion != null){
                                            $row.find('[name=tipo]').html( 'Adjuntaci&oacute;n' );
                                        }else if(result.traspaso != null){
                                            $row.find('[name=tipo]').html( 'Traspaso' );
                                        }else if(result.inhumacion != null){
                                            $row.find('[name=tipo]').html( 'Inhumaci&oacute;n' );
                                        }else if(result.traslado != null){
                                            $row.find('[name=tipo]').html( 'Traslado' );
                                        }else if(result.colocacion != null){
                                            $row.find('[name=tipo]').html( 'Colocaci&oacute;n' );
                                        }else if(result.anular_asignacion != null){
                                            $row.find('[name=tipo]').html( 'Anular Asignaci&oacute;n' );
                                        }else if(result.anular_concesion != null){
                                            $row.find('[name=tipo]').html( 'Anular Concesi&oacute;n' );
                                        }else if(result.conversion != null){
                                            $row.find('[name=tipo]').html( 'Conversi&oacute;n' );
                                        }
            
                                        if(result.anulacion!=null){
                                            $row.find('[name=trabajador]').html('<span style="color:red;">Anulada</span>'+'<br />'+mgEnti.formatName(result.trabajador) );
                                        }else if(result.ejecucion!=null){
                                            $row.find('[name=trabajador]').html('Ejecutado'+'<br />'+mgEnti.formatName(result.trabajador) );
                                        }else{
                                            $row.find('[name=trabajador]').html('Activa'+'<br />'+mgEnti.formatName(result.trabajador));
                                        }
                                        if(result.recibos==null){
                                            $row.find('[name=obser]').html(moment(result.fecreg.sec,'X').format('YYYY-MM-DD'));
                                        }else{
                                            $row.find('[name=obser]').html(moment(result.fecreg.sec,'X').format('YYYY-MM-DD')+'<br />'
                                            +'<b>RC '+result.recibos[0].serie+'-'+result.recibos[0].num+'</b><br />'
                                            +mgEnti.formatName(result.recibos[0].cliente));
                                        }
                                        */
                                        $row.find('[name=btnDigi]').click(function(){
                                            var fila_id = $(this).closest('tr').data('id');
                                            cmRehi.windowSubirArchivo_({
                                                fila_id: fila_id,
                                                callback:function(fila_id){
                                            },bootstrap: true});
                                        });
                                        
                                    }
                                //}
                            }
                            
                        },'json');
                    }
                });
                
            },'json');
            
           
          
        }
    });
},
/*windowView: function(p){
    if(p==null) p={};
    new K.Panel({
        contentURL: 'cm/rehi/view',
        store: false,
        title: 'Subir Archivo',
        buttons: {
            "Cancelar": {
                icon: 'fa-ban',
                type: 'danger',
                f: function(){
                    cmRehi.init();
                }
            }
        },
        onClose: function(){ p = null; },
        onContentLoaded: function(){
            p.$w = $('#mainPanel');
        }
    });
}*/

};
define(
['mg/enti'],
function(mgEnti){
    return cmRehi;
}
);