cjEcom = {
  digitRedo: 2,
  monedas: [{
      cod: 'S',
      label: 'S/.',
      descr: 'Nuevos Soles'
    },
    {
      cod: 'D',
      label: '$',
      descr: 'Dolares'
    }
  ],
  states: {
    BO: {
      descr: "Borrador",
      color: "green",
      label: '<span class="label label-info">Borrador</span>'
    },
    CO: {
      descr: "Confirmado",
      color: "green",
      label: '<span class="label label-warning">Confirmado</span>'
    },
    FI: {
      descr: "Firmado",
      color: "green",
      label: '<span class="label label-primary">Firmado</span>'
    },
    ES: {
      descr: "Enviado SUNAT",
      color: "#CCCCCC",
      label: '<span class="label label-success">Enviado SUNAT</span>'
    },
    X: {
      descr: "Dado de baja SUNAT",
      color: "black",
      label: '<span class="label label-danger">Peticion de baja</span>'
    },
  },
  init: function() {
    K.initMode({
      mode: 'cj',
      action: 'cjEcom',
      titleBar: {
        title: 'Comprobantes Electronicos'
      }
    });

    new K.Panel({
      onContentLoaded: function() {
        $.post('cj/caje/cajas_trabajador', {
          _id: K.session.enti._id.$id
        }, function(cajas) {
          if (cajas != null) {
            if (cajas.length > 0) {
              var $grid = new K.grid({
                //cols: ['','Fecha de Reg.','Estado','Estado SUNAT','NUMERO','DOC. IDEN.','NOMBRES / RAZON SOCIAL','OPE.GRAV','IGV','TOTAL'],
                cols: ['', 'Fecha de Emi.', 'Estado', 'Estado SUNAT', 'NUMERO', 'DOC. IDEN.', 'NOMBRES / RAZON SOCIAL', 'OPE.GRAV', 'IGV', 'TOTAL', 'AUTOR'],
                data: 'cj/ecom/lista',
                params: {
                  caja: cajas[0]._id.$id
                },
                itemdescr: 'Comprobantes(s)',
                toolbarHTML: '<select name="caja" class="form-control"></select><button type="button" class="btn btn-primary" name="btnAgregar2">Nuevo EComprobante</button><button type="button" class="btn btn-outline-primary" name="btnActualizarTc">Actualizar Tipo de Cambio <span name="tc_span" class="badge badge-info" style="display:none">New</span></button>',
                onContentLoaded: function($el) {
                  //$('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height())+240+'px');
                  $('#mainPanel .fuelux').height(parseFloat($('#mainPanel .fuelux').height('auto')) + 240 + 'px');
                  for (var i = 0; i < cajas.length; i++) {
                    $el.find('[name=caja]').append('<option value="' + cajas[i]._id.$id + '">' + cajas[i].nomb + '</option>');
                    $el.find('[name=caja]').find('option:last').data('data', cajas[i]);
                  }

                  $el.find('[name=btnAgregar2]').click(function() {
                    cjEcom.windowComprobante({
                      caja: {
                        _id: $el.find('[name=caja] :selected').val(),
                        nomb: $el.find('[name=caja] :selected').text(),
                        modulo: $el.find('[name=caja] :selected').data('data').modulo
                      }
                    });
                  });
                  $el.find('[name=btnActualizarTc]').click(function() {
                    $.post('mg/vari/get_sunat_tc', {}, function(tc) {
                      $el.find('[name=tc_span]').html(tc).show();
                    });
                  });
                  $el.find('[name=caja]').change(function() {
                    $grid.reinit({
                      params: {
                        caja: $el.find('[name=caja] :selected').val()
                      }
                    });
                  });
                },
                onLoading: function() {
                  K.block();
                },
                onComplete: function() {
                  K.unblock();
                },
                fill: function(data, $row) {
                  $row.append('<td>');
                  $row.append('<td>' + moment(data.fecemi.sec, 'X').format('DD/MM/YYYY') + '</td>');
                  $row.append('<td>' + cjEcom.states[data.estado].label + '</td>');
                  $row.append('<td>' + data.sunat_responsecode + '</td>');
                  $row.append('<td>' + data.serie + ' ' + ((data.numero.value != null) ? data.numero.value : "") + '</td>');
                  $row.append('<td>' + data.cliente_doc + '</td>');
                  $row.append('<td>' + data.cliente_nomb + '</td>');
                  $row.append('<td>' + data.total_ope_gravadas + '</td>');
                  $row.append('<td>' + data.total_igv + '</td>');
                  $row.append('<td>' + data.total + '</td>');
                  $row.append('<td>' + ' Reg&iacute;stro: <span>' + ciHelper.date.format.bd_ymdhi(data.fecreg) + '</span><br />' +
                    ' Modificaci&oacuten: <span>' + ciHelper.date.format.bd_ymdhi(data.fecmod) + '</span><br />' +
                    ' Confirmaci&oacuten: <span>' + ciHelper.date.format.bd_ymdhi(data.feccon) + '</span><br />' +
                    ' GENERADO POR: <span style="font-weight: 900; color: #7014CC; font-style: oblique;">' + mgEnti.formatName(data.autor) + '</span><br />' +
                    ' ENVIADO POR: <span style="font-weight: 900; color: #7014CC; font-style: oblique;">' + mgEnti.formatName(data.autor_con) + '</span><br />' +
                    ' DADO DE BAJA POR: <span style="font-weight: 900; color: #7014CC; font-style: oblique;">' + mgEnti.formatName(data.autor_anu) + '</span></td>');
                  //AQUI SE ENVIA LAS FUNCIONES AL MENU, ASI QUE AQUI AGREGARE LO DEMAS
                  $row.data('id', data._id.$id).data('data', data).data('estado', data.estado).data('nota_serie', data.serie).data('nota_numero', data.numero).data('nota_tipo', data.tipo).data('nota_caja', data.caja).contextMenu("conMenEcom", {
                    onShowMenu: function($row, menu) {
                      $('#conMenList_edi,#conMenList_imp', menu).remove();
                      $('#conMenEcom_env', menu).remove();
                      //NO SE DONDE VIENE EL BOTON VER, como no se utiliza, se comenta #conMenEcom_ver
                      $('#conMenEcom_ver', menu).remove();
                      //EL SISTEMA NO GENERARA NOTAS DE DEBITO, como no se utiliza, se comenta #conMenEcom_fop
                      //POR SEGURIDAD SE OCULTARA NOTAS DE CREDITO, como no se utiliza, se comenta #conMenEcom_fop
                      $('#conMenEcom_gnd', menu).remove();

                      //SOLO SE EDITA O SE FIRMA SI ES UN BORRADOR
                      if ($row.data('data').estado != 'BO') {
                        $('#conMenEcom_edi,#conMenEcom_fir', menu).remove();
                      }
                      //SOLO SE FIRMA SI ES UNA FACTURA,BOLETA,NOTA DE DEBITO Y NOTA DE CREDITO
                      if ($row.data('data').tipo != 'F' && $row.data('data').tipo != 'B' && $row.data('data').tipo != 'ND' && $row.data('data').tipo != 'NC') {
                        $('#conMenEcom_fir', menu).remove();
                      }
                      //SI NO ES FACTURA O BOLETA NO SE DEBE EMITIR NOTA DE CREDITO Y DE DEBITO
                      if ($row.data('data').tipo != 'F' && $row.data('data').tipo != 'B') {
                        $('#conMenEcom_gnc, #conMenEcom_gnd', menu).remove();
                      }
                      //SOLO SE ANULARA BOLETAS, IMPRIMIRA COMP ELECTRONICO Y GENERARA NOTAS DE DEBITO O CREDITO SI ES QUE ESTAN FIRMADAS O ENVIADAS A LA SUNAT
                      if ($row.data('data').estado != 'FI' && $row.data('data').estado != 'ES') {
                        //$('#conMenEcom_gnc,#conMenEcom_gnd,#conMenEcom_anu,#conMenEcom_im2',menu).remove();
                        $('#conMenEcom_gnc,#conMenEcom_gnd,#conMenEcom_fop,#conMenEcom_anu', menu).remove();
                      }
                      //SI SE DIO LA PETICION DE BAJA, SOLO DEBE PODER VER LA VERSION IMPRESA DE ESA BOLETA
                      if ($row.data('data').estado == 'X') {
                        //$('#conMenEcom_edi,#conMenEcom_fir,#conMenEcom_anu,#conMenEcom_imp,#conMenEcom_im2,#conMenEcom_gnc,#conMenEcom_gnd',menu).remove();
                        $('#conMenEcom_edi,#conMenEcom_fir,#conMenEcom_anu,#conMenEcom_gnc,#conMenEcom_gnd,#conMenEcom_imp', menu).remove();
                      }
                      //SOLO SI ES UNA FACTURA o NOTA DE CREDITO, Y ESTA FIRMADA SE PODRA VERIFICAR SU ESTADO
                      if (($row.data('data').tipo != 'F' && $row.data('data').tipo != 'NC') || $row.data('data').estado != 'FI') {
                        $('#conMenEcom_chk', menu).remove();
                      }

                      //SOLO SI ESTA ENVIADA A SUNAT SE PUEDE ANULAR
                      if (($row.data('data').tipo == 'F' || $row.data('data').tipo == 'NC') && $row.data('data').estado != 'ES') {
                        $('#conMenEcom_anu', menu).remove();
                      }

                      //SOLO SI NO ES UN BORRADOR
                      if (($row.data('data').estado != 'BO')) {
                        $('#conMenEcom_del', menu).remove();
                      }

                      //if($row.data('data').estado=='FI' || $row.data('data').estado=='ES'){
                      //	$('#conMenEcom_fir',menu).remove();
                      //}

                      //if($row.data('data').estado!='FI' && $row.data('data').estado!='ES'){
                      //	$('#conMenEcom_anu',menu).remove();
                      //}
                      return menu;
                    },
                    bindings: {
                      //EDITAR COMPROBANTE ELECTRONICO
                      'conMenEcom_edi': function(t) {
                        cjEcom.windowComprobante({
                          id: K.tmp.data('id')
                        });
                      },
                      //IMPRIMIR REPRESENTACION IMPRESA
                      'conMenEcom_fir': function(t) {
                        cjEcom.widnowConfirm({
                          id: K.tmp.data('id')
                        });
                      },
                      //IMPRIMIR COMPROBANTE IMPRESA
                      'conMenEcom_imp': function(t) {
                        window.open('cj/ecom/print_preview?_id=' + K.tmp.data('data')._id.$id);
                      },
                      //DAR DE BAJA A LA BOLETA
                      'conMenEcom_anu': function(t) {
                        ciHelper.confirm('&#191;Desea <b>Dar de baja</b> el Comprobante <b>' + K.tmp.find('td:eq(4)').html() + ' ' + K.tmp.find('td:eq(3)').html() + '</b>&#63;',
                          function() {
                            p = {};
                            new K.Modal({
                              id: 'windowPetiBaja',
                              title: 'Motivo de petici&oacute;n de Baja',
                              contentURL: 'cj/ecom/baja_edit',
                              width: 900,
                              height: 100,
                              store: false,
                              buttons: {
                                "Guardar": {
                                  icon: 'fa-save',
                                  type: 'success',
                                  f: function() {
                                    K.clearNoti();
                                    var form = ciHelper.validator(p.$w.find('form'), {
                                      onSuccess: function() {
                                        K.sendingInfo();
                                        p.$w.find('#div_buttons button').attr('disabled', 'disabled');
                                        if (p.$w.find('[name=motivo_baja]').val() == '') {
                                          return K.notification({
                                            title: ciHelper.titleMessages.infoReq,
                                            text: 'No se introdujo un motivo para la peticion de baja',
                                            type: 'error'
                                          });
                                        }
                                        $.post('cj/ecom/anular', {
                                          _id: K.tmp.data('id'),
                                          motivo_baja: p.$w.find('[name=motivo_baja]').val()
                                        }, function() {
                                          K.clearNoti();
                                          K.notification({
                                            title: 'Comprobante Anulado',
                                            text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'
                                          });
                                          K.closeWindow(p.$w.attr('id'));
                                          cjEcom.init();
                                        });
                                      }
                                    }).submit();
                                  }
                                },
                                "Cancelar": {
                                  icon: 'fa-ban',
                                  type: 'danger',
                                  f: function() {
                                    K.closeWindow(p.$w.attr('id'));
                                  }
                                }
                              },
                              onClose: function() {
                                p = null;
                              },
                              onContentLoaded: function() {
                                p.$w = $('#windowPetiBaja');
                              }
                            });
                          },
                          function() {
                            $.noop();
                          }, 'Anulaci&oacute;n de Comprobante');
                      },
                      //CAMBIAR FORMA DE PAGO, COMO VOUCHERS DE DETRACCION
                      'conMenEcom_fop': function(t) {
                        cjEcom.windowVoucher({
                          id: K.tmp.data('id'),
                          nomb: K.tmp.find('td:eq(3)').html()
                        });
                      },
                      //GENERAR NOTA DE DEBITO
                      'conMenEcom_gnc': function(t) {
                        ciHelper.confirm('&#191;Desea generar una <b>Nota de Credito</b> para el Comprobante <b>' + K.tmp.find('td:eq(4)').html() + ' ' + K.tmp.find('td:eq(3)').html() + '</b>&#63;',
                          function() {
                            cjEcom.windowComprobante({
                              caja: {
                                _id: K.tmp.data('nota_caja')._id.$id,
                                nomb: K.tmp.data('nota_caja').nomb,
                                modulo: K.tmp.data('nota_caja').modulo,
                              },
                              nota: 'NC',
                              nota_serie: K.tmp.data('nota_serie'),
                              nota_numero: K.tmp.data('nota_numero'),
                              nota_tipo: K.tmp.data('nota_tipo'),
                            });
                          },
                          function() {
                            $.noop();
                          }, 'Generar Nota de debito del Comprobante');
                      },
                      //IMPRIMIR BOLETA ELECTRONICA
                      'conMenEcom_im2': function(t) {
                        if (K.tmp.data('data').ruta_pdf != '') {
                          //window.open('conflux_see_server/'+K.tmp.data('data').ruta_pdf);
                          window.open('http://35.193.115.148/' + K.tmp.data('data').ruta_pdf);
                        } else if (!isNaN(K.tmp.data('data').conflux_see_id)) {
                          window.open('https://einvoice.conflux.pe/cpe/' + K.tmp.data('data').conflux_see_id + '.pdf');
                        } else {
                          return K.notification({
                            title: 'Respuesta del sistema',
                            text: 'No esta disponible la representacion impresa',
                            type: 'error'
                          });
                        }
                      },
                      //GENERAR NOTA DE DEBITO
                      /*'conMenEcom_gnd': function(t) {
                      	ciHelper.confirm('&#191;Desea generar una <b>Nota de Debito</b> para el Comprobante <b>'+K.tmp.find('td:eq(4)').html()+' '+K.tmp.find('td:eq(3)').html()+'</b>&#63;',
                      	function(){
                      		console.log(K.tmp.data('nota_serie'));
                      		console.log(K.tmp.data('nota_numero'));
                      		console.log(K.tmp.data('nota_tipo'));
                      		cjEcom.windowComprobante({
                      			id:K.tmp.data('id'),
                      			nota:'ND',
                      			nota_serie:K.tmp.data('nota_serie'),
                      			nota_numero:K.tmp.data('nota_numero'),
                      			nota_tipo:K.tmp.data('nota_tipo'),
                      		});
                      		//K.sendingInfo();
                      		//$.post('cj/ecom/anular',{_id: K.tmp.data('id')},function(){
                      		//	K.clearNoti();
                      		//	K.notification({title: 'Comprobante Anulado',text: 'La anulaci&oacute;n se realiz&oacute; con &eacute;xito!'});
                      		//cjEcom.init();
                      		//});
                      	},function(){
                      		$.noop();
                      	},'Generar Nota de debito del Comprobante');
                      },*/
                      //VERIFICAR ESTADO DEL COMPROBANTE
                      'conMenEcom_chk': function(t) {
                        ciHelper.confirm('&#191;Desea <b>Verificar Estado</b> del Comprobante <b>' + K.tmp.find('td:eq(4)').html() + ' ' + K.tmp.find('td:eq(3)').html() + '</b>&#63;',
                          function() {
                            K.sendingInfo();
                            $.post('cj/ecom/verificacion_estado', {
                              _id: K.tmp.data('id')
                            }, function() {
                              K.clearNoti();
                              K.notification({
                                title: 'Comprobante Verificado',
                                text: 'La verificaci&oacute;n se realiz&oacute; con &eacute;xito!'
                              });
                              cjEcom.init();
                            });
                          },
                          function() {
                            $.noop();
                          }, 'Anulaci&oacute;n de Comprobante');
                      },
                      //ELIMINAR BORRADOR DEL COMPROBANTE
                      'conMenEcom_del': function(t) {
                        ciHelper.confirm('&#191;Desea <b>ELIMINAR EL BORRADOR</b> del Comprobante <b>' + K.tmp.find('td:eq(4)').html() + ' ' + K.tmp.find('td:eq(3)').html() + '</b>&#63;',
                          function() {
                            K.sendingInfo();
                            $.post('cj/ecom/eliminar', {
                              _id: K.tmp.data('id')
                            }, function() {
                              K.clearNoti();
                              K.notification({
                                title: 'Comprobante Eliminado',
                                text: 'Se elimino el comprobante con exito!'
                              });
                              cjEcom.init();
                            });
                          },
                          function() {
                            $.noop();
                          }, 'Anulaci&oacute;n de Comprobante');
                      },
                    }
                  });
                  return $row;
                }
              });
            }
          }
        }, 'json');
      }
    });
  },
  saveComprobante: function(p) {
    var data = {
      tipo: p.$w.find('[name=tipo] :selected').val(),
      tipo_oper: '01',
      tipo_doc: p.$w.find('[name=cliente_tipo_documento] :selected').val(),
      serie: p.$w.find('[name=serie] :selected').val(),
      numero: p.$w.find('[name=numero]').val(),
      cliente_nomb: p.$w.find('[name=cliente_nomb]').val(),
      cliente_doc: p.$w.find('[name=cliente_num_documento]').val(),
      cliente_domic: p.$w.find('[name=cliente_direccion]').val(),
      cliente_email_1: p.$w.find('[name=cliente_email_1]').val(),
      fecemi: p.$w.find('[name=fecemi]').val(),
      fecven: p.$w.find('[name=fecven]').val(),
      moneda: p.$w.find('[name=moneda] :selected').val(),
      tipo_cambio: p.$w.find('[name=tipo_cambio]').val(),
      porcentaje_igv: p.$w.find('[name=porcentaje_igv]').val(),
      total_detraccion: p.$w.find('[name=total_detraccion]').val(),
      porcentaje_detraccion: p.$w.find('[name=porcentaje_detraccion]').val(),
      observ: p.$w.find('[name=observ]').val(),
      items: []
    };
    if(data.porcentaje_detraccion==0)
        data.total_detraccion=0;

    if (p.$w.find('[name=header_caja]').data('data') != null) {
      data.caja = {
        _id: p.$w.find('[name=header_caja]').data('data')._id,
        nomb: p.$w.find('[name=header_caja]').data('data').nomb,
        modulo: p.$w.find('[name=header_caja]').data('data').modulo,
      };
    }

    //SI SOLO SI NO SE DETECTA LA VARIABLE NOTA
    if (p.nota != null) {
      data.documento_modificar = {
        tipo: p.$w.find('[name=tipo_paranota]').val(),
        serie: p.$w.find('[name=serie_paranota]').val(),
        numero: p.$w.find('[name=numero_paranota]').val(),
        motivo: p.$w.find('[name=motivo_nota]').val(),
        motivo_descripcion: p.$w.find('[name=desc_nota]').val(),
      };
    } else {
      if (p.$w.find('[name=tipo]').val() == 'NC') {
        data.documento_modificar = {
          tipo: p.$w.find('[name=tipo_paranota]').val(),
          serie: p.$w.find('[name=serie_paranota]').val(),
          numero: p.$w.find('[name=numero_paranota]').val(),
          motivo: p.$w.find('[name=motivo_nota_NC]').val(),
          motivo_descripcion: p.$w.find('[name=desc_nota_NC]').val()
        };
      } else if (p.$w.find('[name=tipo]').val() == 'ND') {
        data.documento_modificar = {
          tipo: p.$w.find('[name=tipo_paranota]').val(),
          serie: p.$w.find('[name=serie_paranota]').val(),
          numero: p.$w.find('[name=numero_paranota]').val(),
          motivo: p.$w.find('[name=motivo_nota_ND]').val(),
          motivo_descripcion: p.$w.find('[name=desc_nota_ND]').val()
        };
      }
    }
    if (data.tipo=="F" && data.tipo_doc!="RUC"){
      return K.notification({
        title: ciHelper.titleMessages.infoReq,
        text: 'Has elegido crear una factura, Debes de ingresar un número de RUC',
        type: 'error'
      });
    }
    
    if (p.id != null) data._id = p.id;
    if (p.$w.find('[name=grid] tbody tr').length > 0) {
      for (var i = 0; i < p.$w.find('[name=grid] tbody tr').length; i++) {
        var $row = p.$w.find('[name=grid] tbody tr').eq(i);
        if ($row.attr('parent_uid') != null) continue;
        var _item = $row.data('data');
        var descripcion = $row.find('[name=descripcion]').val();
        var cod_unidad = $row.find('[name=unidad] :selected').val();
        var unidad = $row.find('[name=unidad] :selected').text();
        var cantidad = $row.find('[name=cantidad]').val();
        var valor_unitario = $row.find('[name=valor_unitario]').val();
        var igv = $row.find('[name=igv]').val();
        var precio_venta_unitario = $row.find('[name=precio_venta_unitario]').val();
        var precio_total = $row.find('[name=precio_total]').val();
        _item.descr = descripcion;
        _item.unidad = unidad;
        _item.cant = cantidad;
        _item.valor_unitario = valor_unitario;
        _item.igv = igv;
        _item.precio_venta_unitario = precio_venta_unitario;
        _item.precio_total = precio_total;
        if (_item.descr == '') {
          return K.notification({
            title: ciHelper.titleMessages.infoReq,
            text: 'La descripcion en el item' + (i + 1) + ' no puede estar vacia',
            type: 'error'
          });
        }
        if (_item.cant == '') {
          return K.notification({
            title: ciHelper.titleMessages.infoReq,
            text: 'La cantidad en el item' + (i + 1) + ' no puede estar vacia',
            type: 'error'
          });
        }
        if (_item.valor_unitario == '') {
          return K.notification({
            title: ciHelper.titleMessages.infoReq,
            text: 'El valor unitario en el item' + (i + 1) + ' no puede estar vacia',
            type: 'error'
          });
        }
        if (_item.igv == '') {
          return K.notification({
            title: ciHelper.titleMessages.infoReq,
            text: 'El IGV en el item' + (i + 1) + ' no puede estar vacia',
            type: 'error'
          });
        }
        if (_item.precio_total == '') {
          return K.notification({
            title: ciHelper.titleMessages.infoReq,
            text: 'El precio total en el item' + (i + 1) + ' no puede estar vacia',
            type: 'error'
          });
        }

        if (p.$w.find('[parent_uid=' + $row.attr('uid') + ']').length > 0) {
          _item.subitems = [];
          for (var j = 0; j < p.$w.find('[parent_uid=' + $row.attr('uid') + ']').length; j++) {
            var $subrow = p.$w.find('[parent_uid=' + $row.attr('uid') + ']').eq(j);

            var _subitem = $subrow.data('data');
            var descripcion = $subrow.find('[name=descripcion]').val();
            var cod_unidad = $subrow.find('[name=unidad] :selected').val();
            var unidad = $subrow.find('[name=unidad] :selected').text();
            var cantidad = $subrow.find('[name=cantidad]').val();
            var valor_unitario = $subrow.find('[name=valor_unitario]').val();
            var igv = $subrow.find('[name=igv]').val();
            var precio_venta_unitario = $subrow.find('[name=precio_venta_unitario]').val();
            var precio_total = $subrow.find('[name=precio_total]').val();
            _subitem.descr = descripcion;
            _subitem.unidad = unidad;
            _subitem.cant = cantidad;
            _subitem.valor_unitario = valor_unitario;
            _subitem.igv = igv;
            _subitem.precio_venta_unitario = precio_venta_unitario;
            _subitem.precio_total = precio_total;
            _item.subitems.push(_subitem);
          }
        }
        data.items.push(_item);
      }
    } else {
      return K.notification({
        title: ciHelper.titleMessages.infoReq,
        text: 'El comprobante no puede ser emitido sin tener al menos un item',
        type: 'error'
      });
    }
    data.total_isc = p.$w.find('[name=total_isc]').val();
    data.total_igv = p.$w.find('[name=total_igv]').val();
    data.total_otros_tributos = 0;
    data.total_otros_cargos = 0;
    data.total_ope_inafectas = p.$w.find('[name=total_inafectas]').val();
    data.total_ope_gravadas = p.$w.find('[name=total_gravadas]').val();
    data.total_desc = 0;
    data.total_ope_exoneradas = p.$w.find('[name=total_exoneradas]').val();
    data.total_ope_gratuitas = p.$w.find('[name=total_gratuitas]').val();
    data.total = p.$w.find('[name=total]').val();

    var tot = 0;
    tot += parseFloat(p.$w.find('[name=mon_sol] [name=tot]').val());
    tot += parseFloat(p.$w.find('[name=mon_dol] [name=tot]').val()) * parseFloat(data.tipo_cambio);
    data.efectivos = [{
        moneda: 'S',
        monto: parseFloat(p.$w.find('[name=mon_sol] [name=tot]').val())
      },
      {
        moneda: 'D',
        monto: parseFloat(p.$w.find('[name=mon_dol] [name=tot]').val())
      }
    ];
    for (var i = 0, j = p.$w.find('[name=ctban]').length; i < j; i++) {
      var tmp = {
        num: p.$w.find('[name=ctban]').eq(i).find('[name=voucher]').val(),
        monto: parseFloat(p.$w.find('[name=ctban]').eq(i).find('[name=tot]').val()),
        moneda: p.$w.find('[name=ctban]').eq(i).data('moneda'),
        cuenta_banco: p.$w.find('[name=ctban]').eq(i).data('data')
      };
      if (tmp.monto > 0) {
        if (tmp.num == '') {
          p.$w.find('[name=ctban]').eq(i).find('[name=voucher]').focus();
          return K.msg({
            title: ciHelper.titles.infoReq,
            text: 'Debe ingresar un n&uacute;mero de voucher!',
            type: 'error'
          });
        }
        if (data.vouchers == null) data.vouchers = [];
        data.vouchers.push(tmp);
        tot += (tmp.moneda == 'S') ? tmp.monto : tmp.monto * data.tipo_cambio;
      }
    }

    if (data.tipo=="B" && data.total>=700 && data.tipo_doc!=="DNI" && data.tipo_doc!=="RUC"){
        return K.notification({
          title: ciHelper.titleMessages.infoReq,
          text: 'El comprobante (Boleta) para sumas mayores a 700 debe ser un cliente registrado [Nº DOCUMENTO, NOMBRE y DIRECCION]',
          type: 'error'
        });
    }

    console.log('DATA.TOTAL ==== ' + K.round(data.total, 2));
    console.log('TOT === ' + K.round(tot, 2));
    /*if(parseFloat(K.round(data.total,2))!=parseFloat(K.round(tot,2))){
    	return K.msg({
    		title: ciHelper.titles.infoReq,
    		text: 'El total del comprobante no coincide con el total de la forma de pagar!',
    		type: 'error'
    	});
    }*/

    K.block();

    //SI SOLO SI NO SE DETECTA LA VARIABLE NOTA
    //if(p.nota!=null){
    //	$.post('cj/ecom/save_nota',data,function(comp){
    //		K.clearNoti();
    //		K.msg({
    //			title: 'Respuesta del sistema',
    //			text: comp.message,
    //			type: comp.status
    //		});
    //		if(comp.status=='success'){
    //			cjEcom.widnowConfirm({id:comp.data.id});
    //		}else{
    //			K.unblock();
    //		}
    //	},'json');
    //}else{
    //console.log("value data="+JSON.stringify(data));
    $.post('cj/ecom/save', data, function(comp) {
      K.clearNoti();
      K.msg({
        title: 'Respuesta del sistema',
        text: comp.message,
        type: comp.status
      });
      if (comp.status == 'success') {
        //console.log("responce POST"+JSON.stringify(comp));
        cjEcom.widnowConfirm({
          id: comp.data.id
        });
      } else {
        //console.log("responce POST"+JSON.stringify(comp));
        K.unblock();
      }
    }, 'json');
    //}
  },
  windowComprobante: function(p) {
    if (p == null) p = {};
    p.config = {
      moneda: 'S'
    };
    p.get_config = function() {
      return $.extend(p.config, {
        tc: p.$w.find('[name=tipo_cambio]').val(),
        igv: parseFloat(p.$w.find('[name=porcentaje_igv]').val()) / 100,
        detraccion: p.$w.find('[name=porcentaje_detraccion] :selected').val(),
        moneda: p.$w.find('[name=moneda] :selected').val(),
        almacen: p.$w.find('[name=almacen] :selected').val()
      });
    };
    p.calcTot = function() {
      var total_ope_gravadas = 0;
      var total_ope_inafectas = 0;
      var total_ope_exoneradas = 0;
      var total_ope_gratuitas = 0;
      var total_igv = 0;
      var total_isc = 0;
      var total_descuentos = 0;
      var tipo_cambio = p.$w.find('[name=tipo_cambio]').val();
      var moneda = p.$w.find('[name=moneda] :selected').val();
      if (p.$w.find('[name=grid] tbody tr').length > 0) {
        for (var i = 0; i < p.$w.find('[name=grid] tbody tr').length; i++) {
          var $row = p.$w.find('[name=grid] tbody tr').eq(i);
          var data = $row.data('data');
          var igv = $row.find('[name=igv]').val();
          if (!parseFloat(igv)) {
            igv = 0;
          } else {
            igv = parseFloat(igv);
          }
          var valor_unitario = $row.find('[name=valor_unitario]').val();
          if (!parseFloat(valor_unitario)) {
            valor_unitario = 0;
          } else {
            valor_unitario = parseFloat(valor_unitario);
          }
          var cantidad = $row.find('[name=cantidad]').val();
          if (!parseFloat(cantidad)) {
            cantidad = 0;
          } else {
            cantidad = parseFloat(cantidad);
          }
          //var precio_total = $row.find('[name=precio_total]').val();
          //if(!parseFloat(precio_total)){
          //	precio_total = 0;
          //}else{
          //	precio_total = parseFloat(precio_total);
          //}

          var importe_total = $row.find('[name=importe_total]').val();
          if (!parseFloat(importe_total)) {
            importe_total = 0;
          } else {
            importe_total = parseFloat(importe_total);
          }
          if (data != null) {
            if (data.tipo_igv != null) {

              if (data.tipo == "agua_chapi" || data.tipo == "farmacia") {
                console.log("SE DEBE USAR EL METODO DIFERENCIA");
                if (data.tipo_igv == '10' || data.tipo_igv == '17') { //verificar la validez del 17
                  total_ope_gravadas += data.conceptos[0].monto;
                  //total_ope_gravadas+=precio_total/1.18
                } else if (data.tipo_igv == '20') {
                  total_ope_exoneradas += data.conceptos[0].monto;
                  //total_ope_gravadas+=precio_total/1.18
                } else if (data.tipo_igv == '30' || data.tipo_igv == '40') {
                  total_ope_inafectas += data.conceptos[0].monto;
                  //total_ope_gravadas+=precio_total/1.18
                } else {
                  total_ope_gratuitas += data.conceptos[0].monto;
                  //total_ope_gravadas+=precio_total/1.18
                }
              } else {
                //console.log("LEGADO");
                if (data.tipo_igv == '10' || data.tipo_igv == '17') { //verificar la validez del 17
                  total_ope_gravadas += valor_unitario * cantidad;
                } else if (data.tipo_igv == '20') {
                  total_ope_exoneradas += valor_unitario * cantidad;
                } else if (data.tipo_igv == '30' || data.tipo_igv == '40') {
                  total_ope_inafectas += valor_unitario * cantidad;
                } else {
                  total_ope_gratuitas += valor_unitario * cantidad;
                }
              }

              //if(data.tipo_igv=='10' || data.tipo_igv=='17'){//verificar la validez del 17
              //	total_ope_gravadas+=valor_unitario*cantidad;
              //}else if(data.tipo_igv=='20'){
              //	total_ope_exoneradas+=valor_unitario*cantidad;
              //}else if(data.tipo_igv=='30' || data.tipo_igv=='40'){
              //	total_ope_inafectas+=valor_unitario*cantidad;
              //}else{
              //	total_ope_gratuitas+=valor_unitario*cantidad;
              //}
            }
          }
          total_igv += igv;
        }
      }
      p.$w.find('[name=total_gravadas]').val(K.round(total_ope_gravadas, 2));
      p.$w.find('[name=total_inafectas]').val(K.round(total_ope_inafectas, 2));
      p.$w.find('[name=total_exoneradas]').val(K.round(total_ope_exoneradas, 2));
      p.$w.find('[name=total_gratuitas]').val(K.round(total_ope_gratuitas, 2));
      var total = total_ope_gravadas + total_ope_exoneradas + total_ope_inafectas + total_igv;
      p.$w.find('[name=total_igv]').val(K.round(total_igv, 2));
      p.$w.find('[name=total]').val(K.round(total, 2));

      if (parseFloat(tipo_cambio)) {
        tipo_cambio = parseFloat(tipo_cambio);
      } else {
        tipo_cambio = 0;
      }

      if (moneda == 'USD') {
        total = total * tipo_cambio;
      }
      var total_efectivo = 0;
      var total_soles = 0;
      var total_dolares = 0;
      var total_detraccion = 0;
      var porcentaje_detraccion = 0;
      if (p.$w.find('[name=tipo]').val() == 'F') {
        if (total > 700) {
          p.$w.find('[name=porcentaje_detraccion]').val("10");
          //total_detraccion=K.round(porcentaje_detraccion*total/100,2);
          //total_detraccion=parseFloat(total_detraccion);
        }
        if (parseFloat(p.$w.find('[name=porcentaje_detraccion]').val())) {
          porcentaje_detraccion = parseFloat(p.$w.find('[name=porcentaje_detraccion] :selected').val());
          total_detraccion = K.round(porcentaje_detraccion * total / 100, 0);
          total_detraccion = parseFloat(total_detraccion);
        }
        /*if(parseFloat(p.$w.find('[name=porcentaje_detraccion]').val())){
        	porcentaje_detraccion = parseFloat(p.$w.find('[name=porcentaje_detraccion] :selected').val());
        	total_detraccion=K.round(porcentaje_detraccion*total/100,2);
        	total_detraccion=parseFloat(total_detraccion);
        }
        */
      }
      var $row_cuenta_detraccion = p.$w.find('[cod_cuenta="101-089983"]'); //CUENTA DE DETRACCION DEL BANCO DE LA NACION
      var $row_monto_soles = p.$w.find('[name=mon_sol]');
      var $row_monto_dolares = p.$w.find('[name=mon_dol]');
      if (total_detraccion > 0) {
        $row_cuenta_detraccion.find('[name=voucher]').val("XXX");
        $row_cuenta_detraccion.find('[name=tot]').val(total_detraccion);
        p.$w.find('[name=total_detraccion]').val(total_detraccion);

      }

      if (p.$w.find('[name=moneda] :selected').val() == 'PEN') {
        total_soles = total - total_detraccion;
      } else {
        total_dolares = total - total_detraccion;
      }
      total_efectivo = total - total_detraccion;
      p.$w.find('[name=monto_base]').val(parseFloat(K.round(total, 2)));
      $row_monto_soles.find('[name=tot]').val(K.round(total_efectivo, 2));
      //$row_monto_dolares.find('[name=tot]').val(K.round(total_dolares,2));
    };

    p.calcTotSinDetracion = function() {
      var total_ope_gravadas = 0;
      var total_ope_inafectas = 0;
      var total_ope_exoneradas = 0;
      var total_ope_gratuitas = 0;
      var total_igv = 0;
      var total_isc = 0;
      var total_descuentos = 0;
      var tipo_cambio = p.$w.find('[name=tipo_cambio]').val();
      var moneda = p.$w.find('[name=moneda] :selected').val();
      if (p.$w.find('[name=grid] tbody tr').length > 0) {
        for (var i = 0; i < p.$w.find('[name=grid] tbody tr').length; i++) {
          var $row = p.$w.find('[name=grid] tbody tr').eq(i);
          var data = $row.data('data');
          var igv = $row.find('[name=igv]').val();
          if (!parseFloat(igv)) {
            igv = 0;
          } else {
            igv = parseFloat(igv);
          }
          var valor_unitario = $row.find('[name=valor_unitario]').val();
          if (!parseFloat(valor_unitario)) {
            valor_unitario = 0;
          } else {
            valor_unitario = parseFloat(valor_unitario);
          }
          var cantidad = $row.find('[name=cantidad]').val();
          if (!parseFloat(cantidad)) {
            cantidad = 0;
          } else {
            cantidad = parseFloat(cantidad);
          }

          var importe_total = $row.find('[name=importe_total]').val();
          if (!parseFloat(importe_total)) {
            importe_total = 0;
          } else {
            importe_total = parseFloat(importe_total);
          }
          if (data != null) {
            if (data.tipo_igv != null) {

              if (data.tipo == "agua_chapi" || data.tipo == "farmacia") {
                console.log("SE DEBE USAR EL METODO DIFERENCIA");
                if (data.tipo_igv == '10' || data.tipo_igv == '17') { //verificar la validez del 17
                  total_ope_gravadas += data.conceptos[0].monto;
                  //total_ope_gravadas+=precio_total/1.18
                } else if (data.tipo_igv == '20') {
                  total_ope_exoneradas += data.conceptos[0].monto;
                  //total_ope_gravadas+=precio_total/1.18
                } else if (data.tipo_igv == '30' || data.tipo_igv == '40') {
                  total_ope_inafectas += data.conceptos[0].monto;
                  //total_ope_gravadas+=precio_total/1.18
                } else {
                  total_ope_gratuitas += data.conceptos[0].monto;
                  //total_ope_gravadas+=precio_total/1.18
                }
              } else {
                console.log("LEGADO");
                if (data.tipo_igv == '10' || data.tipo_igv == '17') { //verificar la validez del 17
                  total_ope_gravadas += valor_unitario * cantidad;
                } else if (data.tipo_igv == '20') {
                  total_ope_exoneradas += valor_unitario * cantidad;
                } else if (data.tipo_igv == '30' || data.tipo_igv == '40') {
                  total_ope_inafectas += valor_unitario * cantidad;
                } else {
                  total_ope_gratuitas += valor_unitario * cantidad;
                }
              }

            }
          }
          total_igv += igv;
        }
      }
      p.$w.find('[name=total_gravadas]').val(K.round(total_ope_gravadas, 2));
      p.$w.find('[name=total_inafectas]').val(K.round(total_ope_inafectas, 2));
      p.$w.find('[name=total_exoneradas]').val(K.round(total_ope_exoneradas, 2));
      p.$w.find('[name=total_gratuitas]').val(K.round(total_ope_gratuitas, 2));
      var total = total_ope_gravadas + total_ope_exoneradas + total_ope_inafectas + total_igv;
      p.$w.find('[name=total_igv]').val(K.round(total_igv, 2));
      p.$w.find('[name=total]').val(K.round(total, 2));

      if (parseFloat(tipo_cambio)) {
        tipo_cambio = parseFloat(tipo_cambio);
      } else {
        tipo_cambio = 0;
      }

      if (moneda == 'USD') {
        total = total * tipo_cambio;
      }
      var total_efectivo = 0;
      var total_soles = 0;
      var total_dolares = 0;
      var total_detraccion = 0;
      var porcentaje_detraccion = 0;
      porcentaje_detraccion = parseFloat(p.$w.find('[name=porcentaje_detraccion] :selected').val());
      if (p.$w.find('[name=tipo]').val() == 'F'  && porcentaje_detraccion>0){
          console.log("calculate porcentaje");
          total_detraccion = K.round(porcentaje_detraccion * total / 100, 0);
          total_detraccion = parseFloat(total_detraccion);
      }
      var $row_cuenta_detraccion = p.$w.find('[cod_cuenta="101-089983"]'); //CUENTA DE DETRACCION DEL BANCO DE LA NACION
      var $row_monto_soles = p.$w.find('[name=mon_sol]');
      var $row_monto_dolares = p.$w.find('[name=mon_dol]');
      if (total_detraccion > 0) {
        $row_cuenta_detraccion.find('[name=voucher]').val("XXX");
        $row_cuenta_detraccion.find('[name=tot]').val(total_detraccion);
        p.$w.find('[name=total_detraccion]').val(total_detraccion);
        console.log("True");
      }
      else {
        $row_cuenta_detraccion.find('[name=voucher]').val("");
        $row_cuenta_detraccion.find('[name=tot]').val(total_detraccion);
        p.$w.find('[name=total_detraccion]').val(total_detraccion);
        console.log("False");
      }

      if (p.$w.find('[name=moneda] :selected').val() == 'PEN') {
        total_soles = total - total_detraccion;
      } else {
        total_dolares = total - total_detraccion;
      }
      total_efectivo = total - total_detraccion;
      p.$w.find('[name=monto_base]').val(parseFloat(K.round(total, 2)));
      $row_monto_soles.find('[name=tot]').val(K.round(total_efectivo, 2));

    };

    p.calcRow = function($row) {};
    p.onChangeCaja = function() {
      var $select = p.$w.find('[name=tipo]');
      $select.change(function() {
        p.$w.find('[name=serie]').empty();
        var docu = $select.find('option:selected').attr('docu');
        var caja_actual = p.$w.find('[name=header_caja]').data('data');
        if (docu == 'FE' || docu == 'BE' || docu == 'NCE' || docu == 'NDE') {
          p.$w.find('[name=numero]').val('').attr('disabled', 'disabled');
          $.post('cj/talo/get_talos', {
            tipo: $select.find('option:selected').val(),
            caja: caja_actual._id
          }, function(talo) {
            if (talo.status == 'success') {
              for (var i = 0; i < talo.rpta.length; i++) {
                var serie = '';
                if (talo.rpta[i].prefijo != null) {
                  serie = talo.rpta[i].prefijo;
                } else if (talo.rpta[i].serie != null) {
                  serie = talo.rpta[i].serie;
                }
                p.$w.find('[name=serie]').append('<option value="' + serie + '">' + serie + '</option>');
                p.$w.find('[name=serie]').find('option:last').data('data', talo.rpta[i]);
              }
            }
          }, 'json');
        } else {
          p.$w.find('[name=numero]').removeAttr('disabled');
          $.post('cj/talo/all', {
            tipo: $select.find('option:selected').val(),
            'caja': p.$w.find('[name=header_caja]').data('data')._id
          }, function(talo) {
            if (talo != null) {
              for (var i = 0; i < talo.length; i++) {
                var serie = '';
                if (talo[i].prefijo != null) {
                  serie = talo[i].prefijo;
                } else if (talo[i].serie != null) {
                  serie = talo[i].serie;
                }
                p.$w.find('[name=serie]').append('<option value="' + serie + '">' + serie + '</option>');
                p.$w.find('[name=serie]').find('option:last').data('data', talo[i]);
              }
            }
          }, 'json');
        }
        p.calcTot();
      }).change();
      $.post('cj/talo/get_caja', 'caja=' + p.$w.find('[name=header_caja]').data('data')._id, function(data) {
        /*var $select = p.$w.find('[name=tipo]').data('data',data).empty();
        for(var i=0,j=data.length; i<j; i++){
        	var selected = '';
        	if(p.update_talonario!=null){
        		if(p.update_talonario.tipo==data[i].tipo) selected = ' selected="selected"';
        	}

        	if($select.find('[value='+data[i].tipo+']').length<=0)
        		$select.append('<option value="'+data[i].tipo+'"'+selected+'>'+cjTalo.types[data[i].tipo]+'</option>');
        	if($select.find('option').length==3) i = j;
        }
        $select.unbind('change').change(function(){
        	var $sel = p.$w.find('[name=serie]').empty(),
        	talos = p.$w.find('[name=tipo]').data('data'),
        	$this = $(this);
        	for(var i=0,j=talos.length; i<j; i++){
        		if($this.find('option:selected').val()==talos[i].tipo){
        			var serie = '';
        			if(talos[i].serie!=null) serie = talos[i].serie;
        			if(talos[i].prefijo!=null) serie = talos[i].prefijo;

        			var selected = '';
        			if(p.update_talonario!=null){
        				if(p.update_talonario.serie==serie) selected = ' selected="selected"';
        			}

        			$sel.append('<option value="'+serie+'"'+selected+'>'+serie+'</option>')
        			.find('option:last').data('data',talos[i]);
        		}
        	}
        	$sel.unbind('change').change(function(){
        		if(p.update_talonario!=null){
        			p.$w.find('[name=numero]').val(p.update_talonario.numero);
        			p.update_talonario = null;
        		}else{
        			p.$w.find('[name=numero]').val(parseInt($(this).find('option:selected').data('data').actual)+1);
        		}
        	}).change();
        }).change();*/


        var modulo = p.$w.find('[name=header_caja]').data('data').modulo;
        p.$w.find('[name=logistica]').hide();
        p.$w.find('[opt-alma=LG]').hide();
        p.$w.find('[opt-alma=AG]').hide();
        p.$w.find('[opt-alma=FA]').hide();
        p.$w.find('[name=fecemi]').attr('disabled', 'disabled');
        p.$w.find('[name=fecven]').attr('disabled', 'disabled');
        switch (modulo) {
          case "IN":
            break;
          case "AG":
            p.$w.find('[name=logistica]').show();
            p.$w.find('[opt-alma=AG]').show();
            break;
          case "PA":
              p.$w.find('[name=fecemi]').removeAttr('disabled');
              p.$w.find('[name=fecven]').removeAttr('disabled');
              break;
          case "FA":
            p.$w.find('[name=logistica]').show();
            p.$w.find('[opt-alma=FA]').show();
            break;
          case "MH":
            break;
          case "CM":
            break;
        }
        $.post('re/posc/get_by_caja', 'caja=' + p.$w.find('[name=header_caja]').data('data')._id, function(pos) {
          if (pos != null) {
            p.$w.find('[name=POSselector]').empty();
            p.$w.find('[name=POSselector]').append('<option value="NO">Pagar sin POS</option>');
            for (var i = 0; i < pos.length; i++) {
              p.$w.find('[name=POSselector]').append('<option value="' + pos[i]._id.$id + '">' + pos[i].nombre + '</option>');
              p.$w.find('[name=POSselector]').find('option:last').data('data', pos[i]);
            }
            p.onChangePOS();
          }
        }, 'json');
      }, 'json');

    }
    p.onChangePOS = function() {
      var $select = p.$w.find('[name=POSselector]');
      var $POSForm = p.$w.find('[name="POSForm"]');
      if ($select.val() != 'NO') {
        $POSForm.show();
        $.post('re/posc/get', 'id=' + $select.val(), function(pos) {
          p.$w.find('[name=proveedor]').html(pos.proveedor);
          p.$w.find('[name=comision]').html(pos.comision);
          p.$w.find('[name=ctban_descr]').html(pos.cbancaria.descr);
          p.$w.find('[name=ctban_cod]').html(pos.cbancaria.cod).data('data', pos.cbancaria._id.$id);
          p.$w.find('[name=pcon_descr]').html(pos.ccontable.descr);
          p.$w.find('[name=pcon_cod]').html(pos.ccontable.cod);
          p.$w.find('[name=conc_descr]').html(pos.cdiverso.nomb).data('data', pos.cdiverso._id.$id);
          p.$w.find('[name=serv_descr]').html(pos.sdiverso.nomb).data('data', pos.sdiverso._id.$id);
        }, 'json');
      } else $POSForm.hide();
    }
    new K.Panel({
      contentURL: 'cj/ecom/edit',
      store: false,
      buttons: {
        'Guardar': {
          icon: 'fa-save',
          type: 'success',
          f: function() {
            cjEcom.saveComprobante(p);
          }
        },
        'Cancelar': {
          icon: 'fa-ban',
          type: 'danger',
          f: function() {
            cjEcom.init();
          }
        }
      },
      onContentLoaded: function() {
        p.$w = $('#mainPanel');
        /*AGREGAR CAMPOS DE NOTAS ELECTRONICAS Y DEBITOS, TAMBIEN SI SE SLEECIONA FACTURA Y */

        p.$w.find('[name=tipo]').click(function() {
          ///SI HA SIDO ENVIADO POR NOTA DE CREDITO Y DEBITO
          var tipo_comp = p.$w.find('[name=tipo] :selected').val();
          if (tipo_comp == 'NC' || tipo_comp == 'ND') {
            if (tipo_comp == 'NC') {
              p.$w.find('[name=notas_cd]').hide();
              p.$w.find('[name=notas_NC]').show();
              p.$w.find('[name=refnotas_cd]').show();
              p.$w.find('[name=notas_ND]').hide();
            } else {
              p.$w.find('[name=notas_cd]').hide();
              p.$w.find('[name=notas_ND]').show();
              p.$w.find('[name=refnotas_cd]').show();
              p.$w.find('[name=notas_NC]').hide();
            }
            //if(talo.status=='success'){
            //	for(var i=0;i<talo.rpta.length;i++){
            //		var serie = '';
            //		if(talo.rpta[i].prefijo!=null){
            //			serie = talo.rpta[i].prefijo;
            //		}else if(talo.rpta[i].serie!=null){
            //			serie = talo.rpta[i].serie;
            //		}
            //		p.$w.find('[name=serie]').append('<option value="'+serie+'">'+serie+'</option>');
            //		p.$w.find('[name=serie]').find('option:last').data('data',talo.rpta[i]);
            //	}
            //}

          } else {
            p.$w.find('[name=notas_NC]').hide();
            p.$w.find('[name=notas_ND]').hide();
            p.$w.find('[name=refnotas_cd]').hide();
          }
          p.calcTot();
        });

        p.$w.find('[name=fecemi]').val(moment().format('YYYY-MM-DD')).datepicker();
        p.$w.find('[name=fecven]').val(moment().format('YYYY-MM-DD')).datepicker();
        p.$w.find('[name=header_responsable]').html(K.session.enti.nomb + ' ' + K.session.enti.appat + ' ' + K.session.enti.apmat);
        p.$w.find('[name=btnSelectCaja]').click(function() {
          cjEcom.windowSelectCaja({
            callback: function(data) {
              p.$w.find('[name=header_caja]').html(data.nomb).data('data', {
                _id: data._id.$id,
                nomb: data.nomb,
                modulo: data.modulo
              });
              p.onChangeCaja();
            }
          });
        });
        p.$w.find('[name=porcentaje_detraccion]').click(function() {
          //console.log(p.$w.find('[name=porcentaje_detraccion]').val());
            if (p.$w.find('[name="porcentaje_detraccion"]').val() == "10") {
              //console.log("CALCULAR DETRACCION");
              p.calcTot();
            }
            else {
              //console.log("CALCULAR TOTAL SIN DETRACCION");
              p.calcTotSinDetracion();
            }

        });
        p.$w.find('[name=btnSelectCliente]').click(function() {
          mgEnti.windowSelect({
            bootstrap: true,
            callback: function(data) {
              var tipo = p.$w.find('[name=tipo] :selected').val();
              var tipo_doc = '';
              var num_doc = '';
              if (data.docident != null) {
                for (var i = 0; i < data.docident.length; i++) {
                  tipo_doc = data.docident[i].tipo;
                  num_doc = data.docident[i].num;
                }
              }
              p.$w.find('[name=btnSelectCliente]').data('data', data);
              p.$w.find('[name=cliente_tipo_documento]').val(tipo_doc);
              p.$w.find('[name=cliente_num_documento]').val(num_doc);
              p.$w.find('[name=cliente_nomb]').val(mgEnti.formatName(data));
              var direccion = '';
              if (data.domicilios != null) {
                for (var i = 0; i < data.domicilios.length; i++) {
                  direccion = data.domicilios[i].direccion;
                }
              }
              p.$w.find('[name=cliente_direccion]').val(direccion);
            }
          });
        });
        p.$w.find('[name=btnClearSelectCliente]').click(function() {
          p.$w.find('[name=cliente_num_documento]').val("");
          p.$w.find('[name=cliente_nomb]').val("");
          p.$w.find('[name=cliente_direccion]').val("");
          p.$w.find('[name=cliente_email_1]').val("");
          p.$w.find('[name=cliente_email_2]').val("");
          p.$w.find('[name=btnSelectCliente]').removeData('data');
        });
        p.$w.find('[name=btnAddRow]').click(function() {
          var $row = p.$w.find('#rowReference').clone().show().removeAttr('id');
          $row.attr('uid', guid());
          p.$w.find('[name=grid] tbody').append($row);
        });

        p.$w.find('[name=btnAddRowAdmin]').click(function() {
          var auth = prompt("Inggrese la clave");
          if (auth == 'sbp@2016') {
            var $row = p.$w.find('#rowReference').clone().show().removeAttr('id');
            $row.attr('uid', guid());
            $row.find('[name=btnSettingsRow]').remove();
            p.$w.find('[name=grid] tbody').append($row);
          }
        });
        p.$w.on('click', '[name=btnDeleteRow]', function() {
          var $row = $(this).closest('tr');
          var _data = $row.data('data');
          var uid_row = $row.attr('uid');
          p.$w.find('[parent_uid=' + uid_row + ']').remove();
          $row.remove();
          p.calcTot();
        });
        p.$w.on('click', '[name=btnSettingsRow]', function() {
          var $row = $(this).closest('tr');
          var _data = $row.data('data');
          var uid_row = $row.attr('uid');
          //p.da = funcionpadre(tufunction(asd,b),funcionbsad(c,d));
          cjEcom.windowRowConfiguration({
            data: _data,
            data_header: {
              tipo: p.$w.find('[name=tipo] :selected').val(),
            },
            config: p.get_config(),
            callback: function(data, data_header) {
              if (data != null) {
                if (data != null) {
                  $row.find('[name=codigo]').val(data.codigo);
                  $row.find('[name=descripcion]').val(data.descr);
                  $row.find('[name=cantidad]').val(data.cant);
                  $row.find('[name=unidad]').val(data.cod_unidad);
                  $row.find('[name=valor_unitario]').val(data.valor_unitario);
                  $row.find('[name=igv]').val(data.igv);
                  $row.find('[name=precio_venta_unitario]').val(data.precio_venta_unitario);
                  $row.find('[name=precio_total]').val(data.precio_total);
                  p.$w.find('[parent_uid=' + uid_row + ']').remove();
                  if (data.subitems != null) {
                    if (data.subitems.length) {
                      for (var i = 0; i < data.subitems.length; i++) {
                        var $subrow = p.$w.find('#rowReference').clone().show().removeAttr('id');
                        $subrow.attr('parent_uid', uid_row);
                        $subrow.find('[name=codigo]').val(data.subitems[i].codigo);
                        $subrow.find('[name=descripcion]').val(data.subitems[i].descr);
                        $subrow.find('[name=cantidad]').val(data.subitems[i].cant);
                        $subrow.find('[name=unidad]').val(data.subitems[i].cod_unidad);
                        $subrow.find('[name=valor_unitario]').val(data.subitems[i].valor_unitario);
                        $subrow.find('[name=igv]').val(data.subitems[i].igv);
                        $subrow.find('[name=precio_venta_unitario]').val(data.subitems[i].precio_venta_unitario);
                        $subrow.find('[name=precio_total]').val(data.subitems[i].precio_total);
                        $subrow.find('[name=btnSettingsRow]').remove();
                        $subrow.find('[name=btnDeleteRow]').remove();
                        $subrow.data('data', data.subitems[i]);
                        if (data.tipo_agregacion == 'append') {
                          p.$w.find('[name=grid] tbody').append($subrow);
                        } else if (data.tipo_agregacion == 'after') {
                          $row.after($subrow);
                        }
                      }
                    }
                  }
                  $row.data('data', data);
                  p.calcTot();
                }
              }
              if (data_header != null) {
                if (data_header.cliente != null) {
                  var tipo_doc = '';
                  var num_doc = '';
                  if (data_header.cliente.docident != null) {
                    for (var i = 0; i < data_header.cliente.docident.length; i++) {
                      tipo_doc = data_header.cliente.docident[i].tipo;
                      num_doc = data_header.cliente.docident[i].num;
                    }
                  }
                  p.$w.find('[name=btnSelectCliente]').data('data', data_header.cliente);
                  p.$w.find('[name=cliente_tipo_documento]').val(tipo_doc);
                  p.$w.find('[name=cliente_num_documento]').val(num_doc);
                  p.$w.find('[name=cliente_nomb]').val(mgEnti.formatName(data_header.cliente));
                  var direccion = '';
                  if (data_header.cliente.domicilios != null) {
                    for (var i = 0; i < data_header.cliente.domicilios.length; i++) {
                      direccion = data_header.cliente.domicilios[i].direccion;
                    }
                  }
                  p.$w.find('[name=cliente_direccion]').val(direccion);
                }

                if (data_header.moneda) {
                  if (data_header.moneda == 'PEN' || data_header.moneda == 'USD') {
                    p.$w.find('[name=moneda]').val(data_header.moneda).attr('disabled', 'disabled');
                  }
                  p.calcTot();
                }
              }
            }
          });
        });
        $.post('cj/ecom/get_vars', {}, function(data) {
          p.config.tc = data.tc.valor;
          p.config.ctban = data.ctban;
          p.config.almacenes = data.almacenes;
          for (var i = 0, j = data.vars.length; i < j; i++) {
            if (data.vars[i].cod == 'IGV')
              p.config.igv = (parseFloat(data.vars[i].valor) / 100);
            else if (data.vars[i].cod == 'MORA')
              p.config.mora = parseFloat(data.vars[i].valor);
            else if (data.vars[i].cod == 'DETRACCION')
              p.config.detraccion = parseFloat(data.vars[i].valor);
          }
          if (data.almacenes != null) {
            if (data.almacenes.length > 0) {
              var $cbo_alma = p.$w.find('[name=almacen]');
              for (var i = 0; i < data.almacenes.length; i++) {
                $cbo_alma.append('<option opt-alma="' + data.almacenes[i].aplicacion + '" value="' + data.almacenes[i]._id.$id + '">' + data.almacenes[i].nomb + '</option>');
              }
            }
          }
          p.$w.find('[name=tipo_cambio]').val(p.config.tc);
          p.$w.find('[name=porcentaje_igv]').val(p.config.igv * 100);
          //p.$w.find('[name=porcentaje_detraccion]').val(p.config.detraccion*100);


          /*Efectivo Soles*/
          var $row = $('<tr class="item" name="mon_sol">');
          $row.append('<td>Efectivo Soles</td>');
          $row.append('<td>');
          $row.append('<td>S/.<input type="text" name="tot" size="7"/></td>');
          $row.append('<td>S/.0.00</td>');
          $row.find('[name=tot]').val(0).keyup(function() {
            if ($(this).val() == '')
              $(this).val(0);
            $(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon($(this).val()));
            $(this).closest('.item').data('total', parseFloat($(this).val()));
          });
          p.$w.find('[name=gridForm] tbody').append($row);
          /*Efectivo Dolares*/
          var $row = $('<tr class="item" name="mon_dol">');
          $row.append('<td>Efectivo D&oacute;lares</td>');
          $row.append('<td>');
          $row.append('<td>$<input type="text" name="tot" size="7"/></td>');
          $row.append('<td>$0.00</td>');
          $row.find('[name=tot]').val(0).keyup(function() {
            if ($(this).val() == '')
              $(this).val(0);
            $(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon($(this).val(), '$'));
            $(this).closest('.item').data('total', parseFloat($(this).val()));
          });
          p.$w.find('[name=gridForm] tbody').append($row);
          /*Cuentas bancarios*/
          if (p.config.ctban != null) {
            for (var i = 0, j = p.config.ctban.length; i < j; i++) {
              var $row = $('<tr class="item" name="ctban" cod_cuenta="' + p.config.ctban[i].cod + '" id_banco="' + p.config.ctban[i]._id.$id + '">');
              $row.append('<td>Voucher <input type="text" name="voucher" size="7"/></td>');
              $row.append('<td>' + p.config.ctban[i].nomb + ' (' + p.config.ctban[i].cod + ')</td>');
              $row.append('<td>' + (p.config.ctban[i].moneda == 'S' ? 'S/.' : '$') + '<input type="text" name="tot" size="7"/></td>');
              $row.append('<td>S/.0.00</td>');
              $row.find('[name=tot]').val(0).keyup(function() {
                if ($(this).val() == '') {
                  $(this).val(0);
                }
                var tipo_cambio = p.$w.find('[name=tipo_cambio]').val();
                if (!parseFloat(tipo_cambio)) tipo_cambio = 0;
                else tipo_cambio = parseFloat(tipo_cambio);
                var moneda = $(this).closest('.item').data('moneda');
                var tot = moneda == 'S' ? $(this).val() : $(this).val() * tipo_cambio;
                $(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon(tot));
                $(this).closest('.item').data('total', parseFloat(tot));
              });
              $row.data('moneda', p.config.ctban[i].moneda).data('data', {
                _id: p.config.ctban[i]._id.$id,
                cod: p.config.ctban[i].cod,
                nomb: p.config.ctban[i].nomb,
                moneda: p.config.ctban[i].moneda,
                cod_banco: p.config.ctban[i].cod_banco
              });
              p.$w.find('[name=gridForm] tbody').append($row);
            }
          }
        }, 'json');

        p.$w.find('[name=POSselector]').click(function() {
          p.onChangePOS();
        });

        p.$w.find('[name=btnPagarPOS]').click(function() {
          var total = p.$w.find('[name=monto_base]').val();
          if (isNaN(parseFloat(total))) {
            return K.notification({
              title: ciHelper.titleMessages.infoReq,
              text: 'El total no es un numero',
              type: 'error'
            });
          }
          p.$w.find('[name=monto_comision]').html(K.round(parseFloat(total) * parseFloat(p.$w.find('[name=comision]').html()) / 100, 2));
          p.$w.find('[name=monto_total]').html(K.round(parseFloat(p.$w.find('[name=monto_comision]').html()) + parseFloat(total), 2));

          var $Servicio_id = p.$w.find('[name=serv_descr]').data('data');
          var $Servicio_nomb = p.$w.find('[name=serv_descr]').html();
          var $Concepto_id = p.$w.find('[name=conc_descr]').data('data');

          p.$w.find('[name=btnAddRow]').click();
          p.$w.find('[name=grid] tbody').last().find('[name=btnSettingsRow]').click();
          var $VentanaPago = $('#windowRowConfiguration');
          $VentanaPago.find('[name=btnServicio]').click();
          setTimeout(function() {
            var $VentanaServicios = $('#windowSelect');
            $VentanaServicios.find(":text").val($Servicio_nomb);
            $VentanaServicios.find('[name=btnSearch]').click();
            setTimeout(function() {
              var primerElemento = $VentanaServicios.find('[name=grid] tbody tr:eq(0)');
              if (primerElemento.data() == undefined) {
                return K.notification({
                  title: ciHelper.titleMessages.infoReq,
                  text: 'Error, no se encontro el concepto del ingreso diverso. probablemente se deshabilito',
                  type: 'error'
                });
              }
              if (primerElemento.data().data._id.$id != $Servicio_id) {
                return K.notification({
                  title: ciHelper.titleMessages.infoReq,
                  text: 'Error al seleccionar el concepto de Ingresos Diversos. debe seleccionarse manualmente',
                  type: 'error'
                });
              }
              primerElemento.click();
              $VentanaServicios.find('button.btn.dim.btn-info').click();
              setTimeout(function() {
                $VentanaPago.find('[name="' + $Concepto_id + '"] td:eq(2) input').val(p.$w.find('[name=monto_comision]').html()).change(),
                  $VentanaPago.find('button:contains("Guardar cambios").btn.dim.btn-default').click();
                p.calcTot();
                var total = p.$w.find('[name=monto_base]').val();
                var total_detraccion = 0;
                var porcentaje_detraccion = 0;
                if (p.$w.find('[name=tipo]').val() == 'F') {
                  if (total > 700) {
                    p.$w.find('[name=porcentaje_detraccion]').val("10");
                  }
                  if (parseFloat(p.$w.find('[name=porcentaje_detraccion]').val())) {
                    porcentaje_detraccion = parseFloat(p.$w.find('[name=porcentaje_detraccion] :selected').val());
                    total_detraccion = K.round(porcentaje_detraccion * total / 100, 2);
                    total_detraccion = parseFloat(total_detraccion);
                  }
                }
                var $row_cuenta_detraccion = p.$w.find('[cod_cuenta="101-089983"]'); //CUENTA DE DETRACCION DEL BANCO DE LA NACION
                if (total_detraccion > 0) {
                  $row_cuenta_detraccion.find('[name=voucher]').val("XXX");
                  $row_cuenta_detraccion.find('[name=tot]').val(total_detraccion);
                  p.$w.find('[name=total_detraccion]').val(total_detraccion);
                }
                total = total - total_detraccion;
                p.$w.find('[id_banco=' + p.$w.find('[name=ctban_cod]').data('data') + '] [name=tot]').val(parseFloat(total)).keyup();
                p.$w.find('[name=mon_sol] [name=tot]').val(0).keyup();
              }, 1000);
            }, 1000);
          }, 1000);
        });



        /*p.$w.find('[name=btnSave]').click(function(){

        });*/
        if (p.id != null) {
          /*p.$w.find('[name=btnUpdate]').click(function(){
          	cjEcom.saveComprobante(p);
          });*/
          $.post('cj/ecom/get', {
            _id: p.id
          }, function(comp) {
            if (comp != null) {
              p.update_talonario = {
                tipo: comp.tipo,
                serie: comp.serie,
                numero: comp.numero
              };
              p.$w.find('[name=tipo]').val(comp.tipo);
              p.$w.find('[name=cliente_tipo_documento] :selected').val(comp.tipo_doc);
              p.$w.find('[name=cliente_nomb]').val(comp.cliente_nomb);
              p.$w.find('[name=cliente_num_documento]').val(comp.cliente_doc);
              p.$w.find('[name=cliente_direccion]').val(comp.cliente_domic);
              p.$w.find('[name=cliente_email_1]').val(comp.cliente_email_1);
              p.$w.find('[name=fecemi]').val(moment(comp.fecemi.sec, 'X').format('YYYY-MM-DD'));
              p.$w.find('[name=fecven]').val(moment(comp.fecven.sec, 'X').format('YYYY-MM-DD'));
              p.$w.find('[name=moenda] :selected').val(comp.moneda);
              p.$w.find('[name=tipo_cambio]').val(comp.tipo_cambio);
              p.$w.find('[name=porcentaje_igv]').val(comp.porcentaje_igv);
              p.$w.find('[name=total_detraccion]').val(comp.total_detraccion);
              p.$w.find('[name=observ]').val(comp.observ);
              p.$w.find('[name=header_caja]').html(comp.caja.nomb).data('data', {
                _id: comp.caja._id.$id,
                nomb: comp.caja.nomb,
                modulo: comp.caja.modulo,
              });

              //EN CASO DE SER NC y ND
              /*if(comp.tipo == 'NC' || comp.tipo == 'ND'){
              	//p.$w.find('[name=notas_cd]').show();
              	p.$w.find('[name=refnotas_cd]').show();
              	var $select_motivo_nota = p.$w.find('[name=motivo_nota]').empty();
              }else{
              	p.$w.find('[name=notas_cd]').hide();
              	p.$w.find('[name=refnotas_cd]').hide();
              }
              */
              if (comp.items != null) {
                for (var i = 0; i < comp.items.length; i++) {
                  var $row = p.$w.find('#rowReference').clone().show().removeAttr('id');
                  var uid_row = guid();
                  $row.attr('uid', uid_row);
                  $row.find('[name=codigo]').val(comp.items[i].codigo);
                  $row.find('[name=descripcion]').val(comp.items[i].descr);
                  $row.find('[name=cantidad]').val(comp.items[i].cant);
                  $row.find('[name=unidad]').val(comp.items[i].cod_unidad);
                  $row.find('[name=valor_unitario]').val(comp.items[i].valor_unitario);
                  $row.find('[name=igv]').val(comp.items[i].igv);
                  $row.find('[name=precio_venta_unitario]').val(comp.items[i].precio_venta_unitario);
                  $row.find('[name=precio_total]').val(comp.items[i].precio_total);

                  if (comp.items[i].tipo != null) {
                    switch (comp.items[i].tipo) {
                      case 'pago_meses':
                        if (comp.items[i].conceptos) {
                          for (var j = 0; j < comp.items[i].conceptos.length; j++) {
                            if (comp.items[i].conceptos[j].cuenta != null) {
                              comp.items[i].conceptos[j].cuenta._id = comp.items[i].conceptos[j].cuenta._id.$id;
                            }

                            if (comp.items[i].conceptos[j].alquiler != null) {
                              if (comp.items[i].conceptos[j].alquiler.contrato != null) {
                                comp.items[i].conceptos[j].alquiler.contrato = comp.items[i].conceptos[j].alquiler.contrato.$id;
                              }
                            }
                          }
                        }
                        break;
                      case 'pago_parcial':
                        if (comp.items[i].conceptos) {
                          for (var j = 0; j < comp.items[i].conceptos.length; j++) {
                            if (comp.items[i].conceptos[j].cuenta != null) {
                              comp.items[i].conceptos[j].cuenta._id = comp.items[i].conceptos[j].cuenta._id.$id;
                            }

                            if (comp.items[i].conceptos[j].alquiler != null) {
                              if (comp.items[i].conceptos[j].alquiler.contrato != null) {
                                comp.items[i].conceptos[j].alquiler.contrato = comp.items[i].conceptos[j].alquiler.contrato.$id;
                              }
                            }
                          }
                        }
                        break;
                      case 'pago_acta':
                        if (comp.items[i].conceptos) {
                          for (var j = 0; j < comp.items[i].conceptos.length; j++) {
                            if (comp.items[i].conceptos[j].cuenta != null) {
                              comp.items[i].conceptos[j].cuenta._id = comp.items[i].conceptos[j].cuenta._id.$id;
                            }

                            if (comp.items[i].conceptos[j].alquiler != null) {
                              if (comp.items[i].conceptos[j].alquiler.contrato != null) {
                                comp.items[i].conceptos[j].alquiler.contrato = comp.items[i].conceptos[j].alquiler.contrato.$id;
                              }
                            }

                            if (comp.items[i].conceptos[j].acta_conciliacion != null) {
                              comp.items[i].conceptos[j].acta_conciliacion = comp.items[i].conceptos[j].acta_conciliacion.$id;
                            }
                          }
                        }
                        break;
                      case 'cuenta_cobrar':
                        if (comp.items[i].conceptos) {
                          for (var j = 0; j < comp.items[i].conceptos.length; j++) {
                            if (comp.items[i].conceptos[j].cuenta != null) {
                              comp.items[i].conceptos[j].cuenta._id = comp.items[i].conceptos[j].cuenta._id.$id;
                            }

                            if (comp.items[i].conceptos[j].alquiler != null) {
                              if (comp.items[i].conceptos[j].alquiler.contrato != null) {
                                comp.items[i].conceptos[j].alquiler.contrato = comp.items[i].conceptos[j].alquiler.contrato.$id;
                              }
                            }

                            if (comp.items[i].cuenta_cobrar != null) {
                              comp.items[i].cuenta_cobrar._id = comp.items[i].cuenta_cobrar._id.$id;
                              if (comp.items[i].cuenta_cobrar.servicio != null) {
                                comp.items[i].cuenta_cobrar.servicio._id = comp.items[i].cuenta_cobrar.servicio._id.$id;
                              }
                            }

                            if (comp.items[i].conceptos[j].cuenta_cobrar != null) {
                              comp.items[i].conceptos[j].cuenta_cobrar._id = comp.items[i].conceptos[j].cuenta_cobrar._id.$id;
                              if (comp.items[i].conceptos[j].cuenta_cobrar.servicio != null) {
                                comp.items[i].conceptos[j].cuenta_cobrar.servicio._id = comp.items[i].conceptos[j].cuenta_cobrar.servicio._id.$id;
                              }
                            }
                          }
                        }
                        break;
                      case 'agua_chapi':
                        if (comp.items[i].conceptos) {
                          for (var j = 0; j < comp.items[i].conceptos.length; j++) {
                            if (comp.items[i].conceptos[j].cuenta != null) {
                              comp.items[i].conceptos[j].cuenta._id = comp.items[i].conceptos[j].cuenta._id.$id;
                            }

                            if (comp.items[i].conceptos[j].producto != null) {
                              comp.items[i].conceptos[j].producto._id = comp.items[i].conceptos[j].producto._id.$id;
                            }
                          }
                        }
                        break;
                      case 'farmacia':
                        if (comp.items[i].conceptos) {
                          for (var j = 0; j < comp.items[i].conceptos.length; j++) {
                            if (comp.items[i].conceptos[j].cuenta != null) {
                              comp.items[i].conceptos[j].cuenta._id = comp.items[i].conceptos[j].cuenta._id.$id;
                            }

                            if (comp.items[i].conceptos[j].producto != null) {
                              comp.items[i].conceptos[j].producto._id = comp.items[i].conceptos[j].producto._id.$id;
                            }
                          }
                        }
                        break;
                      case 'servicio':
                        if (comp.items[i].conceptos) {
                          for (var j = 0; j < comp.items[i].conceptos.length; j++) {
                            if (comp.items[i].conceptos[j].cuenta != null) {
                              comp.items[i].conceptos[j].cuenta._id = comp.items[i].conceptos[j].cuenta._id.$id;
                            }

                            if (comp.items[i].conceptos[j]._id != null) {
                              comp.items[i].conceptos[j]._id = comp.items[i].conceptos[j]._id.$id;
                            }

                            if (comp.items[i].conceptos[j].servicio != null) {
                              if (comp.items[i].conceptos[j].servicio._id != null) {
                                comp.items[i].conceptos[j].servicio._id = comp.items[i].conceptos[j].servicio._id.$id;
                              }
                            }
                          }
                        }
                        break;
                    }
                  }

                  $row.data('data', comp.items[i]);
                  p.$w.find('[name=grid] tbody').append($row);
                  //p.$w.find('[parent_uid='+uid_row+']').remove();
                  if (comp.items[i].subitems != null) {
                    if (comp.items[i].subitems.length) {
                      for (var j = 0; j < comp.items[i].subitems.length; j++) {
                        var $subrow = p.$w.find('#rowReference').clone().show().removeAttr('id');
                        var uid_subrow = guid();
                        $subrow.attr('parent_uid', uid_row);
                        $subrow.attr('uid', uid_subrow);
                        $subrow.find('[name=codigo]').val(comp.items[i].subitems[j].codigo);
                        $subrow.find('[name=descripcion]').val(comp.items[i].subitems[j].descr);
                        $subrow.find('[name=cantidad]').val(comp.items[i].subitems[j].cant);
                        $subrow.find('[name=unidad]').val(comp.items[i].subitems[j].cod_unidad);
                        $subrow.find('[name=valor_unitario]').val(comp.items[i].subitems[j].valor_unitario);
                        $subrow.find('[name=igv]').val(comp.items[i].subitems[j].igv);
                        $subrow.find('[name=precio_venta_unitario]').val(comp.items[i].subitems[j].precio_venta_unitario);
                        $subrow.find('[name=precio_total]').val(comp.items[i].subitems[j].precio_total);
                        $subrow.find('[name=btnSettingsRow]').remove();
                        $subrow.find('[name=btnDeleteRow]').remove();
                        $subrow.data('data', comp.items[i].subitems[j]);
                        $row.after($subrow);
                      }
                    }
                  }
                  //p.calcTot();
                }
              }
              p.$w.find('[name=total_isc]').val(comp.total_isc);
              p.$w.find('[name=total_igv]').val(comp.total_igv);
              p.$w.find('[name=total_inafectas]').val(comp.total_ope_inafectas);
              p.$w.find('[name=total_gravadas]').val(comp.total_ope_gravadas);
              p.$w.find('[name=total_exoneradas]').val(comp.total_ope_exoneradas);
              p.$w.find('[name=total_gratuitas]').val(comp.total_ope_gratuitas);
              p.$w.find('[name=total]').val(comp.total);

              p.$w.find('[name=mon_sol] [name=tot]').val(comp.efectivos[0].monto).keyup();
              p.$w.find('[name=mon_dol] [name=tot]').val(comp.efectivos[1].monto).keyup();
              if (comp.vouchers != null) {
                for (var i = 0; i < comp.vouchers.length; i++) {
                  if (p.$w.find('[id_banco=' + comp.vouchers[i].cuenta_banco._id.$id + ']').length > 0) {
                    p.$w.find('[id_banco=' + comp.vouchers[i].cuenta_banco._id.$id + ']').find('[name=voucher]').val(comp.vouchers[i].num);
                    p.$w.find('[id_banco=' + comp.vouchers[i].cuenta_banco._id.$id + ']').find('[name=tot]').val(comp.vouchers[i].monto).keyup();
                    p.$w.find('[id_banco=' + comp.vouchers[i].cuenta_banco._id.$id + ']').find('td:eq()').html(ciHelper.formatMon(comp.vouchers[i].monto));
                  }
                }
              }
              p.onChangeCaja();
            }
          }, 'json')
        } else {
          p.$w.find('[name=header_caja]').html(p.caja.nomb).data('data', {
            _id: p.caja._id,
            nomb: p.caja.nomb,
            modulo: p.caja.modulo
          });

          //ESTO DEBE SER SOLO SI SE INCLUYE p.nota_serie, p.nota_numero y p.nota_tipo o se lanza un evento
          if (p.nota_serie != null && p.nota_numero != null && p.nota_tipo != null) {
            p.$w.find('[name=tipo]').attr('disabled', 'disabled').val(p.nota);
            if (p.nota_serie != null) console.log(p.nota_serie);
            if (p.nota_numero != null) console.log(p.nota_numero);
            if (p.nota != null) console.log(p.nota);
            if (p.nota_tipo != null) {
              console.log("Tipo de la serie a modificar: " + p.nota_tipo);
              if (p.nota_tipo == "F") {
                console.log("numero: 01");
                //p.$w.find('[name=tipo_paranota]').attr('disabled','disabled').val("01");
                p.$w.find('[name=tipo_paranota]').attr('disabled', 'disabled').val("F");
              } else if (p.nota_tipo == "B") {
                console.log("numero: 00");
                //p.$w.find('[name=tipo_paranota]').attr('disabled','disabled').val("00");
                p.$w.find('[name=tipo_paranota]').attr('disabled', 'disabled').val("B");
              } else {
                console.log("No es boleta ni factura");
                p.$w.find('[name=tipo_paranota]').attr('disabled', 'disabled').val("Solo boleta o factura");
              }
            }
            console.log("numero de verdad: " + p.$w.find('[name=tipo_paranota]').val());
            p.$w.find('[name=serie_paranota]').attr('disabled', 'disabled').val(p.nota_serie);
            p.$w.find('[name=numero_paranota]').attr('disabled', 'disabled').val(p.nota_numero);
            p.$w.find('[name=notas_cd]').show();
            p.$w.find('[name=refnotas_cd]').show();
            var $select_motivo_nota = p.$w.find('[name=motivo_nota]').empty();
            if (p.$w.find('[name=tipo]').val() == 'NC') {
              $select_motivo_nota.append('<option value="01">Anulacion de la operacion</option>');
              $select_motivo_nota.append('<option value="02">Anulacion por error en el RUC</option>');
              $select_motivo_nota.append('<option value="03">Correccion por error en la descripcion</option>');
              $select_motivo_nota.append('<option value="04">Descuento global</option>');
              $select_motivo_nota.append('<option value="05">Descuento por item</option>');
              $select_motivo_nota.append('<option value="06">Devolucion total</option>');
              $select_motivo_nota.append('<option value="07">Devolucion por item</option>');
              $select_motivo_nota.append('<option value="08">Bonificacion</option>');
              $select_motivo_nota.append('<option value="09">Disminucion en el valor</option>');
              $select_motivo_nota.append('<option value="10">Otros Conceptos</option>');
            }
            if (p.$w.find('[name=tipo]').val() == 'ND') {
              $select_motivo_nota.append('<option value="01">Intereses por mora</option>');
              $select_motivo_nota.append('<option value="02">Aumento en el valor</option>');
              $select_motivo_nota.append('<option value="03">Penalidades/ otros conceptos</option>');
            }
          }
          //CUANDO LE HAGAN CLICK
          p.$w.find('[name=tipo]').click(function() {
            var tipo_comp = p.$w.find('[name=tipo] :selected').val();
            if (tipo_comp == 'NC' || tipo_comp == 'ND') {
              p.$w.find('[name=notas_cd]').hide();
              p.$w.find('[name=refnotas_cd]').show();
              var $select_motivo_nota = p.$w.find('[name=motivo_nota]').empty();
            } else {
              p.$w.find('[name=notas_cd]').hide();
              p.$w.find('[name=refnotas_cd]').hide();
            }
          });
          p.onChangeCaja();
        }
      }
    });
  },
  windowRowConfiguration: function(p) {
    if (p == null) p = {};
    p.item_count = 1;
    p.calcTot = function() {
      //CALCULO POR LEGADO
      switch (p.tipo) {
        case 'pago_meses':
          p.calcTotInmuebles();
          break;
        case 'pago_parcial':
          p.calcTotInmuebles();
          break;
        case 'cuenta_cobrar':
          p.calcTotCuentaCobrar();
          break;
        case 'servicio':
          p.calcConc();
          break;
        case 'pago_acta':
          p.calcTotActas();
          break
        case 'agua_chapi':
          p.calcTotChapiDiferencia();
          break;
        case 'farmacia':
          //p.calcTotFarmacia();
          p.calcTotFarmaciaDiferencia();
          break;
      }
    };
    p.calcTotDecimales = function() {
      //CALCULO CON  LA DIFERENCIA SEGUN C.I. N 010-2018-SBPA-OEI
      switch (p.tipo) {
        case 'agua_chapi':
          p.calcTotChapiDiferencia();
          break;
      }
    };
    p.calcTotDiferencia = function() {
      //CALCULO CON  LA DIFERENCIA SEGUN C.I. N 010-2018-SBPA-OEI
      switch (p.tipo) {
        case 'agua_chapi':
          p.calcTotChapiDiferencia();
          break;
      }
    };
    p.calcTotRegresion = function() {
      //CALCULO CON  LA DIFERENCIA SEGUN C.I. N 010-2018-SBPA-OEI
      switch (p.tipo) {
        case 'pago_meses':
          p.calcTotInmuebles();
          break;
        case 'pago_parcial':
          p.calcTotInmuebles();
          break;
        case 'cuenta_cobrar':
          p.calcTotCuentaCobrar();
          break;
        case 'servicio':
          p.calcConc();
          break;
        case 'pago_acta':
          p.calcTotActas();
          break
        case 'agua_chapi':
          p.calcTotChapiRegresion();
          break;
        case 'farmacia':
          p.calcTotFarmaciaRegresion();
          break;
      }
    };
    /*p.calcTotChapiDecimales = function(){
    	console.log("CALCULO CON TODOS LOS DECIMALES DE LA COMUNICACION CI N 010-2018-SBPA-OEI");
    	if(p.$w.find('[name=grid] tbody tr').length>0){
    		p.$w.find('[name=cantidad]').removeAttr('disabled');
    		p.$w.find('[name=unidad]').removeAttr('disabled');
    		var tipo_igv_global = p.$w.find('[name=tipo_igv] :selected').val();
    		var valor_venta = 0;
    		var cantidad = 0;
    		var valor_unitario = 0;
    		var igv = 0;
    		for(var i=0;i<p.$w.find('[name=grid] tbody tr').length;i++){
    			var $row = p.$w.find('[name=grid] tbody tr').eq(i);
    			//AQUI SE CALCULA EL VALOR DEL IGV Y DE EL STOCK
    			if($row.data('data').igv!=null){
    				var parent_id = $row.data('data').parent;
    				var $parent = p.$w.find('[item='+$row.data('data').parent+']');
    				var valor_venta = $parent.find('[name=monto]').val();
    				if(isNaN(parseFloat(valor_venta))){
    					return K.notification({
    						title: ciHelper.titleMessages.infoReq,
    						text: 'El monto indicado en el item'+(parent_id)+' no es un valor numerico valido',
    						type: 'error'
    					});
    				}else{
    					valor_venta = parseFloat(valor_venta);
    				}
    				var monto = 0;
    				if(tipo_igv_global=='10'){
    					monto = valor_venta*p.config.igv;
    				}
    				$row.find('[name=monto]').val(K.round(monto,2));
    				igv+=parseFloat(monto);
    				console.log("-IMPUESTO=> "+"valor_venta: "+valor_venta+" X p.config.igv: "+p.config.igv+" monto: "+monto);
    			}else{
    				console.log($row.data('data'));
    				cantidad = $row.find('[name=cant_item]').val();
    				if(!parseFloat(cantidad)) cantidad=0;
    				else cantidad = parseFloat(cantidad);
    				valor_unitario = $row.find('[name=valor_unitario_item]').val();
    				if(!parseFloat(valor_unitario)){
    					valor_unitario=0;
    				}else{
    					valor_unitario = parseFloat(valor_unitario);
    				}
    				//valor_venta=K.round(valor_unitario*cantidad,2);
    				valor_venta=valor_unitario*cantidad;
    				$row.find('[name=monto]').val(valor_venta);
    				console.log("-CONCEPTO=> "+"valor_unitario: "+valor_unitario+" X cantidad: "+cantidad+" valor_venta: "+valor_venta);
    			}
    		}
    		console.log("==ITEM==");
    		console.log("-[name=cantidad]=>"+cantidad);
    		console.log("-[name=valor_unitario]=>"+valor_unitario);
    		console.log("-[name=igv]=>"+igv);
    		console.log("-[name=precio_venta_unitario]=>"+"valor_unitario: "+valor_unitario+" + igv: "+igv+" /cantidad: "+cantidad+" = "+parseFloat(valor_unitario+igv/cantidad));
    		console.log("-TOTAL=> [name=precio_total]: "+"valor_venta: "+valor_venta+" + igv: "+igv+" -=> "+parseFloat(valor_venta+igv));

    		p.$w.find('[name=cantidad]').val(cantidad).attr('disabled','disabled');
    		p.$w.find('[name=unidad]').val("NIU").attr('disabled','disabled');
    		//p.$w.find('[name=valor_unitario]').val(K.round(valor_unitario,2)).attr('disabled','disabled');
    		p.$w.find('[name=valor_unitario]').val(valor_unitario).attr('disabled','disabled');
    		p.$w.find('[name=tipo_igv]').val("10");
    		//p.$w.find('[name=igv]').val(K.round(igv,2));
    		p.$w.find('[name=igv]').val(igv);
    		p.$w.find('[name=precio_venta_unitario]').val(K.round(valor_unitario+igv/cantidad,2));
    		p.$w.find('[name=precio_venta_unitario]').val(valor_unitario+igv/cantidad);
    		p.$w.find('[name=precio_total]').val(K.round(valor_venta+igv,2));
    	}
    };*/
    p.calcTotChapiDiferencia = function() {
      console.log("CALCULO POR DIFERENCIA DE LA COMUNICACION CI N 010-2018-SBPA-OEI");
      if (p.$w.find('[name=grid] tbody tr').length > 0) {
        p.$w.find('[name=cantidad]').removeAttr('disabled');
        p.$w.find('[name=unidad]').removeAttr('disabled');
        var tipo_igv_global = p.$w.find('[name=tipo_igv] :selected').val();
        var valor_venta = 0;
        var cantidad = 0;
        var valor_unitario = 0;
        var igv = 0;
        for (var i = 0; i < p.$w.find('[name=grid] tbody tr').length; i++) {
          var $row = p.$w.find('[name=grid] tbody tr').eq(i);
          if ($row.data('data').igv != null) {
            //AQUI SE CALCULA EL VALOR DEL IGV MEDIANTE LA DIFERENCIA DEL TOTAL
            var parent_id = $row.data('data').parent;
            var $parent = p.$w.find('[item=' + $row.data('data').parent + ']');
            var valor_venta = $parent.find('[name=monto]').val();
            if (isNaN(parseFloat(valor_venta))) {
              return K.notification({
                title: ciHelper.titleMessages.infoReq,
                text: 'El monto indicado en el item' + (parent_id) + ' no es un valor numerico valido',
                type: 'error'
              });
            } else {
              valor_venta = parseFloat(valor_venta);
            }
            var monto = 0;
            if (tipo_igv_global == '10') {
              precio_de_venta = $row.data('diff_precio_venta');
              //monto = valor_venta*p.config.igv;
              monto = (precio_de_venta * cantidad) - valor_venta;
            }
            $row.find('[name=monto]').val(K.round(monto, 2));
            igv += parseFloat(monto);
            console.log("-IMPUESTO=> " + "(precio_de_venta: " + precio_de_venta + ") X cantidad: " + cantidad + " - valor_venta: " + valor_venta + " =monto: " + K.round(monto, 2) + "(" + monto + ")");
          } else {
            //LA PRIMERA VEZ PASA POR AQUI EL CONCEPTO:
            cantidad = $row.find('[name=cant_item]').val();
            if (!parseFloat(cantidad)) cantidad = 0;
            else cantidad = parseFloat(cantidad);
            //SE RECALCULA EL VALOR UNITARIO
            valor_unitario = $row.find('[name=valor_unitario_item]').val();
            if (!parseFloat(valor_unitario)) {
              valor_unitario = 0;
            } else {
              valor_unitario = parseFloat(valor_unitario);
            }
            //valor_venta=valor_unitario*cantidad;
            //EN EL METODO DE DIFERENCIA SE REDONDEA
            valor_venta = K.round(valor_unitario * cantidad, 2);
            $row.find('[name=monto]').val(valor_venta);
            console.log("-CONCEPTO=> " + "valor_unitario: " + valor_unitario + " X cantidad: " + cantidad + " =valor_venta: " + valor_venta + "(" + valor_unitario * cantidad + ")");

          }
        }
        console.log("==ITEM==");
        console.log("-[name=cantidad]=>" + cantidad);
        console.log("-[name=valor_unitario]=>" + valor_unitario);
        console.log("-[name=igv]=>" + igv);
        console.log("-[name=precio_venta_unitario]=>" + "valor_unitario: " + valor_unitario + " + igv: " + igv + " /cantidad: " + cantidad + " = " + parseFloat(valor_unitario + igv / cantidad));
        console.log("-TOTAL=> [name=precio_total]: " + "valor_venta: " + valor_venta + " + igv: " + igv + " -=> " + parseFloat(valor_venta + igv));
        p.$w.find('[name=cantidad]').val(cantidad).attr('disabled', 'disabled');
        p.$w.find('[name=unidad]').val("NIU").attr('disabled', 'disabled');
        //p.$w.find('[name=valor_unitario]').val(K.round(valor_unitario,2)).attr('disabled','disabled');
        //SE QUITA EL REDONDEO PARA DARLE TODA LA RESOLUCION AL SERVIDOR DE FAVIO
        p.$w.find('[name=valor_unitario]').val(valor_unitario).attr('disabled', 'disabled');
        p.$w.find('[name=tipo_igv]').val("10");
        p.$w.find('[name=igv]').val(K.round(igv, 2));
        p.$w.find('[name=precio_venta_unitario]').val(K.round(valor_unitario + igv / cantidad, 2));
        p.$w.find('[name=precio_total]').val(K.round(valor_venta + igv, 2));
      }
    };
    p.calcTotChapiRegresion = function() {
      console.log("CALCULO MEDIANTE REGRESION PARA AGUA CHAPI");
      if (p.$w.find('[name=grid] tbody tr').length > 0) {
        p.$w.find('[name=cantidad]').removeAttr('disabled');
        p.$w.find('[name=unidad]').removeAttr('disabled');
        var tipo_igv_global = p.$w.find('[name=tipo_igv] :selected').val();
        precio_total = p.$w.find('[name=precio_total]').val();

        var valor_venta = 0;
        var cantidad = 0;
        var valor_unitario = 0;
        var precio_venta_unitario = 0;
        var igv = 0;
        for (var i = 0; i < p.$w.find('[name=grid] tbody tr').length; i++) {
          var $row = p.$w.find('[name=grid] tbody tr').eq(i);
          if ($row.data('data').igv != null) {
            //AQUI SE CALCULA EL VALOR DEL IGV MEDIANTE LA DIFERENCIA DEL TOTAL
            var parent_id = $row.data('data').parent;
            var $parent = p.$w.find('[item=' + $row.data('data').parent + ']');
            var valor_venta = $parent.find('[name=monto]').val();
            if (isNaN(parseFloat(valor_venta))) {
              return K.notification({
                title: ciHelper.titleMessages.infoReq,
                text: 'El monto indicado en el item' + (parent_id) + ' no es un valor numerico valido',
                type: 'error'
              });
            } else {
              valor_venta = parseFloat(valor_venta);
            }
            var monto = 0;
            if (tipo_igv_global == '10') {
              monto = precio_total - valor_venta;
            }
            $row.find('[name=monto]').val(K.round(monto, 2));
            igv += parseFloat(monto);
            console.log("-IMPUESTO=> " + "(precio_de_venta: " + precio_de_venta + ") X cantidad: " + cantidad + " - valor_venta: " + valor_venta + " =monto: " + K.round(monto, 2) + "(" + monto + ")");
          } else {
            //LA PRIMERA VEZ PASA POR AQUI EL CONCEPTO:
            cantidad = $row.find('[name=cant_item]').val();
            if (!parseFloat(cantidad)) cantidad = 0;
            else cantidad = parseFloat(cantidad);
            if (!parseFloat(precio_total)) precio_total = 0;
            else precio_total = parseFloat(precio_total);
            //SE RECALCULA EL precio_venta_unitario
            precio_venta_unitario = precio_total / cantidad;
            valor_unitario = precio_venta_unitario / 1.18;

            //valor_venta=valor_unitario*cantidad;
            //EN EL METODO DE REGRESION
            valor_venta = K.round(valor_unitario * cantidad, 2);
            $row.find('[name=monto]').val(valor_venta);
            $row.find('[name=valor_unitario_item]').val(valor_unitario);
            console.log("-CONCEPTO=> " + "valor_unitario: " + valor_unitario + " X cantidad: " + cantidad + " =valor_venta: " + valor_venta + "(" + valor_unitario * cantidad + ")");

          }
        }
        p.$w.find('[name=cantidad]').val(cantidad).attr('disabled', 'disabled');
        p.$w.find('[name=unidad]').val("NIU").attr('disabled', 'disabled');
        p.$w.find('[name=precio_venta_unitario]').val(K.round(precio_venta_unitario, 2));
        p.$w.find('[name=valor_unitario]').val(K.roundValorUnitario(valor_unitario, 10)).attr('disabled', 'disabled');
        p.$w.find('[name=tipo_igv]').val("10");
        p.$w.find('[name=igv]').val(K.round(igv, 2));
      }
    };
    /*p.calcTotFarmacia = function(){
    	//CALCULO LEGADO, NO SE UTILIZA PORQUE DA ERRORES DE DECIMALES
    	if(p.$w.find('[name=grid] tbody tr').length>0){
    		p.$w.find('[name=cantidad]').removeAttr('disabled');
    		p.$w.find('[name=unidad]').removeAttr('disabled');
    		var tipo_igv_global = p.$w.find('[name=tipo_igv] :selected').val();
    		var valor_venta = 0;
    		var cantidad = 0;
    		var valor_unitario = 0;
    		var igv = 0;
    		for(var i=0;i<p.$w.find('[name=grid] tbody tr').length;i++){
    			var $row = p.$w.find('[name=grid] tbody tr').eq(i);
    			//AQUI SE CALCULA EL VALOR DEL IGV Y DE EL STOCK
    			console.log("VALOR DEL CALCULO DE STOCK");
    			console.log($row.data('data'));
    			if($row.data('data').igv!=null){
    				var parent_id = $row.data('data').parent;
    				var $parent = p.$w.find('[item='+$row.data('data').parent+']');
    				var valor_venta = $parent.find('[name=monto]').val();
    				//console.log($parent);
    				if(isNaN(parseFloat(valor_venta))){
    					return K.notification({
    						title: ciHelper.titleMessages.infoReq,
    						text: 'El monto indicado en el item'+(parent_id)+' no es un valor numerico valido',
    						type: 'error'
    					});
    				}else{
    					valor_venta = parseFloat(valor_venta);
    					//console.log();
    				}
    				var monto = 0;
    				if(tipo_igv_global=='10'){
    					//porcion original
    					//monto = valor_venta*p.config.igv;
    					monto = $row.data('data').prec_comp * cantidad - valor_venta;
    				}
    				$row.find('[name=monto]').val(K.round(monto,2));
    				igv+=parseFloat(monto);
    				console.log("***CALCULO EN EL IGV**");
    				console.log(monto);
    				console.log(parseFloat(monto));
    				console.log(parseFloat(monto)*100);
    				console.log(Math.round(parseFloat(monto)*100));
    				console.log(Math.round(parseFloat(monto)*100)/100);
    				console.log(K.round(monto,2));
    				console.log(valor_venta);
    			}else{
    				//PARA EL CASO DE EL SUBTOTAL:
    				cantidad = $row.find('[name=cant_item]').val();
    				if(!parseFloat(cantidad)) cantidad=0;
    				else cantidad = parseFloat(cantidad);
    				//SE RECALCULA EL VALOR UNITARIO HEREDADO DEL IGV
    				valor_unitario = $row.find('[name=valor_unitario_item]').val();
    				//valor_unitario = $row.find('[name=valor_unitario_item]').val(K.round(($row.data('data').prec_comp*cantidad/1.18),2));
    				if(!parseFloat(valor_unitario)) valor_unitario=0;
    				else valor_unitario = parseFloat(valor_unitario);
    				valor_venta=K.round(valor_unitario*cantidad,2);
    				//valor_venta=K.round(K.round(($row.data('data').prec_comp),2)*cantidad/1.18,2);
    				//valor_venta=K.round($row.data('data').prec_comp*cantidad/1.18,2);
    				//console.log($row.data('data').prec_comp*cantidad/1.18);
    				console.log(valor_venta);
    				$row.find('[name=monto]').val(valor_venta);
    			}
    		}
    		p.$w.find('[name=cantidad]').val(cantidad).attr('disabled','disabled');
    		p.$w.find('[name=unidad]').val("NIU").attr('disabled','disabled');
    		p.$w.find('[name=valor_unitario]').val(K.round(valor_unitario,2)).attr('disabled','disabled');
    		p.$w.find('[name=tipo_igv]').val("10");
    		p.$w.find('[name=igv]').val(K.round(igv,2));
    		p.$w.find('[name=precio_venta_unitario]').val(K.round(valor_unitario+igv/cantidad,2));
    		p.$w.find('[name=precio_total]').val(K.round(valor_venta+igv,2));
    	}
    };*/
    p.calcTotFarmaciaDiferencia = function() {
      console.log("CALCULO POR DIFERENCIA DE LA COMUNICACION CI N 010-2018-SBPA-OEI");
      if (p.$w.find('[name=grid] tbody tr').length > 0) {
        p.$w.find('[name=cantidad]').removeAttr('disabled');
        p.$w.find('[name=unidad]').removeAttr('disabled');
        var tipo_igv_global = p.$w.find('[name=tipo_igv] :selected').val();
        var valor_venta = 0;
        var cantidad = 0;
        var valor_unitario = 0;
        var igv = 0;
        for (var i = 0; i < p.$w.find('[name=grid] tbody tr').length; i++) {
          var $row = p.$w.find('[name=grid] tbody tr').eq(i);
          if ($row.data('data').igv != null) {
            //AQUI SE CALCULA EL VALOR DEL IGV MEDIANTE LA DIFERENCIA DEL TOTAL
            var parent_id = $row.data('data').parent;
            var $parent = p.$w.find('[item=' + $row.data('data').parent + ']');
            var valor_venta = $parent.find('[name=monto]').val();
            if (isNaN(parseFloat(valor_venta))) {
              return K.notification({
                title: ciHelper.titleMessages.infoReq,
                text: 'El monto indicado en el item' + (parent_id) + ' no es un valor numerico valido',
                type: 'error'
              });
            } else {
              valor_venta = parseFloat(valor_venta);
            }
            var monto = 0;
            if (tipo_igv_global == '10') {
              precio_de_venta = $row.data('diff_precio_venta');
              //monto = valor_venta*p.config.igv;
              monto = (precio_de_venta * cantidad) - valor_venta;
            }
            $row.find('[name=monto]').val(K.round(monto, 2));
            igv += parseFloat(monto);
            console.log("-IMPUESTO=> " + "(precio_de_venta: " + precio_de_venta + ") X cantidad: " + cantidad + " - valor_venta: " + valor_venta + " =monto: " + K.round(monto, 2) + "(" + monto + ")");
          } else {
            //LA PRIMERA VEZ PASA POR AQUI EL CONCEPTO:

            if ($row.find('[name=cant_item]').val() > $row.data('maxstock')) {
              $row.find('[name=cant_item]').val($row.data('maxstock'));
              K.notification({
                title: ciHelper.titleMessages.infoReq,
                text: 'No hay stock para realizar la venta',
                type: 'error'
              });
            }
            cantidad = $row.find('[name=cant_item]').val();

            if (!parseFloat(cantidad)) cantidad = 0;
            else cantidad = parseFloat(cantidad);
            //SE RECALCULA EL VALOR UNITARIO
            valor_unitario = $row.find('[name=valor_unitario_item]').val();
            if (!parseFloat(valor_unitario)) {
              valor_unitario = 0;
            } else {
              valor_unitario = parseFloat(valor_unitario);
            }
            //valor_venta=valor_unitario*cantidad;
            //EN EL METODO DE DIFERENCIA SE REDONDEA
            valor_venta = K.round(valor_unitario * cantidad, 2);
            $row.find('[name=monto]').val(valor_venta);
            console.log("-CONCEPTO=> " + "valor_unitario: " + valor_unitario + " X cantidad: " + cantidad + " =valor_venta: " + valor_venta + "(" + valor_unitario * cantidad + ")");

          }
        }
        console.log("==ITEM==");
        console.log("-[name=cantidad]=>" + cantidad);
        console.log("-[name=valor_unitario]=>" + valor_unitario);
        console.log("-[name=igv]=>" + igv);
        console.log("-[name=precio_venta_unitario]=>" + "valor_unitario: " + valor_unitario + " + igv: " + igv + " /cantidad: " + cantidad + " = " + parseFloat(valor_unitario + igv / cantidad));
        console.log("-TOTAL=> [name=precio_total]: " + "valor_venta: " + valor_venta + " + igv: " + igv + " -=> " + parseFloat(valor_venta + igv));
        p.$w.find('[name=cantidad]').val(cantidad).attr('disabled', 'disabled');
        p.$w.find('[name=unidad]').val("NIU").attr('disabled', 'disabled');
        //p.$w.find('[name=valor_unitario]').val(K.round(valor_unitario,2)).attr('disabled','disabled');
        //SE QUITA EL REDONDEO PARA DARLE TODA LA RESOLUCION AL SERVIDOR DE FAVIO
        p.$w.find('[name=valor_unitario]').val(valor_unitario).attr('disabled', 'disabled');
        p.$w.find('[name=tipo_igv]').val("10");
        p.$w.find('[name=igv]').val(K.round(igv, 2));
        p.$w.find('[name=precio_venta_unitario]').val(K.round(valor_unitario + igv / cantidad, 2));
        p.$w.find('[name=precio_total]').val(K.round(valor_venta + igv, 2));
      }
    };
    p.calcTotFarmaciaRegresion = function() {
      console.log("CALCULO MEDIANTE REGRESION PARA FARMACIA SEGUN OFICIO  ");
      if (p.$w.find('[name=grid] tbody tr').length > 0) {
        p.$w.find('[name=cantidad]').removeAttr('disabled');
        p.$w.find('[name=unidad]').removeAttr('disabled');
        var tipo_igv_global = p.$w.find('[name=tipo_igv] :selected').val();
        precio_total = p.$w.find('[name=precio_total]').val();

        var valor_venta = 0;
        var cantidad = 0;
        var valor_unitario = 0;
        var precio_venta_unitario = 0;
        var igv = 0;
        for (var i = 0; i < p.$w.find('[name=grid] tbody tr').length; i++) {
          var $row = p.$w.find('[name=grid] tbody tr').eq(i);
          if ($row.data('data').igv != null) {
            //AQUI SE CALCULA EL VALOR DEL IGV MEDIANTE LA DIFERENCIA DEL TOTAL
            var parent_id = $row.data('data').parent;
            var $parent = p.$w.find('[item=' + $row.data('data').parent + ']');
            var valor_venta = $parent.find('[name=monto]').val();
            if (isNaN(parseFloat(valor_venta))) {
              return K.notification({
                title: ciHelper.titleMessages.infoReq,
                text: 'El monto indicado en el item' + (parent_id) + ' no es un valor numerico valido',
                type: 'error'
              });
            } else {
              valor_venta = parseFloat(valor_venta);
            }
            var monto = 0;
            if (tipo_igv_global == '10') {
              monto = precio_total - valor_venta;
            }
            $row.find('[name=monto]').val(K.round(monto, 2));
            igv += parseFloat(monto);
            console.log("-IMPUESTO=> " + "(precio_de_venta: " + precio_de_venta + ") X cantidad: " + cantidad + " - valor_venta: " + valor_venta + " =monto: " + K.round(monto, 2) + "(" + monto + ")");
          } else {
            //LA PRIMERA VEZ PASA POR AQUI EL CONCEPTO:
            cantidad = $row.find('[name=cant_item]').val();
            if (!parseFloat(cantidad)) cantidad = 0;
            else cantidad = parseFloat(cantidad);
            if (!parseFloat(precio_total)) precio_total = 0;
            else precio_total = parseFloat(precio_total);
            //SE RECALCULA EL precio_venta_unitario
            precio_venta_unitario = precio_total / cantidad;
            valor_unitario = precio_venta_unitario / 1.18;

            //valor_venta=valor_unitario*cantidad;
            //EN EL METODO DE REGRESION
            valor_venta = K.round(valor_unitario * cantidad, 2);
            $row.find('[name=monto]').val(valor_venta);
            $row.find('[name=valor_unitario_item]').val(valor_unitario);
            console.log("-CONCEPTO=> " + "valor_unitario: " + valor_unitario + " X cantidad: " + cantidad + " =valor_venta: " + valor_venta + "(" + valor_unitario * cantidad + ")");

          }
        }
        p.$w.find('[name=cantidad]').val(cantidad).attr('disabled', 'disabled');
        p.$w.find('[name=unidad]').val("NIU").attr('disabled', 'disabled');
        p.$w.find('[name=precio_venta_unitario]').val(K.round(precio_venta_unitario, 2));
        p.$w.find('[name=valor_unitario]').val(K.roundValorUnitario(valor_unitario, 10)).attr('disabled', 'disabled');
        p.$w.find('[name=tipo_igv]').val("10");
        p.$w.find('[name=igv]').val(K.round(igv, 2));
      }
    };
    p.calcTotInmuebles = function() {
      if (p.$w.find('[name=grid] tbody tr').length > 0) {
        p.$w.find('[name=cantidad]').removeAttr('disabled');
        p.$w.find('[name=unidad]').removeAttr('disabled');
        var valor_venta = 0;
        var igv = 0;
        for (var i = 0; i < p.$w.find('[name=grid] tbody tr').length; i++) {
          var $row = p.$w.find('[name=grid] tbody tr').eq(i);
          if ($row.data('data').porcentaje_mora != null) {
            var mora_porc = $row.find('[name=mora]').val();
            if (mora_porc != "") {
              var $parent = p.$w.find('[item=' + $row.data('data').parent + ']');
              var alquiler = $parent.find('[name=monto]').val();
              alquiler = parseFloat(alquiler);
              if (isNaN(alquiler)) alquiler = 0;
              var monto = mora_porc * alquiler / 100;
              $row.find('[name=monto]').val(K.round(monto, 2));
            }
          } else if ($row.data('data').igv != null) {
            //var parent_id = $row.data('data').parent-1;
            var $parent = p.$w.find('[item=' + $row.data('data').parent + ']');
            var valor_venta_item = $parent.find('[name=monto]').val();
            if (!parseFloat(valor_venta_item)) {
              return K.notification({
                title: ciHelper.titleMessages.infoReq,
                text: 'El monto indicado en el item' + (parent_id) + ' no es un valor numerico valido',
                type: 'error'
              });
            } else {
              valor_venta_item = parseFloat(valor_venta_item);
            }
            var monto = valor_venta_item * p.config.igv;
            $row.find('[name=monto]').val(K.round(monto, 2));
            igv += parseFloat(monto);
          } else {
            valor_venta += parseFloat($row.find('[name=monto]').val());
          }
        }
        p.$w.find('[name=cantidad]').val("1").attr('disabled', 'disabled');
        p.$w.find('[name=unidad]').val("ZZ").attr('disabled', 'disabled');
        p.$w.find('[name=valor_unitario]').val(K.round(valor_venta, 2));
        p.$w.find('[name=tipo_igv]').val("10");
        p.$w.find('[name=igv]').val(K.round(igv, 2));
        p.$w.find('[name=precio_venta_unitario]').val(K.round(valor_venta + igv, 2));
        p.$w.find('[name=precio_total]').val(K.round(valor_venta + igv, 2));
      }
    };
    p.calcTotCuentaCobrar = function() {
      if (p.$w.find('[name=grid] tbody tr').length > 0) {
        p.$w.find('[name=cantidad]').removeAttr('disabled');
        p.$w.find('[name=unidad]').removeAttr('disabled');
        var tipo_igv_global = p.$w.find('[name=tipo_igv] :selected').val();
        var valor_venta = 0;
        var igv = 0;
        var tmp_total = 0;
        for (var i = 0; i < p.$w.find('[name=grid] tbody tr').length; i++) {
          var $row = p.$w.find('[name=grid] tbody tr').eq(i);
          if ($row.data('data').igv != null) {
            igv += valor_venta * p.config.igv;
            $row.find('[name=monto]').val(K.round(igv, 2));
          } else {
            var monto_data = parseFloat($row.data('data').monto);
            monto = monto_data;
            if (tipo_igv_global == '10') {
              monto = monto_data / (1 + p.config.igv);
            }
            valor_venta += monto;
            $row.find('[name=monto]').val(K.round(monto, 2));
          }
        }
        igv = K.round(igv, 2);
        valor_venta = K.round(valor_venta, 2);
        p.$w.find('[name=cantidad]').val("1").attr('disabled', 'disabled');
        p.$w.find('[name=unidad]').val("ZZ").attr('disabled', 'disabled');
        p.$w.find('[name=valor_unitario]').val(K.round(valor_venta, 2));
        p.$w.find('[name=tipo_igv]').val("10");
        p.$w.find('[name=igv]').val(K.round(igv, 2));
        p.$w.find('[name=precio_venta_unitario]').val(K.round(parseFloat(valor_venta) + parseFloat(igv), 2));
        p.$w.find('[name=precio_total]').val(K.round(parseFloat(valor_venta) + parseFloat(igv), 2));
      }
    };
    p.calcTotActas = function() {
      if (p.$w.find('[name=grid] tbody tr').length > 0) {
        p.$w.find('[name=cantidad]').removeAttr('disabled');
        p.$w.find('[name=unidad]').removeAttr('disabled');
        var valor_venta = 0;
        var igv = 0;
        for (var i = 0; i < p.$w.find('[name=grid] tbody tr').length; i++) {
          var $row = p.$w.find('[name=grid] tbody tr').eq(i);
          if ($row.data('data').porcentaje_mora != null) {
            var mora_porc = $row.find('[name=mora]').val();
            var monto = 0;
            if (parseFloat(mora_porc)) {
              //var $parent = p.$w.find('[item='+$row.data('data').parent+']');
              monto = parseFloat(mora_porc) * valor_venta / 100;
            }
            $row.find('[name=monto]').val(K.round(monto, 2));
          } else if ($row.data('data').igv != null) {
            //var parent_id = $row.data('data').parent-1;
            var monto = parseFloat(K.round($row.find('[name=monto]').val(), 2));
            igv += parseFloat(monto);
          } else if ($row.data('data').monto_mora != null) {
            //valor_venta+=parseFloat($row.find('[name=monto]').val());
          } else {
            valor_venta += parseFloat($row.find('[name=monto]').val());
          }
        }
        p.$w.find('[name=cantidad]').val("1").attr('disabled', 'disabled');
        p.$w.find('[name=unidad]').val("ZZ").attr('disabled', 'disabled');
        p.$w.find('[name=valor_unitario]').val(K.round(valor_venta, 2));
        p.$w.find('[name=tipo_igv]').val("10");
        p.$w.find('[name=igv]').val(K.round(igv, 2));
        p.$w.find('[name=precio_venta_unitario]').val(K.round(valor_venta + igv, 2));
        p.$w.find('[name=precio_total]').val(K.round(valor_venta + igv, 2));
      }
    };
    p.loadConc = function() {
      var $table, espacio, conceptos, variables, servicio, SERV = {},
        __VALUE__ = 0,
        cuotas = 0;
      SERV = {
        SALDO: 0,
        FECVEN: 0,
        CM_PREC_PERP: 0,
        CM_PREC_TEMP: 0,
        CM_PREC_VIDA: 0,
        CM_ACCE_PREC: 0,
        CM_TIPO_ESPA: 0
      };
      variables = p.$w.data('vars');
      if (variables == null) {
        return K.notification({
          title: 'Servicio no seleccionado',
          text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',
          type: 'error'
        });
      } else {
        for (var i = 0, j = variables.length; i < j; i++) {
          try {
            if (variables[i].valor == 'true') eval('var ' + variables[i].cod + ' = true;');
            else if (variables[i].valor == 'false') eval('var ' + variables[i].cod + ' = false;');
            else eval('var ' + variables[i].cod + ' = ' + variables[i].valor + ';');
          } catch (e) {
            console.warn('error en carga de variables');
          }
        }
      }
      p.$w.find('[name=grid] tbody').empty();
      $table = p.$w;
      servicio = $table.find('[name^=serv]').data('data');
      conceptos = $table.find('[name^=serv]').data('concs');
      if (servicio == null) {
        return K.notification({
          title: 'Servicio no seleccionado',
          text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',
          type: 'error'
        });
      }
      SERV.FECVEN = 0;
      if ($table.find('[name^=fecven]').length > 0) {
        if ($table.find('[name^=fecven]').val() == '') {
          $table.find('[name^=fecven]').focus();
          return K.notification({
            title: ciHelper.titleMessages.infoReq,
            text: 'Debe seleccionar una fecha de vencimiento!',
            type: 'error'
          });
        }
        SERV.FECVEN = ciHelper.date.diffDays(new Date(), $table.find('[name^=fecven]:eq(0)').data("DateTimePicker").date());
      }
      if (SERV.FECVEN < 0) SERV.FECVEN = 0;
      p.conceptos = conceptos;
      for (var i = 0, j = conceptos.length; i < j; i++) {
        var $row = $('<tr class="item" name="' + conceptos[i]._id.$id + '">');
        var monto = eval(conceptos[i].formula);
        eval("var " + conceptos[i].cod + " = " + monto + ";");
        $row.append('<td>' + conceptos[i].nomb + '</td>');
        $row.append('<td>');
        $row.append('<td>');
        if (conceptos[i].formula.indexOf('__VALUE__') != -1) {
          var formula = conceptos[i].formula;
          formula = ciHelper.string.replaceAll(formula, "__VALUE__", "__VALUE" + conceptos[i].cod + "__");
          $row.find('td:eq(1)').html('<input type="number" size="7" name="codform' + conceptos[i].cod + '">');
          $row.find('[name^=codform]').val(0).change(function() {
            var val = parseFloat($(this).val()),
              formula = $(this).data('form'),
              cod = $(this).data('cod'),
              $row = $(this).closest('.item');
            eval("var __VALUE" + cod + "__ = " + val + ";");
            var monto = eval(formula);
            $row.find('td:eq(2)').html(ciHelper.formatMon(monto));
            $row.data('monto', monto);
            eval('var ' + cod + ' = ' + monto);
            for (var ii = 0, jj = p.conceptos.length; ii < jj; ii++) {
              var $row = $table.find('.grid .item').eq(ii),
                $cell = $row.find('li').eq(2),
                monto = eval($cell.data('formula'));
              if ($cell.data('formula') != null) {
                $cell.html(ciHelper.formatMon(monto));
                $row.data('monto', monto);
              }
            }
            p.calcConc();
          }).data('form', formula).data('cod', conceptos[i].cod);
        } else {
          eval('var ' + conceptos[i].cod + ' = ' + monto + ';');
          $row.find('td:eq(2)').data('formula', conceptos[i].formula);
        }
        $row.find('td:eq(2)').html(ciHelper.formatMon(monto));
        $row.data('monto', monto);
        $table.find("[name=grid] tbody").append($row);
      }
      p.calcConc();
    };
    p.calcConc = function() {
      K.clearNoti();
      /*var $table, servicio, conceptos, total = 0, cuotas=0;
      $table = p.$w;
      servicio = $table.find('[name^=serv]').data('data');
      conceptos = $table.find('[name^=serv]').data('concs');
      if(servicio==null){
      	return K.notification({title: 'Servicio no seleccionado',text: 'Debe seleccionar un servicio para poder realizar los c&aacute;lculos!',type: 'error'});
      }
      for(var i=0,j=conceptos.length; i<j; i++){
      	total += parseFloat($table.find('.item').eq(i).data('monto'));
      }
      if(conceptos.length!=p.$w.find('[name=gridCob] tbody .item').length){
      	p.$w.find('[name=gridCob] tbody .item:last').remove();
      }
      var $row = $('<tr class="item">');
      $row.append('<td colspan="2" style="text-align:right">Total</td>');
      $row.append('<td>'+ciHelper.formatMon(total)+'</td>');
      $row.data('total',total);
      $table.find("[name=gridCob] tbody").append($row);
      p.$w.find('[name=gridPag] [name=tot]').val('0');
      p.$w.find('[name=gridPag] [name=tot]:first').val(total);*/


      if (p.$w.find('[name=grid] tbody tr').length > 0) {
        p.$w.find('[name=cantidad]').removeAttr('disabled');
        p.$w.find('[name=unidad]').removeAttr('disabled');
        var tipo_igv_global = p.$w.find('[name=tipo_igv] :selected').val();
        var valor_venta = 0;
        var igv = 0;
        for (var i = 0; i < p.$w.find('[name=grid] tbody tr').length; i++) {
          var $row = p.$w.find('[name=grid] tbody tr').eq(i);
          if ($row.data('data').porcentaje_mora != null) {
            var mora_porc = $row.find('[name=mora]').val();
            var monto = 0;
            if (parseFloat(mora_porc)) {
              //var $parent = p.$w.find('[item='+$row.data('data').parent+']');
              monto = parseFloat(mora_porc) * valor_venta / 100;
            }
            $row.find('[name=monto]').val(K.round(monto, 2));
          } else if ($row.data('data').igv != null) {
            //var parent_id = $row.data('data').parent-1;
            var monto = 0;
            if (tipo_igv_global == '10') {
              monto = K.round(valor_venta * p.config.igv, 2);
            }
            $row.find('[name=monto]').val(monto);
            var monto = parseFloat($row.find('[name=monto]').val());
            igv += parseFloat(monto);
          } else {
            var monto_data = $row.data('monto');
            var monto = monto_data;
            if (!parseFloat(monto_data)) monto_data = 0;
            if (tipo_igv_global == '10') {
              monto = monto_data / (1 + p.config.igv);
            }
            $row.find('[name=monto]').val(K.round(monto, 2));
            valor_venta += parseFloat(monto);
          }
        }
        p.$w.find('[name=cantidad]').val("1").attr('disabled', 'disabled');
        p.$w.find('[name=unidad]').val("ZZ").attr('disabled', 'disabled');
        p.$w.find('[name=valor_unitario]').val(K.round(valor_venta, 2));
        p.$w.find('[name=tipo_igv]').val(tipo_igv_global);
        p.$w.find('[name=igv]').val(K.round(igv, 2));
        p.$w.find('[name=precio_venta_unitario]').val(K.round(valor_venta + igv, 2));
        p.$w.find('[name=precio_total]').val(K.round(valor_venta + igv, 2));
      }
    };

    p.tipo = null;
    p.cliente = null;
    p.moneda = null;
    K.block();
    new K.Modal({
      id: 'windowRowConfiguration',
      title: 'Configuracion de Fila',
      contentURL: 'cj/ecom/row_configuration',
      icon: 'ui-icon-plusthick',
      width: 1050,
      height: 650,
      buttons: {
        "Guardar cambios": function() {
          /*console.log('========================BEFORE DECLARE DATA=======================');
          console.log(data);*/
          var data_header = {
            cliente: p.cliente
          };
          var data = {
            codigo: '',
            descr: p.$w.find('[name=descripcion_item]').val(),
            cant: p.$w.find('[name=cantidad]').val(),
            cod_unidad: p.$w.find('[name=unidad] :selected').val(),
            unidad: p.$w.find('[name=unidad] :selected').text(),
            valor_unitario: p.$w.find('[name=valor_unitario]').val(),
            tipo_igv: p.$w.find('[name=tipo_igv] :selected').val(),
            igv: p.$w.find('[name=igv]').val(),
            precio_venta_unitario: p.$w.find('[name=precio_venta_unitario]').val(),
            precio_total: p.$w.find('[name=precio_total]').val(),
            tipo: p.tipo,
            subitems: [],
            conceptos: []
          };
          data.tipo_agregacion = 'after';
          /*console.log('========================EMPTY CONCEPTS=======================');
          console.log(data.subitems);
          console.log(data.conceptos);*/

          var monto_mora_inmuebles = 0;
          var subitem_tmp = null;
          var _subitem_tmp = null;
          switch (p.tipo) {
            case 'pago_meses':
              for (var i = 0; i < p.$w.find('[name=grid] tbody tr').length; i++) {
                var $row = p.$w.find('[name=grid] tbody tr').eq(i);
                var _data = $row.data('data');
                if (_data != null) {
                  _data.monto = $row.find('[name=monto]').val();
                  if (isNaN(parseFloat(_data.monto))) {
                    return K.notification({
                      title: ciHelper.titleMessages.infoReq,
                      text: 'El monto indicado en el item' + (i + 1) + ' no es un valor numerico valido',
                      type: 'error'
                    });
                  } else {
                    _data.monto = parseFloat(_data.monto);
                  }

                  if (_data.porcentaje_mora != null) {
                    _data.porcentaje_mora = $row.find('[name=mora]').val();
                    if (isNaN(parseFloat(_data.porcentaje_mora))) {
                      _data.porcentaje_mora = 0;
                      return K.notification({
                        title: ciHelper.titleMessages.infoReq,
                        text: 'El porcentaje de mora indicado en el item' + (i + 1) + ' no es un valor numerico valido',
                        type: 'error'
                      });
                    } else {
                      _data.porcentaje_mora = parseFloat(_data.porcentaje_mora)
                    }
                    monto_mora_inmuebles += _data.monto;
                  }
                }
                data.conceptos.push(_data);
              }
              break;
            case 'pago_parcial':
              for (var i = 0; i < p.$w.find('[name=grid] tbody tr').length; i++) {
                var $row = p.$w.find('[name=grid] tbody tr').eq(i);
                var _data = $row.data('data');
                if (_data != null) {
                  _data.monto = $row.find('[name=monto]').val();
                  if (isNaN(parseFloat(_data.monto))) {
                    return K.notification({
                      title: ciHelper.titleMessages.infoReq,
                      text: 'El monto indicado en el item' + (i + 1) + ' no es un valor numerico valido',
                      type: 'error'
                    });
                  } else {
                    _data.monto = parseFloat(_data.monto);
                  }


                  if (_data.porcentaje_mora != null) {
                    _data.porcentaje_mora = $row.find('[name=mora]').val();
                    if (isNaN(parseFloat(_data.porcentaje_mora))) {
                      _data.porcentaje_mora = 0;
                      return K.notification({
                        title: ciHelper.titleMessages.infoReq,
                        text: 'El porcentaje de mora indicado en el item' + (i + 1) + ' no es un valor numerico valido',
                        type: 'error'
                      });
                    } else {
                      _data.porcentaje_mora = parseFloat(_data.porcentaje_mora)
                    }
                    monto_mora_inmuebles += _data.monto;
                  }
                }
                data.conceptos.push(_data);
              }
              break;
            case 'cuenta_cobrar':
              for (var i = 0; i < p.$w.find('[name=grid] tbody tr').length; i++) {
                var $row = p.$w.find('[name=grid] tbody tr').eq(i);
                var _data = $row.data('data');
                if (_data != null) {
                  _data.monto = $row.find('[name=monto]').val();
                  if (isNaN(parseFloat(_data.monto))) {
                    return K.notification({
                      title: ciHelper.titleMessages.infoReq,
                      text: 'El monto indicado en el item' + (i + 1) + ' no es un valor numerico valido',
                      type: 'error'
                    });
                  } else {
                    _data.monto = parseFloat(_data.monto);
                  }
                }
                data.cuenta_cobrar = _data.cuenta_cobrar;
                data.conceptos.push(_data);
              }
              break;
            case 'pago_acta':
              for (var i = 0; i < p.$w.find('[name=grid] tbody tr').length; i++) {
                var $row = p.$w.find('[name=grid] tbody tr').eq(i);
                var _data = $row.data('data');
                if (_data != null) {
                  _data.monto = $row.find('[name=monto]').val();
                  console.log(_data.monto);
                  if (isNaN(parseFloat(_data.monto))) {
                    return K.notification({
                      title: ciHelper.titleMessages.infoReq,
                      text: 'El monto indicado en el item' + (i + 1) + ' no es un valor numerico valido',
                      type: 'error'
                    });
                  } else {
                    _data.monto = parseFloat(_data.monto);
                  }
                  if (_data.porcentaje_mora != null) {
                    _data.porcentaje_mora = $row.find('[name=mora]').val();
                    if (isNaN(parseFloat(_data.porcentaje_mora))) {
                      _data.porcentaje_mora = 0;
                      return K.notification({
                        title: ciHelper.titleMessages.infoReq,
                        text: 'El porcentaje de mora indicado en el item' + (i + 1) + ' no es un valor numerico valido',
                        type: 'error'
                      });
                    } else {
                      _data.porcentaje_mora = parseFloat(_data.porcentaje_mora)
                    }
                  }
                }
                //if(subitem_tmp==null){
                if (_data.igv == null && _data.monto_mora == null && _data.porcentaje_mora == null) {
                  subitem_tmp = {
                    codigo: '',
                    descr: _data.descr,
                    cant: 1,
                    cod_unidad: 'ZZ',
                    unidad: 'SERVICIO',
                    valor_unitario: _data.monto,
                    tipo_igv: p.$w.find('[name=tipo_igv] :selected').val(),
                    igv: 0,
                    precio_venta_unitario: _data.monto,
                    precio_total: _data.monto,
                    tipo: 'subitem'
                  };
                } else if (_data.monto_mora != null) {
                  _subitem_tmp = {
                    codigo: '',
                    descr: _data.descr,
                    cant: 1,
                    cod_unidad: 'ZZ',
                    unidad: 'SERVICIO',
                    valor_unitario: _data.monto,
                    tipo_igv: '30',
                    igv: 0,
                    precio_venta_unitario: _data.monto,
                    precio_total: _data.monto,
                    tipo: 'subitem'
                  };
                  data.subitems.push(_subitem_tmp);
                  _subitem_tmp = null;
                } else if (_data.porcentaje_mora != null) {
                  _subitem_tmp = {
                    codigo: '',
                    descr: _data.descr,
                    cant: 1,
                    cod_unidad: 'ZZ',
                    unidad: 'SERVICIO',
                    valor_unitario: _data.monto,
                    tipo_igv: '30',
                    igv: 0,
                    precio_venta_unitario: _data.monto,
                    precio_total: _data.monto,
                    tipo: 'subitem'
                  };
                  data.subitems.push(_subitem_tmp);
                  _subitem_tmp = null;
                }
                //}else{
                if (_data.igv != null && subitem_tmp != null) {
                  subitem_tmp.igv += _data.monto;
                  subitem_tmp.precio_venta_unitario += _data.monto;
                  subitem_tmp.precio_total += _data.monto;
                  if (data.descr == '') {
                    data.descr = subitem_tmp.descr;
                    data.cant = subitem_tmp.cant;
                    data.cod_unidad = subitem_tmp.cod_unidad;
                    data.unidad = subitem_tmp.unidad;
                    data.valor_unitario = subitem_tmp.valor_unitario;
                    data.igv = subitem_tmp.igv;
                    data.precio_venta_unitario = K.round(subitem_tmp.precio_venta_unitario, 2);
                    data.precio_total = K.round(subitem_tmp.precio_total, 2);
                    subitem_tmp = null;
                  } else {
                    data.subitems.push(subitem_tmp);
                    subitem_tmp = null;
                  }
                }
                //}
                data.conceptos.push(_data);
              }
              if (subitem_tmp != null) {
                data.subitems.push(subitem_tmp);
              }
              data.tipo_agregacion = 'append';
              break;
            case 'agua_chapi':
              for (var i = 0; i < p.$w.find('[name=grid] tbody tr').length; i++) {
                var $row = p.$w.find('[name=grid] tbody tr').eq(i);
                var _data = $row.data('data');
                if (_data != null) {
                  _data.monto = $row.find('[name=monto]').val();
                  if (isNaN(parseFloat(_data.monto))) {
                    return K.notification({
                      title: ciHelper.titleMessages.infoReq,
                      text: 'El monto indicado en el item' + (i + 1) + ' no es un valor numerico valido',
                      type: 'error'
                    });
                  } else {
                    _data.monto = parseFloat(_data.monto);
                  }
                  if (_data.producto != null) {
                    _data.cant = $row.find('[name=cant_item]').val();
                    if (isNaN(parseFloat(_data.cant))) {
                      return K.notification({
                        title: ciHelper.titleMessages.infoReq,
                        text: 'La cantidad indicada en el item' + (i + 1) + ' no es un valor numerico valido',
                        type: 'error'
                      });
                    } else {
                      _data.cant = parseFloat(_data.cant);
                    }
                    _data.valor_unitario = $row.find('[name=valor_unitario_item]').val();
                    if (isNaN(parseFloat(_data.valor_unitario))) {
                      return K.notification({
                        title: ciHelper.titleMessages.infoReq,
                        text: 'El valor unitario indicado en el item' + (i + 1) + ' no es un valor numerico valido',
                        type: 'error'
                      });
                    } else {
                      _data.valor_unitario = parseFloat(_data.valor_unitario);
                    }
                  }
                }
                data.conceptos.push(_data);
              }
              break;
            case 'farmacia':
              for (var i = 0; i < p.$w.find('[name=grid] tbody tr').length; i++) {
                var $row = p.$w.find('[name=grid] tbody tr').eq(i);
                var _data = $row.data('data');
                if (_data != null) {
                  _data.monto = $row.find('[name=monto]').val();
                  if (isNaN(parseFloat(_data.monto))) {
                    return K.notification({
                      title: ciHelper.titleMessages.infoReq,
                      text: 'El monto indicado en el item' + (i + 1) + ' no es un valor numerico valido',
                      type: 'error'
                    });
                  } else {
                    _data.monto = parseFloat(_data.monto);
                  }
                  if (_data.producto != null) {
                    _data.cant = $row.find('[name=cant_item]').val();
                    if (isNaN(parseFloat(_data.cant))) {
                      return K.notification({
                        title: ciHelper.titleMessages.infoReq,
                        text: 'La cantidad indicada en el item' + (i + 1) + ' no es un valor numerico valido',
                        type: 'error'
                      });
                    } else {
                      _data.cant = parseFloat(_data.cant);
                    }
                    _data.valor_unitario = $row.find('[name=valor_unitario_item]').val();
                    if (isNaN(parseFloat(_data.valor_unitario))) {
                      return K.notification({
                        title: ciHelper.titleMessages.infoReq,
                        text: 'El valor unitario indicado en el item' + (i + 1) + ' no es un valor numerico valido',
                        type: 'error'
                      });
                    } else {
                      _data.valor_unitario = parseFloat(_data.valor_unitario);
                    }
                  }
                }
                data.conceptos.push(_data);
              }
              break;
            case 'servicio':
              for (var i = 0; i < p.$w.find('[name=grid] tbody tr').length; i++) {
                var $row = p.$w.find('[name=grid] tbody tr').eq(i);
                var _data = $row.data('data');
                if (_data != null) {
                  _data.monto = $row.find('[name=monto]').val();
                  if (isNaN(parseFloat(_data.monto))) {
                    return K.notification({
                      title: ciHelper.titleMessages.infoReq,
                      text: 'El monto indicado en el item' + (i + 1) + ' no es un valor numerico valido',
                      type: 'error'
                    });
                  } else {
                    _data.monto = parseFloat(_data.monto);
                  }
                  if ($row.find('input').length == 2) {
                    __value__ = $row.find('input').eq(0).val();
                    _data.__value__ = __value__;
                  }
                }
                data.conceptos.push(_data);
              }
              break;
          }

          if (monto_mora_inmuebles > 0) {
            var subitem = {
              codigo: '',
              //descr: 'MORAS',
              descr: 'SANCIONES',
              cant: 1,
              cod_unidad: 'ZZ',
              unidad: 'SERVICIO',
              valor_unitario: K.round(monto_mora_inmuebles, 2),
              tipo_igv: '30',
              igv: 0,
              precio_venta_unitario: monto_mora_inmuebles,
              precio_total: monto_mora_inmuebles,
              tipo: 'subitem'
            };
            data.subitems.push(subitem);
          }
          //console.log('========================SAVE=======================');
          //console.log(data);
          //return false;
          if (p.moneda != null) {
            if (p.moneda == 'S' || p.moneda == 'PEN') {
              data_header.moneda = 'PEN';
            } else if (p.moneda == 'D' || p.moneda == 'USD') {
              data_header.moneda = 'USD';
            }
          }

          p.callback(data, data_header);
          K.closeWindow(p.$w.attr('id'));
        },
        "Cancelar": function() {
          K.closeWindow(p.$w.attr('id'));
        }
      },
      onContentLoaded: function() {
        p.$w = $('#windowRowConfiguration');
        K.unblock();
        if (p.data_header != null) {
          if (p.data_header.tipo == 'R' || p.data_header.tipo == 'RC' || p.data_header.tipo == 'RD') {
            p.$w.find('[name=tipo_igv]').find('[value=10]').attr('disabled', 'disabled');
            p.$w.find('[name=tipo_igv]').val("30");
          }
        }
        if (p.data != null) {
          //console.log("data viewConfig****="+JSON.stringify(p.data));
          //console.log(p.data_header);

          if (p.data.tipo == 'pago_meses' || p.data.tipo == 'pago_parcial' || p.data.tipo == 'pago_acta' || p.data.tipo == 'agua_chapi' || p.data.tipo == 'farmacia') {
            if (p.data.conceptos != null) {
              if (p.data.conceptos.length > 0) {
                for (var i = 0; i < p.data.conceptos.length; i++) {
                  var parent = '';
                  if (p.data.conceptos[i].parent != null) parent = 'parent="' + p.data.conceptos[i].parent + '"';
                  if (p.data.tipo == 'pago_meses' || p.data.tipo == 'pago_parcial') {
                    var $row = $('<tr class="item" item="' + p.data.conceptos[i].item + '" ' + parent + '>');
                    $row.append('<td><span name="item">' + p.data.conceptos[i].item + '</span></td>');
                    $row.append('<td><span name="descripcion">' + p.data.conceptos[i].descr + '</span></td>');
                    if (p.data.conceptos[i].porcentaje_mora != null) {
                      $row.append('<td><input type="text" name="mora" class="form-control" value="' + p.data.conceptos[i].porcentaje_mora + '" data-mes="' + p.data.conceptos[i].mes_n + '" data-ano="' + p.data.conceptos[i].ano_n + '"/>%</td>');
                      $row.find('[name=mora]').keyup(function() {
                        p.calcTot();
                      });
                    } else {
                      $row.append('<td>');
                    }
                    $row.append('<td><input type="text" name="monto" class="form-control" value="' + p.data.conceptos[i].monto + '" disabled></td>');
                    $row.find('[name=monto]').keyup(function() {
                      p.calcTot();
                    });
                    if (p.data.tipo == 'pago_parcial') {
                      $row.find('[name=monto]').removeAttr('disabled', 'disabled');
                    }
                    $row.data('data', p.data.conceptos[i]);
                    p.$w.find('[name=grid] tbody').append($row);
                  } else if (p.data.tipo == 'pago_acta') {
                    var $row = $('<tr class="item" item="' + p.data.conceptos[i].item + '" ' + parent + '>');
                    $row.append('<td><span name="item">' + p.data.conceptos[i].item + '</span></td>');
                    $row.append('<td><span name="descripcion">' + p.data.conceptos[i].descr + '</span></td>');
                    if (p.data.conceptos[i].porcentaje_mora != null) {
                      $row.append('<td><input type="text" name="mora" class="form-control" value="' + p.data.conceptos[i].porcentaje_mora + '" data-mes="' + p.data.conceptos[i].mes_n + '" data-ano="' + p.data.conceptos[i].ano_n + '"/>%</td>');
                      $row.find('[name=mora]').keyup(function() {
                        p.calcTot();
                      });
                    } else {
                      $row.append('<td />');
                    }
                    $row.append('<td><input type="text" name="monto" class="form-control" value="' + p.data.conceptos[i].monto + '" disabled></td>');
                    $row.data('data', p.data.conceptos[i]);
                    p.$w.find('[name=grid] tbody').append($row);
                  } else if (p.data.tipo == 'servicio') {


                  } else if (p.data.tipo == 'agua_chapi') {
                    var $row = $('<tr class="item" item="' + p.data.conceptos[i].item + '" ' + parent + '>');
                    $row.append('<td><span name="item">' + p.item + '</span></td>');
                    $row.append('<td><span name="descripcion">' + p.data.conceptos[i].descr + '</span></td>');
                    if (p.data.conceptos[i].producto != null) {
                      $row.append('<td>Cant: <input type="text" name="cant_item" value="' + p.data.conceptos[i].cant + '"><br>Valor unitario<input type="text" name="valor_unitario_item" value="' + p.data.conceptos[i].valor_unitario + '"></td>');
                    } else {
                      $row.append('<td>');
                    }
                    $row.append('<td><input type="text" name="monto" class="form-control" value="' + p.data.conceptos[i].monto + '" disabled></td>');
                    if (p.data.conceptos[i].producto != null) {
                      $row.find('[name=valor_unitario_item],[name=cant_item]').keyup(function() {
                        p.calcTot();
                      });
                    }
                    $row.data('data', p.data.conceptos[i]);
                    p.$w.find('[name=grid] tbody').append($row);
                  } else if (p.data.tipo == 'farmacia') {
                    var $row = $('<tr class="item" item="' + p.data.conceptos[i].item + '" ' + parent + '>');
                    $row.append('<td><span name="item">' + p.item + '</span></td>');
                    $row.append('<td><span name="descripcion">' + p.data.conceptos[i].descr + '</span></td>');
                    if (p.data.conceptos[i].producto != null) {
                      $row.append('<td>Cant: <input type="text" name="cant_item" value="' + p.data.conceptos[i].cant + '"><br>Valor unitario<input type="text" name="valor_unitario_item" value="' + p.data.conceptos[i].valor_unitario + '"></td>');
                    } else {
                      $row.append('<td>');
                    }
                    //POR AHORA SE DESBLOQUEARA EL CAMPO DE MONTO
                    $row.append('<td><input type="text" name="monto" class="form-control" value="' + p.data.conceptos[i].monto + '" disabled></td>');
                    if (p.data.conceptos[i].producto != null) {
                      $row.find('[name=valor_unitario_item],[name=cant_item]').keyup(function() {
                        p.calcTot();
                      });
                    }
                    $row.data('data', p.data.conceptos[i]);
                    p.$w.find('[name=grid] tbody').append($row);
                  }
                }
              }
            }
          } else if (p.data.tipo == 'servicio') {

            if (p.data.conceptos != null) {
              if (p.data.conceptos.length > 0) {
                var id_servicio = p.data.conceptos[0].servicio._id;
                if (id_servicio.$id != null) id_servicio = id_servicio.$id;
                K.block();
                $.post('cj/conc/get_serv', 'id=' + id_servicio, function(concs) {
                  if (concs.serv == null) {
                    return K.notification({
                      title: 'Servicio inv&aacute;lido',
                      text: 'El servicio seleccionado no tiene conceptos asociados!',
                      type: 'error'
                    });
                  }
                  var variables, SERV = {},
                    __VALUE__ = 0,
                    cuotas = 0;
                  SERV = {
                    SALDO: 0,
                    FECVEN: 0,
                    CM_PREC_PERP: 0,
                    CM_PREC_TEMP: 0,
                    CM_PREC_VIDA: 0,
                    CM_ACCE_PREC: 0,
                    CM_TIPO_ESPA: 0
                  };
                  /*var dateString = p.$w.find('[name=fecven]').val();
                  var date_fecven = new Date(dateString.substring(0,4), (dateString.substring(5,7))-1, dateString.substring(8,10));
                  SERV.FECVEN = ciHelper.date.diffDays(new Date(),date_fecven);
                  if(SERV.FECVEN<0) SERV.FECVEN = 0;*/
                  for (var i = 0, j = concs.vars.length; i < j; i++) {
                    try {
                      if (concs.vars[i].valor == 'true') eval('var ' + concs.vars[i].cod + ' = true;');
                      else if (concs.vars[i].valor == 'false') eval('var ' + concs.vars[i].cod + ' = false;');
                      else eval('var ' + concs.vars[i].cod + ' = ' + concs.vars[i].valor + ';');
                    } catch (e) {
                      console.warn('error en carga de variables');
                    }
                  }
                  for (var i = 0; i < p.data.conceptos.length; i++) {
                    var parent = '';
                    if (p.data.conceptos[i].parent != null) parent = 'parent="' + p.data.conceptos[i].parent + '"';
                    if (p.data.conceptos[i]._id != null) {
                      if (p.data.conceptos[i]._id.$id != null) p.data.conceptos[i]._id = p.data.conceptos[i]._id.$id;
                      if (p.data.conceptos[i].servicio._id.$id != null) p.data.conceptos[i].servicio._id = p.data.conceptos[i].servicio._id.$id

                      var monto = eval(p.data.conceptos[i].formula);
                      var $row = $('<tr class="item" item="' + p.data.conceptos[i].item + '" name="' + p.data.conceptos[i]._id + '">');
                      $row.append('<td><span name="item">' + p.data.conceptos[i].item + '</span></td>');
                      $row.append('<td><span name="descripcion">' + p.data.conceptos[i].descr + '</span></td>');
                      if (p.data.conceptos[i].formula.indexOf('__VALUE__') != -1) {
                        eval('var ' + concs.serv[i].cod + ' = ' + p.data.conceptos[i].__value__ + ';');
                        var formula = concs.serv[i].formula;
                        formula = ciHelper.string.replaceAll(formula, "__VALUE__", "__VALUE" + p.data.conceptos[i].cod + "__");
                        $row.append('<td><input type="text" size="7" name="codform' + p.data.conceptos[i].cod + '" value="' + p.data.conceptos[i].__value__ + '"></td>');
                        $row.find('[name^=codform]').val(p.data.conceptos[i].__value__).change(function() {
                          var val = parseFloat($(this).val()),
                            formula = $(this).data('form'),
                            cod = $(this).data('cod'),
                            $row = $(this).closest('.item');
                          eval("var __VALUE" + cod + "__ = " + val + ";");
                          var monto = eval(formula);
                          $row.find('[name=monto]').val(K.round(monto, 2));
                          eval("var " + cod + " = " + monto + ";");
                          $row.data('monto', monto);
                          for (var ii = 0, jj = concs.serv.length; ii < jj; ii++) {
                            var $table = p.$w.find('[name=grid]'),
                              $row = $table.find('tbody .item').eq(ii),
                              $cell = $row.find('[name=monto]'),
                              monto = eval($cell.data('formula'));
                            if ($cell.data('formula') != null) {
                              $cell.val(K.round(monto, 2));
                              $row.data('monto', monto);
                            }
                          }
                          p.calcTot();
                        }).data('form', formula).data('cod', p.data.conceptos[i].cod);
                        $row.append('<td><input type="text" name="monto" class="form-control" value="' + p.data.conceptos[i].monto + '" disabled></td>');
                      } else {
                        $row.append('<td>');
                        $row.append('<td><input type="text" name="monto" class="form-control" value="' + p.data.conceptos[i].monto + '" disabled></td>');
                        $row.find('[name=monto]').data('formula', p.data.conceptos[i].formula);
                      }
                      $row.data('monto', p.data.conceptos[i].monto);
                      $row.find('[name=monto]').val(K.round(p.data.conceptos[i].monto, 2));
                      $row.data('data', p.data.conceptos[i]);
                      p.$w.find('[name=grid] tbody').append($row);
                    } else {
                      var $row = $('<tr class="item" item="' + p.data.conceptos[i].item + '" ' + parent + '>');
                      $row.append('<td><span name="item">' + p.data.conceptos[i].item + '</span></td>');
                      $row.append('<td><span name="descripcion">' + p.data.conceptos[i].descr + '</span></td>');
                      $row.append('<td />');
                      $row.append('<td><input type="text" name="monto" class="form-control" value="' + p.data.conceptos[i].monto + '" disabled></td>');
                      $row.data('data', p.data.conceptos[i]);
                      p.$w.find('[name=grid] tbody').append($row);
                    }
                  }
                  K.unblock();
                }, 'json');
              }
            }
          }

          p.$w.find('[name=descripcion_item]').val(p.data.descr);
          p.$w.find('[name=cantidad]').val(p.data.cant);
          p.$w.find('[name=unidad]').val(p.data.cod_unidad);
          p.$w.find('[name=valor_unitario]').val(p.data.valor_unitario);
          p.$w.find('[name=tipo_igv]').val(p.data.tipo_igv);
          p.$w.find('[name=igv]').val(p.data.igv);
          p.$w.find('[name=precio_venta_unitario]').val(p.data.precio_venta_unitario);
          p.$w.find('[name=precio_total]').val(p.data.precio_total);
          p.tipo = p.data.tipo;
          if (p.data.tipo == 'pago_acta') {
            p.$w.find('[name=descripcion_item]').val("");
            p.calcTot();
          }
        }
        p.$w.find('[name=btnAlquiler]').click(function() {
          inMovi.windowMovi({
            callback: function(data) {

              p.$w.find('[name=grid] tbody').empty();
              p.moneda = data.contrato.moneda;
              p.renta = data.contrato.importe;
              console.log(data);
              if (data.pago != null) {
                data.pagos = [];
                data.pagos.push(data.pago);
              }
              K.block();
              $.post('in/movi/get_var_mes', {
                tipo: data.contrato.inmueble.tipo._id.$id,
                dia_ini: parseInt(ciHelper.date.format.bd_d(data.contrato.fecini)),
                pagos: data.pagos
              }, function(config) {
                p.tipo = data.tipo;
                p.config.calf = config.calf;
                p.config.cuenta = config.cuenta;
                p.config.cuenta_dudosa = config.conf.COBRANZA;
                p.config.conf = config.conf;
                if (data.tipo == 'pago_acta') {
                  if (data.contrato != null) {
                    if (data.contrato.arrendatario != null) {
                      p.cliente = data.contrato.arrendatario;
                    }
                  }
                  if (data.pagos != null) {
                    if (data.pagos.length > 0) {
                      for (var i = 0; i < data.pagos.length; i++) {
                        for (var j = 0; j < data.pagos[i].conceptos.length; j++) {
                          var texto_pago = data.pagos[i].conceptos[j].descr;
                          var _data = {
                            item: p.item_count,
                            descr: data.pagos[i].conceptos[j].descr,
                            monto: parseFloat(data.pagos[i].conceptos[j].monto),
                            acta_conciliacion: data.contrato._id.$id,
                            pago: {
                              num: data.pagos[i].num,
                              total: data.pagos[i].total,
                              ano: data.pagos[i].ano,
                            }
                          };
                          if (data.pagos[i].conceptos[j].tipo == 'A') {
                            _data.cuenta = {
                              _id: p.config.cuenta._id.$id,
                              descr: p.config.cuenta.descr,
                              cod: p.config.cuenta.cod
                            };
                          } else if (data.pagos[i].conceptos[j].tipo == 'I') {
                            _data.cuenta = {
                              _id: p.config.conf.IGV._id.$id,
                              descr: p.config.conf.IGV.descr,
                              cod: p.config.conf.IGV.cod
                            };
                            _data.igv = 1;
                          } else if (data.pagos[i].conceptos[j].tipo == 'M') {
                            _data.cuenta = {
                              _id: p.config.conf.MOR._id.$id,
                              descr: p.config.conf.MOR.descr,
                              cod: p.config.conf.MOR.cod
                            };
                            _data.monto_mora = 1;
                          }
                          if (_data.cuenta._id.$id != null) _data.cuenta._id = _data.cuenta._id.$id;
                          var $row = $('<tr class="item" item="' + p.item_count + '">');
                          $row.append('<td><span name="item">' + p.item_count + '</span></td>');
                          $row.append('<td><span name="descripcion">' + texto_pago.toUpperCase() + '</span></td>');
                          $row.append('<td>');
                          $row.append('<td><input type="text" name="monto" class="form-control" value="' + _data.monto + '" disabled></td>');
                          $row.data('data', _data);
                          p.$w.find('[name=grid] tbody').append($row);
                          p.item_count++;

                        }

                        /*MORA POR INCUMPLIMIENTO*/
                        var _data3 = {
                          item: p.item_count,
                          descr: 'MORAS POR INCUMPLIMIENTO',
                          monto: 0,
                          parent: -1,
                          porcentaje_mora: 0,
                          mes_n: null,
                          ano_n: null,
                          cuenta: {
                            _id: p.config.conf.MOR._id.$id,
                            descr: p.config.conf.MOR.descr,
                            cod: p.config.conf.MOR.cod
                          }
                        };

                        var dia_ini_tmp = ciHelper.date.format.bd_d(data.pagos[i].fecven);
                        var porc = 0,
                          mes_n = ciHelper.date.format.bd_m(data.pagos[i].fecven),
                          ano_n = ciHelper.date.format.bd_y(data.pagos[i].fecven);
                        if (parseInt(dia_ini_tmp) != 1) {
                          var mes_n = parseInt(mes_n) - 1,
                            ano_n = parseInt(ano_n);
                          if (mes_n == 0) {
                            mes_n = 12;
                            ano_n--;
                          }
                        }
                        _data3.mes_n = mes_n;
                        _data3.ano_n = ano_n;

                        var $row3 = $('<tr class="item" item="' + (p.item_count) + '" parent="-1">');
                        $row3.append('<td><span name="item">' + (p.item_count) + '</span></td>');
                        $row3.append('<td><span name="descripcion">MORAS</span></td>');
                        $row3.append('<td><input type="text" name="mora" class="form-control" value="' + porc + '" data-mes="' + mes_n + '" data-ano="' + ano_n + '"/>%</td>');
                        $row3.append('<td><input type="text" name="monto" class="form-control" value="' + (p.renta * (porc / 100)) + '"></td>');
                        $row3.find('[name=mora]').val(porc).keyup(function() {
                          p.calcTot();
                        });
                        $row3.data('data', _data3);
                        $.post('in/movi/get_mora', {
                          mes: mes_n,
                          ano: ano_n,
                          fecini: ciHelper.date.format.bd_ymd(data.pagos[i].fecven)
                        }, function(porc) {
                          $row3.find('[name=mora][data-mes=' + porc.mes + '][data-ano=' + porc.ano + ']').val(K.round(porc.mora, 2));
                          p.calcTot();
                          K.unblock();
                        }, 'json');
                        p.$w.find('[name=grid] tbody').append($row3);
                      }
                    }
                  }
                  p.calcTot();
                } else {
                  if (data.contrato != null) {
                    if (data.contrato.titular != null) {
                      p.cliente = data.contrato.titular;
                    }
                  }
                  if (data.pagos != null) {
                    if (data.pagos.length > 0) {
                      for (var i = 0, j = data.pagos.length; i < j; i++) {
                        var texto_pago = '--';
                        if (p.tipo == 'pago_meses') {
                          var texto_pago = inMovi.get_motivo(data.contrato.motivo._id.$id);
                          if (p.dias != null) {
                            texto_pago += ' de ' +
                              ciHelper.date.format.bd_ymd(data.contrato.fecini) + ' al ' + ciHelper.date.format.bd_ymd(data.contrato.fecdes);
                            p.renta = (p.renta / 30) * (1 + parseInt(ciHelper.dateDifference(data.contrato.fecdes, data.contrato.fecini)));
                          } else {
                            var dia_ini_tmp = ciHelper.date.format.bd_d(data.contrato.fecini);
                            p.dia_ini = dia_ini_tmp;
                            if (parseInt(dia_ini_tmp) == 1) {
                              texto_pago += ' de ' +
                                ciHelper.meses[parseInt(data.pagos[i].mes) - 1] + ' - ' + data.pagos[i].ano;
                            } else {
                              var mes_n = parseInt(data.pagos[i].mes) - 1,
                                ano_n = parseInt(data.pagos[i].ano);
                              mes_n--;
                              if (mes_n == -1) {
                                mes_n = 11;
                                ano_n--;
                              }
                              var tmp_ini = 'Del ' + dia_ini_tmp + ' de ' + ciHelper.meses[mes_n] + '-' + ano_n,
                                tmp_fin = 'Al ' + (parseInt(dia_ini_tmp) - 1) + ' de ' + ciHelper.meses[parseInt(data.pagos[i].mes) - 1] + '-' + data.pagos[i].ano;
                              //$row.append('<td>'+tmp_ini+'<br />'+tmp_fin+'</td>');
                              texto_pago += ' ' +
                                tmp_ini + ' ' + tmp_fin;
                            }
                          }
                        } else if (p.tipo == 'pago_parcial') {
                          texto_pago = 'Pago Parcial de Alquiler correspondiente a ' + ciHelper.meses[data.pagos[i].mes - 1] + ' - ' + data.pagos[i].ano + ' de ' + data.contrato.inmueble.direccion + ' (Cuota ' + (parseInt(data.pagos[i].item) + 1) + ')';
                        }
                        //console.log("data contato="+JSON.stringify(data.contrato.contrato_dias));
                        if(data.contrato.contrato_dias=="1")
                        {
                          var day_start = ciHelper.date.format.bd_d(data.contrato.fecini),
                              month_start = ciHelper.date.format.bd_m(data.contrato.fecini),
                              year_start = ciHelper.date.format.bd_y(data.contrato.fecini),
                              day_end = ciHelper.date.format.bd_d(data.contrato.fecfin),
                              month_end = ciHelper.date.format.bd_m(data.contrato.fecfin),
                              year_end = ciHelper.date.format.bd_y(data.contrato.fecfin);
												var tmp_ini = 'Del ' + day_start + ' de ' + ciHelper.meses[parseInt(month_start)-1] + ' del ' + year_start,
                            tmp_fin = 'Al ' + day_end + ' de ' + ciHelper.meses[parseInt(month_end)-1] + ' del ' + year_end;
                        texto_pago = 'Pago Alquiler por días, '+tmp_ini + ' - '+tmp_fin;
                        }

                        var _data = {
                          item: p.item_count,
                          cuenta: p.config.cuenta,
                          descr: texto_pago,
                          monto: p.renta,
                          alquiler: {
                            contrato: data.contrato._id.$id,
                          },
                          pago: {
                            item: data.pagos[i].item,
                            mes: data.pagos[i].mes,
                            ano: data.pagos[i].ano,
                          }
                        };
                        if (inMovi.get_cobranza_dudosa(data.pagos[i].mes, data.pagos[i].ano) == true) {
                          _data.cuenta = p.config.cuenta_dudosa;
                        }
                        if (_data.cuenta._id.$id != null) _data.cuenta._id = _data.cuenta._id.$id;
                        var $row = $('<tr class="item" item="' + p.item_count + '">');
                        $row.append('<td><span name="item">' + p.item_count + '</span></td>');
                        $row.append('<td><span name="descripcion">' + texto_pago.toUpperCase() + '</span></td>');
                        $row.append('<td>');
                        $row.append('<td><input type="text" name="monto" class="form-control" value="' + p.renta + '" disabled></td>');
                        $row.find('[name=monto]').keyup(function() {
                          p.calcTot();
                        });
                        if (p.tipo == 'pago_parcial') {
                          $row.find('[name=monto]').removeAttr('disabled', 'disabled');
                        }

                        $row.data('data', _data);
                        p.$w.find('[name=grid] tbody').append($row);

                        var _data2 = {
                          item: p.item_count + 1,
                          descr: 'IGV',
                          monto: p.config.igv * p.renta,
                          alquiler: {
                            contrato: data.contrato._id.$id,
                          },
                          igv: 1,
                          parent: p.item_count,
                          cuenta: {
                            _id: p.config.conf.IGV._id.$id,
                            descr: p.config.conf.IGV.descr,
                            cod: p.config.conf.IGV.cod
                          }
                        };
                        var $row2 = $('<tr class="item" item="' + (p.item_count + 1) + '" parent="' + (p.item_count) + '">');
                        $row2.append('<td><span name="item">' + (p.item_count + 1) + '</span></td>');
                        $row2.append('<td><span name="descripcion">IGV</span></td>');
                        $row2.append('<td>');
                        $row2.append('<td><input type="text" name="monto" class="form-control" value="' + (p.config.igv * p.renta) + '" disabled></td>');
                        $row2.find('[name=monto]').keyup(function() {
                          p.calcTot();
                        });
                        $row2.data('data', _data2);
                        p.$w.find('[name=grid] tbody').append($row2);
                        if (data.contrato.con_mora == '1') {
                          /************************************************************************************************
                           * HALLAMOS LA MORA
                           ************************************************************************************************/
                          //porc = inMovi.calculoMora(p,i);
                          var _data3 = {
                            item: p.item_count + 2,
                            //descr: 'MORAS',
                            descr: 'SANCIONES',
                            monto: 0,
                            alquiler: {
                              contrato: data.contrato._id.$id,
                            },
                            parent: p.item_count,
                            porcentaje_mora: 0,
                            mes_n: null,
                            ano_n: null,
                            cuenta: {
                              _id: p.config.conf.MOR._id.$id,
                              descr: p.config.conf.MOR.descr,
                              cod: p.config.conf.MOR.cod
                            }
                          };
                          var porc = 0,
                            mes_n = data.pagos[i].mes,
                            ano_n = data.pagos[i].ano;
                          if (parseInt(dia_ini_tmp) != 1) {
                            var mes_n = parseInt(data.pagos[i].mes) - 1,
                              ano_n = parseInt(data.pagos[i].ano);
                            if (mes_n == 0) {
                              mes_n = 12;
                              ano_n--;
                            }
                          };
                          _data3.mes_n = mes_n;
                          _data3.ano_n = ano_n;
                          $.post('in/movi/get_mora', {
                            mes: mes_n,
                            ano: ano_n,
                            fecini: ciHelper.date.format.bd_ymd(data.contrato.fecini)
                          }, function(porc) {
                            p.$w.find('[name=mora][data-mes=' + porc.mes + '][data-ano=' + porc.ano + ']').val(K.round(porc.mora, 2));
                            p.calcTot();
                            K.unblock();
                          }, 'json');
                          var $row3 = $('<tr class="item" item="' + (p.item_count + 2) + '" parent="' + (p.item_count) + '">');
                          $row3.append('<td><span name="item">' + (p.item_count + 2) + '</span></td>');
                          //$row3.append('<td><span name="descripcion">MORAS</span></td>');
                          $row3.append('<td><span name="descripcion">SANCIONES</span></td>');
                          $row3.append('<td><input type="text" name="mora" class="form-control" value="' + porc + '" data-mes="' + mes_n + '" data-ano="' + ano_n + '"/>%</td>');
                          $row3.append('<td><input type="text" name="monto" class="form-control" value="' + (p.renta * (porc / 100)) + '"></td>');
                          $row3.find('[name=mora]').val(porc).keyup(function() {
                            p.calcTot();
                          });
                          $row3.data('data', _data3);
                          p.$w.find('[name=grid] tbody').append($row3);
                          p.item_count += 2;
                        } else {
                          p.item_count += 1;
                          //K.unblock();
                        }
                        p.item_count++;
                        var dia_ini_tmp = ciHelper.date.format.bd_d(data.contrato.fecini);
                        /*if(parseInt(dia_ini_tmp)==1){
                        	//
                        }else{
                        	p.pagos[i].mes = parseInt(p.pagos[i].mes);
                        	p.pagos[i].mes++;
                        	if(p.pagos[i].mes==13){
                        		p.pagos[i].mes = 1;
                        		p.pagos[i].ano++;
                        	}
                        }*/
                      }
                    }
                    /*DESCRIPCION ITEM COMPROBANTE*/
                    var tmp_text_inmu = "INMUEBLE: ";
                    if (data.contrato.inmueble.tipo.nomb == 'EX NUEVO MILENIO') {
                      tmp_text_inmu = "TERRENO: ";
                    }
                    if (data.contrato.inmueble.tipo.nomb == 'LA CHOCITA') {
                      tmp_text_inmu = "ESPACIO: ";
                    }
                    if (data.contrato.inmueble.tipo.nomb == 'VARIOS') {
                      tmp_text_inmu = "POR OCUPACION ";
                    }
                    //p.$w.find('[name=texto_inmueble]').val(tmp_text_inmu+p.contrato.inmueble.direccion.toUpperCase()+' ('+p.contrato.inmueble.tipo.nomb.toUpperCase()+')');
                    var espacio = '';
                    if (data.contrato.inmueble != null) espacio = ', ' + tmp_text_inmu + data.contrato.inmueble.direccion;
                    if (data.pagos.length == 1) {
                      p.$w.find('[name=descripcion_item]').val(p.$w.find('[name=grid] tbody tr').eq(0).find('[name=descripcion]').html() + espacio);
                    } else {
                      var texto_pago = inMovi.get_motivo(data.contrato.motivo._id.$id) + ' DE ';
                      dia_ini_tmp = ciHelper.date.format.bd_d(data.contrato.fecini),
                        fin_pag = data.pagos.length - 1;
                      data.dia_ini = dia_ini_tmp;
                      if (parseInt(dia_ini_tmp) == 1) {
                        texto_pago += ciHelper.meses[parseInt(data.pagos[0].mes) - 1].toUpperCase() + ' - ' + data.pagos[0].ano;
                        texto_pago += ' A ';
                        texto_pago += ciHelper.meses[parseInt(data.pagos[fin_pag].mes) - 1].toUpperCase() + ' - ' + data.pagos[fin_pag].ano;
                      } else {
                        var mes_n = parseInt(data.pagos[0].mes) - 1,
                          ano_n = parseInt(data.pagos[0].ano);
                        mes_n--;
                        if (mes_n == -1) {
                          mes_n = 11;
                          ano_n--;
                        }
                        var tmp_ini = dia_ini_tmp + ' DE ' + ciHelper.meses[mes_n].toUpperCase() + '-' + ano_n,
                          tmp_fin = 'AL ' + (parseInt(dia_ini_tmp) - 1) + ' DE ' + ciHelper.meses[parseInt(data.pagos[fin_pag].mes) - 1].toUpperCase() + '-' + data.pagos[fin_pag].ano;
                        texto_pago += tmp_ini + ' ' + tmp_fin;
                      }
                      //if(p.$w.find('[name=comp] option:selected').val()=='F'){
                      texto_pago += ' C/M ' + K.round(p.renta, 2);
                      /*}else{
                      	texto_pago += ' C/M '+K.round( p.renta+(p.config.igv)*p.renta ,2);
                      }*/
                      p.$w.find('[name=descripcion_item]').val(texto_pago + espacio);
                    }
                    /*./DESCRIPCION ITEM COMPROBANTE*/
                  } else if (data.cuenta_cobrar != null) {
                    p.tipo = 'cuenta_cobrar';
                    var $row = $('<tr class="item" name="serv">');
                    $row.append('<td>');
                    //$row.append('<td>'+p.cobro.servicio.nomb+' de '+p.cobro.inmueble.direccion+'</td>');
                    //Aqui hay un problema con el servicio, primero un cosole.log
                    var texto_pago = data.cuenta_cobrar.servicio.nomb + ' de ' + data.cuenta_cobrar.inmueble.direccion;
                    if (data.cuenta_cobrar.observ != null) {
                      console.log("sospecha de p.cobro");
                      console.log(p);
                      console.log(data);
                      console.log(texto_pago);
                      if (data.cuenta_cobrar.observ != '') {
                        console.log("En el texto de pago se usara la observacion de la cuenta por cobrar");
                        var texto_pago = data.cuenta_cobrar.observ;
                        //p.$w.find('[name=observ]').val(data.cuenta_cobrar.observ);
                      } else {
                        if (p.cobro != null) {
                          if (p.cobro.observ != null) {
                            console.log("En el texto de pago se usara la observacion de p.cobro");
                            var texto_pago = data.p.cobro.observ;
                          } else {
                            console.log("de lo contrario se utilizara de nuevo el texto_pago original a pesar de que existe p.cobro");
                            var texto_pago = data.cuenta_cobrar.servicio.nomb + ' de ' + data.cuenta_cobrar.inmueble.direccion;

                          }
                          console.log("En el texto de pago se usara la observacion de p.cobro");
                          var texto_pago = data.p.cobro.observ;
                        } else {
                          console.log("de lo contrario se utilizara de nuevo el texto_pago original");
                          var texto_pago = data.cuenta_cobrar.servicio.nomb + ' de ' + data.cuenta_cobrar.inmueble.direccion;
                        }
                      };
                      console.log("texto_pago:" + texto_pago);
                    } else {
                      var texto_pago = data.cuenta_cobrar.servicio.nomb + ' de ' + data.cuenta_cobrar.inmueble.direccion;
                    }
                    //var texto_pago = data.cuenta_cobrar.servicio.nomb+' de '+data.cuenta_cobrar.inmueble.direccion;
                    var tmp_tot = 0,
                      tmp_ult = 0,
                      tupa = false;
                    for (var i = 0, j = data.cuenta_cobrar.conceptos.length; i < j; i++) {
                      if (data.cuenta_cobrar.conceptos[i].concepto.cod.substring(0, 4) == 'TUPA') tupa = true;
                      var monto = 0;
                      data.cuenta_cobrar.conceptos[i].monto = parseFloat(data.cuenta_cobrar.conceptos[i].monto);
                      /*if(tupa==false){
                      	monto = data.cuenta_cobrar.conceptos[i].monto/(1+p.igv);
                      	tmp_tot += monto;
                      	tmp_ult += p.cuenta_cobrar.conceptos[i].monto-monto;
                      }else{
                      	monto = data.cuenta_cobrar.conceptos[i].monto;
                      	tmp_tot += monto;
                      	tmp_ult += monto;
                      }*/
                      monto = data.cuenta_cobrar.conceptos[i].monto;
                      tmp_tot += monto;
                      var _data = {
                        item: p.item_count,
                        cuenta: p.config.cuenta,
                        descr: texto_pago,
                        monto: monto,
                        cuenta_cobrar: {
                          _id: data.cuenta_cobrar._id.$id,
                          servicio: {
                            _id: data.cuenta_cobrar.servicio._id.$id,
                            nomb: data.cuenta_cobrar.servicio.nomb
                          }
                        }
                      };
                      if (_data.cuenta._id.$id != null) _data.cuenta._id = _data.cuenta._id.$id;
                      var $row = $('<tr class="item" item="' + p.item_count + '">');
                      $row.append('<td><span name="item">' + p.item_count + '</span></td>');
                      $row.append('<td><span name="descripcion">' + texto_pago.toUpperCase() + '</span></td>');
                      $row.append('<td>');
                      $row.append('<td><input type="text" name="monto" class="form-control" value="' + K.round(_data.monto, 2) + '" disabled></td>');
                      $row.data('data', _data);
                      p.$w.find('[name=grid] tbody').append($row);
                      p.item_count++;
                    }
                    var _data2 = {
                      item: p.item_count + 1,
                      descr: 'IGV',
                      monto: 0,
                      cuenta_cobrar: {
                        _id: data.cuenta_cobrar._id.$id,
                        servicio: {
                          _id: data.cuenta_cobrar.servicio._id.$id,
                          nomb: data.cuenta_cobrar.servicio.nomb
                        }
                      },
                      igv: 1,
                      parent: p.item_count,
                      cuenta: {
                        _id: p.config.conf.IGV._id.$id,
                        descr: p.config.conf.IGV.descr,
                        cod: p.config.conf.IGV.cod
                      }
                    };
                    var $row2 = $('<tr class="item" item="' + (p.item_count + 1) + '" parent="' + (p.item_count) + '">');
                    $row2.append('<td><span name="item">' + (p.item_count + 1) + '</span></td>');
                    $row2.append('<td><span name="descripcion">IGV</span></td>');
                    $row2.append('<td>');
                    $row2.append('<td><input type="text" name="monto" class="form-control" value="' + K.round(tmp_ult, 2) + '" disabled></td>');
                    $row2.data('data', _data2);
                    p.$w.find('[name=grid] tbody').append($row2);
                    p.$w.find('[name=descripcion_item]').val(texto_pago);
                    p.calcTot();
                  }
                }
                K.unblock();
              }, 'json');
            }
          });
        });
        /*-------------PLAYAS------------------*/
        p.$w.find('[name=btnPlaya]').click(function() {
          //console.log(p.config);
          lgProd.windowSelectProducto({
            //texto: texto,
            //stock: true,
            modulo: 'AG',
            almacen: p.config.almacen,
            callback: function(data) {
              p.$w.find('[name=grid] tbody').empty();
              $.post('ag/comp/get_var_comp', function(config) {
                p.tipo = 'agua_chapi';
                p.config.cuenta = config.cuenta;
                p.config.conf = config.conf;
                var _data = {
                  item: p.item_count,
                  cuenta: p.config.cuenta,
                  descr: data.nomb,
                  monto: K.round((data.precio_venta / 1.18), 2),
                  cant: 1,
                  valor_unitario: K.round((data.precio_venta / 1.18), 2),
                  producto: {
                    _id: data._id.$id,
                    cod: data.cod,
                    nomb: data.nomb,
                  }
                };
                if (_data.cuenta._id.$id != null) _data.cuenta._id = _data.cuenta._id.$id;
                data.precio_venta = parseFloat(data.precio_venta);
                var stock = 0;
                if (data.stock != null) {
                  stock = data.stock.stock;
                }
                var $row = $('<tr class="item" item="' + p.item_count + '">');
                $row.append('<td><span name="item">' + p.item_count + '</span></td>');
                $row.append('<td><span name="descripcion">' + _data.descr.toUpperCase() + '</span></td>');
                $row.append('<td>Cant: <input type="text" name="cant_item" value="1"><br>Valor unitario<input type="text" name="valor_unitario_item" value="' + K.round((data.precio_venta / 1.18), 2) + '"></td>');
                $row.append('<td><input type="text" name="monto" class="form-control" value="' + K.round((data.precio_venta / 1.18), 2) + '" disabled></td>');
                $row.find('[name=valor_unitario_item],[name=cant_item]').keyup(function() {
                  p.calcTot();
                });
                $row.data('data', _data);
                p.$w.find('[name=grid] tbody').append($row);

                var _data2 = {
                  item: p.item_count + 1,
                  descr: 'IGV',
                  monto: p.config.igv * p.renta,
                  igv: 1,
                  parent: p.item_count,
                  cuenta: {
                    _id: p.config.conf.IGV._id.$id,
                    descr: p.config.conf.IGV.descr,
                    cod: p.config.conf.IGV.cod
                  }
                };
                var $row2 = $('<tr class="item" item="' + (p.item_count + 1) + '" parent="' + (p.item_count) + '">');
                $row2.append('<td><span name="item">' + (p.item_count + 1) + '</span></td>');
                $row2.append('<td><span name="descripcion">IGV</span></td>');
                $row2.append('<td>');
                $row2.append('<td><input type="text" name="monto" class="form-control" value="' + (p.config.igv * (data.precio_venta / 1.18)) + '" disabled></td>');
                $row2.find('[name=monto]').keyup(function() {
                  p.calcTot();
                });
                $row2.data('data', _data2);
                p.$w.find('[name=grid] tbody').append($row2);

                p.$w.find('[name=descripcion_item]').val(_data.descr.toUpperCase());
                p.calcTot();
              }, 'json');
            }
          });
        });
        /*-------------AGUA-CHAPI--------------*/
        p.$w.find('[name=btnAgua]').click(function() {
          //console.log(p.config);
          lgProd.windowSelectProducto({
            //texto: texto,
            //stock: true,
            modulo: 'AG',
            almacen: p.config.almacen,
            callback: function(data) {
              p.$w.find('[name=grid] tbody').empty();
              $.post('ag/comp/get_var_comp', function(config) {
                p.tipo = 'agua_chapi';
                p.config.cuenta = config.cuenta;
                p.config.conf = config.conf;
                var _data = {
                  item: p.item_count,
                  cuenta: p.config.cuenta,
                  descr: data.nomb,
                  monto: K.round((data.precio_venta / 1.18), 2),
                  cant: 1,
                  valor_unitario: K.roundValorUnitario(data.precio_venta / 1.18, 10),
                  almacen: p.config.almacen,
                  producto: {
                    _id: data._id.$id,
                    cod: data.cod,
                    nomb: data.nomb,
                  }
                };
                /*var _data = {
                	item: p.item_count,
                	cuenta: p.config.cuenta,
                	descr: data.nomb,
                	monto: K.round((data.precio_venta/1.18),2),
                	cant: 1,
                	valor_unitario: K.round((data.precio_venta/1.18),2),
                	producto:{
                		_id:data._id.$id,
                		cod:data.cod,
                		nomb:data.nomb,
                	}
                };*/
                if (_data.cuenta._id.$id != null) _data.cuenta._id = _data.cuenta._id.$id;
                data.precio_venta = parseFloat(data.precio_venta);
                var stock = 0;
                if (data.stock != null) {
                  stock = data.stock.stock;
                }
                var $row = $('<tr class="item" item="' + p.item_count + '">');
                $row.append('<td><span name="item">' + p.item_count + '</span></td>');
                $row.append('<td><span name="descripcion">' + _data.descr.toUpperCase() + '</span></td>');

                //3ra columna EL VALOR UNITARIO ORIGINAL QUE SE NOS DEJO COMO LEGADO
                //$row.append('<td>Cant: <input type="text" name="cant_item" value="1"><br>Valor unitario<input type="text" name="valor_unitario_item" value="'+K.round((data.precio_venta/1.18),2)+'"></td>');
                //1:EL VALOR UNITARIO SIEMPRE ESTA DESBLOQUEADO (DIFERENCIA)
                $row.append('<td>Cant: <input type="text" name="cant_item" value="1"><br>Valor unitario<input type="text" name="valor_unitario_item" value="' + K.roundValorUnitario(_data.valor_unitario, 10) + '" disabled></td>');
                //2:EL VALOR UNITARIO SIEMPRE ESTA DESBLOQUEADO (DECIMALES)
                //$row.append('<td>Cant: <input type="text" name="cant_item" value="1"><br>Valor unitario<input type="text" name="valor_unitario_item" value="'+_data.valor_unitario+'"></td>');
                //3: MODIFICACION: AGREGADO DE VALOR UNITARIO CON DECIMALES DE RICARDO (POR AHORA NO)
                //$row.append('<td>Cant: <input type="text" name="cant_item" value="1"><br>Valor unitario<input type="text" name="valor_unitario_item" value="'+_data.valor_unitario+'"><br><div style="border:2px red solid; display:block"> Valor unitario (decimales)<input type="text"  name="valor_unitario_item_decimal" value="'+K.round(_data.valor_unitario,2)+'"></div></td>');

                //4ta columna: EL VALOR MONTO QUE SE NOS DEJO COMO LEGADO
                //$row.append('<td><input type="text" name="monto" class="form-control" value="'+K.round((data.precio_venta/1.18),2)+'" disabled></td>');
                //1:METODO DE DIFERENCIA
                //$row.append('<td><input type="text" name="monto" class="form-control" value="'+data.precio_venta/1.18+'" disabled></td>');
                $row.append('<td><input type="text" name="monto" class="form-control" value="' + K.round((data.precio_venta / 1.18), 2) + '" disabled></td>');
                //2:METODO DE DECIMALES
                //$row.append('<td><input type="text" name="monto" class="form-control" value="'+_data.monto+'" disabled></td>');
                $row.find('[name=valor_unitario_item],[name=cant_item]').keyup(function() {
                  p.calcTot();
                });
                $row.data('data', _data);
                p.$w.find('[name=grid] tbody').append($row);
                var _data2 = {
                  //prec_comp: K.round((data.precio_venta),2),
                  item: p.item_count + 1,
                  descr: 'IGV',
                  monto: p.config.igv * p.renta,
                  igv: 1,
                  parent: p.item_count,
                  cuenta: {
                    _id: p.config.conf.IGV._id.$id,
                    descr: p.config.conf.IGV.descr,
                    cod: p.config.conf.IGV.cod
                  }
                };
                var $row2 = $('<tr class="item" item="' + (p.item_count + 1) + '" parent="' + (p.item_count) + '">');
                //PARA EL METODO DE DIFERENCIA VAMOS A MANDAR EL PRECIO DE VENTA Y LA CANTIDAD
                $row2.data('diff_precio_venta', data.precio_venta);

                $row2.append('<td><span name="item">' + (p.item_count + 1) + '</span></td>');
                $row2.append('<td><span name="descripcion">IGV</span></td>');
                $row2.append('<td>');
                //$row2.append('<td><input type="text" name="monto" class="form-control" value="'+(p.config.igv*(_data.monto/1.18))+'" disabled></td>');
                //SE APLICA EL METODO DE LA DIFERENCIA
                $row2.append('<td><input type="text" name="monto" class="form-control" value="' + (data.precio_venta * 1 - _data.monto) + '" disabled></td>');
                $row2.find('[name=monto]').keyup(function() {
                  p.calcTot();
                });
                $row2.data('data', _data2);
                //EN CASO DE APLICAR EL DESCUENTO, INSERTARLO AQUI
                p.$w.find('[name=precio_total]').keyup(function() {
                  p.calcTotRegresion();
                });
                p.$w.find('[name=grid] tbody').append($row2);
                p.$w.find('[name=descripcion_item]').val(_data.descr.toUpperCase());
                p.calcTot();
              }, 'json');
            }
          });
        });

        p.$w.find('[name=btnFarmacia]').click(function() {
          lgProd.windowSelectProducto({
            //texto: texto,
            //stock: true,
            modulo: 'FA',
            almacen: p.config.almacen,
            callback: function(data) {
              p.$w.find('[name=grid] tbody').empty();
              $.post('fa/comp/get_var_comp', function(config) {

                p.tipo = 'farmacia';
                p.config.cuenta = config.cuenta;
                p.config.conf = config.conf;
                var _data = {
                  item: p.item_count,
                  cuenta: p.config.cuenta,
                  descr: data.nomb,
                  monto: K.round((data.precio_venta / 1.18), 2),
                  cant: 1,
                  valor_unitario: K.roundValorUnitario(data.precio_venta / 1.18, 10),
                  almacen: p.config.almacen,
                  producto: {
                    _id: data._id.$id,
                    cod: data.cod,
                    nomb: data.nomb,
                  }
                };
                /*var _data = {
                	item: p.item_count,
                	cuenta: p.config.cuenta,
                	descr: data.nomb,
                	monto: K.round((data.precio_venta/1.18),2),
                	cant: 1,
                	valor_unitario: K.round((data.precio_venta/1.18),2),
                	producto:{
                		_id:data._id.$id,
                		cod:data.cod,
                		nomb:data.nomb,
                	}
                };*/
                if (_data.cuenta._id.$id != null) _data.cuenta._id = _data.cuenta._id.$id;
                data.precio_venta = parseFloat(data.precio_venta);
                var stock = 0;
                if (data.stock != null) {
                  stock = data.stock.stock;
                }
                if (data.stock.stock <= 0) {
                  return K.notification({
                    title: ciHelper.titleMessages.infoReq,
                    text: 'No hay stock para realizar la venta',
                    type: 'error'
                  });
                }
                var $row = $('<tr class="item" item="' + p.item_count + '">');
                $row.append('<td><span name="item">' + p.item_count + '</span></td>');
                $row.append('<td><span name="descripcion">' + _data.descr.toUpperCase() + '</span></td>');

                //3ra columna EL VALOR UNITARIO ORIGINAL QUE SE NOS DEJO COMO LEGADO
                //$row.append('<td>Cant: <input type="text" name="cant_item" value="1"><br>Valor unitario<input type="text" name="valor_unitario_item" value="'+K.round((data.precio_venta/1.18),2)+'"></td>');
                //1:EL VALOR UNITARIO SIEMPRE ESTA DESBLOQUEADO (DIFERENCIA)
                $row.append('<td>Cant: <input type="text" name="cant_item" value="1"><br>Valor unitario<input type="text" name="valor_unitario_item" value="' + K.roundValorUnitario(_data.valor_unitario, 10) + '" disabled></td>');
                //2:EL VALOR UNITARIO SIEMPRE ESTA DESBLOQUEADO (DECIMALES)
                //$row.append('<td>Cant: <input type="text" name="cant_item" value="1"><br>Valor unitario<input type="text" name="valor_unitario_item" value="'+_data.valor_unitario+'"></td>');
                //3: MODIFICACION: AGREGADO DE VALOR UNITARIO CON DECIMALES DE RICARDO (POR AHORA NO)
                //$row.append('<td>Cant: <input type="text" name="cant_item" value="1"><br>Valor unitario<input type="text" name="valor_unitario_item" value="'+_data.valor_unitario+'"><br><div style="border:2px red solid; display:block"> Valor unitario (decimales)<input type="text"  name="valor_unitario_item_decimal" value="'+K.round(_data.valor_unitario,2)+'"></div></td>');

                //4ta columna: EL VALOR MONTO QUE SE NOS DEJO COMO LEGADO
                //$row.append('<td><input type="text" name="monto" class="form-control" value="'+K.round((data.precio_venta/1.18),2)+'" disabled></td>');
                //1:METODO DE DIFERENCIA
                //$row.append('<td><input type="text" name="monto" class="form-control" value="'+data.precio_venta/1.18+'" disabled></td>');
                $row.append('<td><input type="text" name="monto" class="form-control" value="' + K.round((data.precio_venta / 1.18), 2) + '" disabled></td>');
                //2:METODO DE DECIMALES
                //$row.append('<td><input type="text" name="monto" class="form-control" value="'+_data.monto+'" disabled></td>');
                //Almacena el stock maximo en la columna del concepto base
                $row.data('maxstock', data.stock.stock);
                $row.find('[name=valor_unitario_item],[name=cant_item]').keyup(function() {
                  p.calcTot();
                });
                $row.data('data', _data);
                p.$w.find('[name=grid] tbody').append($row);
                var _data2 = {
                  //prec_comp: K.round((data.precio_venta),2),
                  item: p.item_count + 1,
                  descr: 'IGV',
                  monto: p.config.igv * p.renta,
                  igv: 1,
                  parent: p.item_count,
                  cuenta: {
                    _id: p.config.conf.IGV._id.$id,
                    descr: p.config.conf.IGV.descr,
                    cod: p.config.conf.IGV.cod
                  }
                };
                var $row2 = $('<tr class="item" item="' + (p.item_count + 1) + '" parent="' + (p.item_count) + '">');
                //PARA EL METODO DE DIFERENCIA VAMOS A MANDAR EL PRECIO DE VENTA Y LA CANTIDAD
                $row2.data('diff_precio_venta', data.precio_venta);

                $row2.append('<td><span name="item">' + (p.item_count + 1) + '</span></td>');
                $row2.append('<td><span name="descripcion">IGV</span></td>');
                $row2.append('<td>');
                //$row2.append('<td><input type="text" name="monto" class="form-control" value="'+(p.config.igv*(_data.monto/1.18))+'" disabled></td>');
                //SE APLICA EL METODO DE LA DIFERENCIA
                $row2.append('<td><input type="text" name="monto" class="form-control" value="' + (data.precio_venta * 1 - _data.monto) + '" disabled></td>');
                $row2.find('[name=monto]').keyup(function() {
                  p.calcTot();
                });
                $row2.data('data', _data2);
                //EN CASO DE APLICAR EL DESCUENTO, INSERTARLO AQUI
                p.$w.find('[name=precio_total]').keyup(function() {
                  p.calcTotRegresion();
                });
                p.$w.find('[name=grid] tbody').append($row2);
                p.$w.find('[name=descripcion_item]').val(_data.descr.toUpperCase());
                p.calcTot();
              }, 'json');
            }
          });
        });

        /*  p.$w.find('[name=btnFarmacia]').click(function(){
        	lgProd.windowSelectProducto({
        		//texto: texto,
        		//stock: true,
        		modulo:'FA',
        		almacen: p.config.almacen,
        		callback: function(data){
        			p.$w.find('[name=grid] tbody').empty();
        			$.post('fa/comp/get_var_comp',function(config){
        				p.tipo = 'farmacia';
        				p.config.cuenta = config.cuenta;
        				p.config.conf = config.conf;
        				var _data = {
        					//Anadido por giancarlo
        					prec_comp: K.round((data.precio_venta),2),
        					item: p.item_count,
        					cuenta: p.config.cuenta,
        					descr: data.nomb,
        					monto: K.round((data.precio_venta/1.18),2),
        					cant: 1,
        					valor_unitario: K.round((data.precio_venta/1.18),2),
        					producto:{
        						_id:data._id.$id,
        						cod:data.cod,
        						nomb:data.nomb,
        					}
        				};
        				if(_data.cuenta._id.$id!=null) _data.cuenta._id = _data.cuenta._id.$id;
        				data.precio_venta = parseFloat(data.precio_venta);
        				var stock = 0;
        				if(data.stock!=null){
        					stock = data.stock.stock;
        				}
        				var $row = $('<tr class="item" item="'+p.item_count+'">');
        				$row.append('<td><span name="item">'+p.item_count+'</span></td>');
        				$row.append('<td><span name="descripcion">'+_data.descr.toUpperCase()+'</span></td>');
        				$row.append('<td>Cant: <input type="text" name="cant_item" value="1"><br>Valor unitario<input type="text" name="valor_unitario_item" value="'+K.round((data.precio_venta/1.18),2)+'"></td>');
        				$row.append('<td><input type="text" name="monto" class="form-control" value="'+K.round((data.precio_venta/1.18),2)+'" disabled></td>');
        				$row.find('[name=valor_unitario_item],[name=cant_item]').keyup(function(){
        					p.calcTot();
        				});
        				$row.data('data',_data);
        				p.$w.find('[name=grid] tbody').append($row);

        				var _data2 = {
        					//Anadido por giancarlo como parche
        					prec_comp: K.round((data.precio_venta),2),
        					item: p.item_count+1,
        					descr: 'IGV',
        					monto: p.config.igv*p.renta,
        					igv: 1,
        					parent: p.item_count,
        					cuenta: {
        						_id: p.config.conf.IGV._id.$id,
        						descr: p.config.conf.IGV.descr,
        						cod: p.config.conf.IGV.cod
        					}
        				};
        				var $row2 = $('<tr class="item" item="'+(p.item_count+1)+'" parent="'+(p.item_count)+'">');
        				$row2.append('<td><span name="item">'+(p.item_count+1)+'</span></td>');
        				$row2.append('<td><span name="descripcion">IGV</span></td>');
        				$row2.append('<td>');
        				$row2.append('<td><input type="text" name="monto" class="form-control" value="'+(p.config.igv*(data.precio_venta/1.18))+'" disabled></td>');
        				//Se actualiza al presionar en monto
        				$row2.find('[name=monto]').keyup(function(){
        					p.calcTot();
        				});
        				$row2.data('data',_data2);
        				p.$w.find('[name=grid] tbody').append($row2);
        				p.$w.find('[name=descripcion_item]').val(_data.descr.toUpperCase());
        				p.calcTot();
        			},'json');
        		}
        	});
        });
        */

        p.$w.find('[name=btnServicio]').click(function() {
          mgServ.windowSelect({
            callback: function(data) {
              p.$w.find('[name=grid] tbody').empty();
              $.post('cj/conc/get_serv', 'id=' + data._id.$id, function(concs) {
                if (concs.serv == null) {
                  return K.notification({
                    title: 'Servicio inv&aacute;lido',
                    text: 'El servicio seleccionado no tiene conceptos asociados!',
                    type: 'error'
                  });
                }
                $.post('fa/comp/get_var_comp', {}, function(config) {
                  p.tipo = 'servicio';
                  p.config.calf = config.calf;
                  p.config.cuenta = config.cuenta;
                  p.config.cuenta_dudosa = config.conf.COBRANZA;
                  p.config.conf = config.conf;
                  var variables, SERV = {},
                    __VALUE__ = 0,
                    cuotas = 0;
                  SERV = {
                    SALDO: 0,
                    FECVEN: 0,
                    CM_PREC_PERP: 0,
                    CM_PREC_TEMP: 0,
                    CM_PREC_VIDA: 0,
                    CM_ACCE_PREC: 0,
                    CM_TIPO_ESPA: 0
                  };
                  /*var dateString = p.$w.find('[name=fecven]').val();
                  var date_fecven = new Date(dateString.substring(0,4), (dateString.substring(5,7))-1, dateString.substring(8,10));
                  SERV.FECVEN = ciHelper.date.diffDays(new Date(),date_fecven);
                  if(SERV.FECVEN<0) SERV.FECVEN = 0;*/
                  for (var i = 0, j = concs.vars.length; i < j; i++) {
                    try {
                      if (concs.vars[i].valor == 'true') eval('var ' + concs.vars[i].cod + ' = true;');
                      else if (concs.vars[i].valor == 'false') eval('var ' + concs.vars[i].cod + ' = false;');
                      else eval('var ' + concs.vars[i].cod + ' = ' + concs.vars[i].valor + ';');
                    } catch (e) {
                      console.warn('error en carga de variables');
                    }
                  }
                  var texto_pago = '';
                  if (concs.serv != null) {
                    if (concs.serv.length > 0) {
                      texto_pago = concs.serv[0].nomb;
                      for (var i = 0; i < concs.serv.length; i++) {
                        var _data = {
                          _id: concs.serv[i]._id.$id,
                          item: p.item_count,
                          cuenta: concs.serv[i].cuenta,
                          descr: concs.serv[i].nomb,
                          monto: concs.serv[i].formula,
                          cod: concs.serv[i].cod,
                          formula: concs.serv[i].formula,
                          servicio: {
                            _id: data._id.$id,
                            nomb: data.nomb
                          }
                        };
                        if (_data.cuenta._id.$id != null) _data.cuenta._id = _data.cuenta._id.$id;
                        var monto = eval(concs.serv[i].formula);
                        var $row = $('<tr class="item" item="' + p.item_count + '" name="' + concs.serv[i]._id.$id + '">');
                        $row.append('<td><span name="item">' + p.item_count + '</span></td>');
                        $row.append('<td><span name="descripcion">' + _data.descr.toUpperCase() + '</span></td>');
                        if (concs.serv[i].formula.indexOf('__VALUE__') != -1) {
                          eval('var ' + concs.serv[i].cod + ' = 0;');
                          var formula = concs.serv[i].formula;
                          formula = ciHelper.string.replaceAll(formula, "__VALUE__", "__VALUE" + concs.serv[i].cod + "__");
                          $row.append('<td><input type="text" size="7" name="codform' + concs.serv[i].cod + '"></td>');
                          $row.find('[name^=codform]').val(0).change(function() {
                            var val = parseFloat($(this).val()),
                              formula = $(this).data('form'),
                              cod = $(this).data('cod'),
                              $row = $(this).closest('.item');
                            eval("var __VALUE" + cod + "__ = " + val + ";");
                            var monto = eval(formula);
                            $row.find('[name=monto]').val(K.round(monto, 2));
                            eval("var " + cod + " = " + monto + ";");
                            $row.data('monto', monto);
                            for (var ii = 0, jj = concs.serv.length; ii < jj; ii++) {
                              var $table = p.$w.find('[name=grid]'),
                                $row = $table.find('tbody .item').eq(ii),
                                $cell = $row.find('[name=monto]'),
                                monto = eval($cell.data('formula'));
                              if ($cell.data('formula') != null) {
                                $cell.val(K.round(monto, 2));
                                $row.data('monto', monto);
                              }
                            }
                            p.calcTot();
                          }).data('form', formula).data('cod', concs.serv[i].cod);
                          $row.append('<td><input type="text" name="monto" class="form-control" value="0.00" disabled></td>');
                        } else {
                          $row.append('<td>');
                          $row.append('<td><input type="text" name="monto" class="form-control" value="0.00" disabled></td>');
                          $row.data('monto', _data.monto);
                        }
                        $row.find('[name=monto]').val(K.round(_data.monto, 2));
                        $row.data('data', _data);
                        p.$w.find('[name=grid] tbody').append($row);
                        p.item_count++;
                      }
                      var _data2 = {
                        item: p.item_count,
                        descr: 'IGV',
                        //monto: 0,
                        monto: p.config.igv * _data.monto,
                        igv: 1,
                        parent: -1,
                        cuenta: {
                          _id: p.config.conf.IGV._id.$id,
                          descr: p.config.conf.IGV.descr,
                          cod: p.config.conf.IGV.cod
                        }
                      };
                      var $row2 = $('<tr class="item" item="' + (p.item_count) + '" parent="' + (_data2.parent) + '">');
                      $row2.append('<td><span name="item">' + (p.item_count) + '</span></td>');
                      $row2.append('<td><span name="descripcion">IGV</span></td>');
                      $row2.append('<td>');
                      $row2.append('<td><input type="text" name="monto" class="form-control" value="' + K.round(_data2.monto, 2) + ' disabled"></td>');
                      $row2.data('data', _data2);
                      p.$w.find('[name=grid] tbody').append($row2);
                      p.$w.find('[name=descripcion_item]').val(texto_pago.toUpperCase());
                      p.item_count++;
                      p.calcTot();
                    }
                  }

                }, 'json');
              }, 'json');
            },
            bootstrap: true,
            //modulo: 'IN'
          });
        });

        p.$w.find('[name=tipo_igv]').change(function() {
          p.calcTot();
        });
      }
    });
  },
  widnowConfirm: function(p) {
    new K.Panel({
      contentURL: 'cj/ecom/confirm_edit',
      store: false,
      buttons: {
        /*'Confirmar': {
        	icon: 'fa-save',
        	type: 'success',
        	f: function(){
        		K.block();
        		$.post('cj/ecom/confirmar',{_id:p.id},function(data){
        			K.notification({
        				title: 'Respuesta del Sistema',
        				text: data.message,
        				type: data.status
        			});
        			if(data.status=='success'){
        				//window.open(data.data.ruta_pdf);
        				window.open('http://35.193.115.148/'+data.data.ruta_pdf);
        				cjEcom.init();
        			}else{
        				K.unblock();
        			}
        		},'json');
        	}
        },*/
        'Confirmar y enviar SUNAT': {
          icon: 'fa-save',
          type: 'success',
          f: function() {
            K.block();
            $.post('cj/ecom/confirmar', {
              _id: p.id,
              enviar: 1
            }, function(data) {
              K.notification({
                title: 'Respuesta del Sistema',
                text: data.message,
                type: data.status
              });
              if (data.status == 'success') {
                window.open('http://35.193.115.148/' + data.data.ruta_pdf);
                cjEcom.init();
              } else {
                K.unblock();
              }
            }, 'json');
          }
        },
        'Cancelar': {
          icon: 'fa-ban',
          type: 'danger',
          f: function() {
            cjEcom.windowComprobante({
              id: p.id
            });
          }
        }
      },
      onContentLoaded: function() {
        p.$w = $('#mainPanel');
        p.$w.find('[name=iframe]').attr('src', 'cj/ecom/print_preview?_id=' + p.id);
        K.unblock();
        /*$.post('cj/ecom/get',{_id:p.id},function(){

        },'json');*/
      }
    });
  },
  windowSelectConcepto: function(p) {
    new K.Modal({
      id: 'windowSelect',
      content: '<div name="tmp"></div>',
      width: 750,
      height: 400,
      title: 'Seleccionar Concepto',
      buttons: {
        "Seleccionar": {
          icon: 'fa-check',
          type: 'info',
          f: function() {
            if (p.$w.find('.highlights').data('data') != null) {
              p.callback(p.$w.find('.highlights').data('data'));
              K.closeWindow(p.$w.attr('id'));
            } else {
              K.clearNoti();
              return K.notification({
                title: ciHelper.titleMessages.infoReq,
                text: 'Debe seleccionar un item!',
                type: 'error'
              });
            }
          }
        },
        "Cancelar": {
          icon: 'fa-ban',
          type: 'danger',
          f: function() {
            K.closeWindow(p.$w.attr('id'));
          }
        }
      },
      onClose: function() {
        p = null;
      },
      onContentLoaded: function() {
        p.$w = $('#windowSelect');
        p.$grid = new K.grid({
          $el: p.$w.find('[name=tmp]'),
          cols: ['', 'Nombre', 'Cuenta', 'IGV'],
          data: 'cj/conc/lista',
          params: {},
          itemdescr: 'concepto(s)',
          onLoading: function() {
            K.block({
              $element: p.$w
            });
          },
          onComplete: function() {
            K.unblock({
              $element: p.$w
            });
          },
          fill: function(data, $row) {
            $row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
            $row.append('<td>' + data.nomb + '</td>');
            $row.append('<td>' + data.cuenta.cod + '</td>');
            var igv = '--';
            if (data.igv != null) {
              igv = data.igv;
            }
            $row.append('<td>' + igv + '</td>');
            $row.data('data', data).dblclick(function() {
              p.$w.find('.modal-footer button:first').click();
            }).contextMenu('conMenListSel', {
              bindings: {
                'conMenListSel_sel': function(t) {
                  p.$w.find('.modal-footer button:first').click();
                }
              }
            });
            return $row;
          }
        });
      }
    });
  },
  windowSelectProducto: function(p) {
    new K.Modal({
      id: 'windowSelect',
      content: '<div name="tmp"></div>',
      width: 750,
      height: 400,
      title: 'Seleccionar Productos a vender',
      buttons: {
        "Seleccionar": {
          icon: 'fa-check',
          type: 'info',
          f: function() {
            if (p.$w.find('.highlights').data('data') != null) {
              p.callback(p.$w.find('.highlights').data('data'));
              K.closeWindow(p.$w.attr('id'));
            } else {
              K.clearNoti();
              return K.notification({
                title: ciHelper.titleMessages.infoReq,
                text: 'Debe seleccionar un item!',
                type: 'error'
              });
            }
          }
        },
        "Cancelar": {
          icon: 'fa-ban',
          type: 'danger',
          f: function() {
            K.closeWindow(p.$w.attr('id'));
          }
        }
      },
      onClose: function() {
        p = null;
      },
      onContentLoaded: function() {
        p.$w = $('#windowSelect');
        p.$grid = new K.grid({
          $el: p.$w.find('[name=tmp]'),
          cols: ['', 'Nombre', 'Cuenta', 'Stock'],
          data: 'lg/prod/lista',
          params: {
            almacen: p.almacen,
            modulo: p.modulo
          },
          itemdescr: 'producto(s)',
          onLoading: function() {
            K.block({
              $element: p.$w
            });
          },
          onComplete: function() {
            K.unblock({
              $element: p.$w
            });
          },
          fill: function(data, $row) {
            $row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
            $row.append('<td>' + data.nomb + '</td>');
            $row.append('<td>' + data.cuenta.cod + '</td>');
            var stock = '--';
            if (data.stock != null) {
              stock = data.stock.stock
            }
            $row.append('<td>' + stock + '</td>');
            $row.data('data', data).dblclick(function() {
              p.$w.find('.modal-footer button:first').click();
            }).contextMenu('conMenListSel', {
              bindings: {
                'conMenListSel_sel': function(t) {
                  p.$w.find('.modal-footer button:first').click();
                }
              }
            });
            return $row;
          }
        });
      }
    });
  },
  windowSelectCaja: function(p) {
    if (p == null) var p = {};
    new K.Modal({
      id: 'windowSelect',
      content: '<div name="tmp"></div>',
      width: 750,
      height: 400,
      title: 'Seleccionar Caja',
      buttons: {
        "Seleccionar": {
          icon: 'fa-check',
          type: 'info',
          f: function() {
            if (p.$w.find('.highlights').data('data') != null) {
              p.callback(p.$w.find('.highlights').data('data'));
              K.closeWindow(p.$w.attr('id'));
            } else {
              K.clearNoti();
              return K.notification({
                title: ciHelper.titleMessages.infoReq,
                text: 'Debe seleccionar un item!',
                type: 'error'
              });
            }
          }
        },
        "Cancelar": {
          icon: 'fa-ban',
          type: 'danger',
          f: function() {
            K.closeWindow(p.$w.attr('id'));
          }
        }
      },
      onClose: function() {
        p = null;
      },
      onContentLoaded: function() {
        p.$w = $('#windowSelect');
        p.$grid = new K.grid({
          $el: p.$w.find('[name=tmp]'),
          cols: ['', 'Nombre', 'Modulo'],
          data: 'cj/caje/cajas_trabajador',
          params: {
            _id: K.session.enti._id.$id
          },
          itemdescr: 'cajas(s)',
          onLoading: function() {
            K.block({
              $element: p.$w
            });
          },
          onComplete: function() {
            K.unblock({
              $element: p.$w
            });
          },
          pagination: false,
          search: false,
          load: function(data, $tbody) {
            for (var i = 0; i < data.length; i++) {
              var $row = $('<tr class="item" />');
              $row.append('<td><button name="btnGrid">M&aacute;s Acciones</button></td>');
              $row.append('<td>' + data[i].nomb + '</td>');
              $row.append('<td>' + data[i].modulo + '</td>');
              $row.data('data', data[i]).dblclick(function() {
                p.$w.find('.modal-footer button:first').click();
              }).contextMenu('conMenListSel', {
                bindings: {
                  'conMenListSel_sel': function(t) {
                    p.$w.find('.modal-footer button:first').click();
                  }
                }
              });
              $tbody.append($row);
            }
            return $tbody;
          }
        });
      }
    });
  },
  windowVoucher: function(p) {
    new K.Modal({
      id: 'windowVoucher' + p.id,
      title: 'Cambiar Datos de Voucher',
      content: '<div name="gridForm"></div>',
      width: 650,
      height: 450,
      buttons: {
        'Actualizar': {
          icon: 'save',
          type: 'success',
          f: function() {
            K.clearNoti();
            var data = {
              _id: p.id
            };
            var tmp_total = 0;
            data.efectivos = [{
                moneda: 'S',
                monto: p.$w.find('[name=mon_sol] [name=tot]').val()
              },
              {
                moneda: 'D',
                monto: p.$w.find('[name=mon_dol] [name=tot]').val()
              }
            ];
            tmp_total += parseFloat(p.$w.find('[name=mon_sol] [name=tot]').val());
            console.log(tmp_total);
            tmp_total += parseFloat(p.$w.find('[name=mon_dol] [name=tot]').val());
            console.log(tmp_total);
            console.log(data.efectivos);

            for (var i = 0, j = p.$w.find('[name=ctban]').length; i < j; i++) {
              var tmp = {
                num: p.$w.find('[name=ctban]').eq(i).find('[name=voucher]').val(),
                monto: parseFloat(p.$w.find('[name=ctban]').eq(i).find('[name=tot]').val()),
                moneda: p.$w.find('[name=ctban]').eq(i).data('moneda'),
                cuenta_banco: p.$w.find('[name=ctban]').eq(i).data('data')
              };
              console.log(tmp);
              if (tmp.monto > 0) {
                if (tmp.num == '') {
                  p.$w.find('[name=ctban]').eq(i).find('[name=voucher]').focus();
                  return K.notification({
                    title: ciHelper.titleMessages.infoReq,
                    text: 'Debe ingresar un n&uacute;mero de voucher!',
                    type: 'error'
                  });
                }
                if (data.vouchers == null) data.vouchers = [];
                data.vouchers.push(tmp);
                tmp_total += parseFloat(tmp.monto);
              }
            }
            console.log(tmp_total);
            console.log(p.total);
            //if(tmp_total!=p.total){
            if (K.round(tmp_total, 2) != p.total) {
              return K.notification({
                title: ciHelper.titleMessages.infoReq,
                text: 'El total del comprobante <b>(' + ciHelper.formatMon(p.total) + ')</b> no coincide con la forma de pago!',
                type: 'error'
              });
            }
            K.sendingInfo();
            p.$w.find('#div_buttons button').attr('disabled', 'disabled');
            $.post('cj/ecom/save_pago', data, function() {
              K.clearNoti();
              K.closeWindow(p.$w.attr('id'));
              K.notification({
                title: ciHelper.titleMessages.regiAct,
                text: 'El comprobante electronico fue actualizado con &eacute;xito!'
              });
              cjEcom.init();
            });
          }
        },
        'Cancelar': {
          icon: 'fa-ban',
          type: 'danger',
          f: function() {
            K.closeWindow(p.$w.attr('id'));
          }
        }
      },
      onContentLoaded: function() {
        p.$w = $('#windowVoucher' + p.id);
        new K.grid({
          $el: p.$w.find('[name=gridForm]'),
          search: false,
          pagination: false,
          cols: ['Descripci&oacute;n', '', 'Subtotal', ''],
          onlyHtml: true
        });
        K.block({
          $element: p.$w
        });
        $.post('cj/ecom/get', {
          _id: p.id,
          forma: true
        }, function(data) {
          p.total = parseFloat(data.total);
          p.ctban = data.ctban;
          /*Efectivo Soles*/
          var $row = $('<tr class="item" name="mon_sol">');
          $row.append('<td>Efectivo Soles</td>');
          $row.append('<td>');
          $row.append('<td>S/.<input type="text" name="tot" size="7"/></td>');
          $row.append('<td>S/.0.00</td>');
          $row.find('[name=tot]').keyup(function() {
            if ($(this).val() == '')
              $(this).val(0);
            $(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon($(this).val()));
            $(this).closest('.item').data('total', parseFloat($(this).val()));
          }).val(data.efectivos[0].monto);
          p.$w.find('[name=gridForm] tbody').append($row);
          /*Efectivo Dolares*/
          var $row = $('<tr class="item" name="mon_dol">');
          $row.append('<td>Efectivo D&oacute;lares</td>');
          $row.append('<td>');
          $row.append('<td>$<input type="text" name="tot" size="7"/></td>');
          $row.append('<td>$0.00</td>');
          $row.find('[name=tot]').val(0).keyup(function() {
            if ($(this).val() == '')
              $(this).val(0);
            $(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon($(this).val(), '$'));
            $(this).closest('.item').data('total', parseFloat($(this).val()));
          }).val(data.efectivos[1].monto);
          p.$w.find('[name=gridForm] tbody').append($row);
          /*Cuentas bancarios*/
          for (var i = 0, j = p.ctban.length; i < j; i++) {
            var $row = $('<tr class="item" name="ctban" data-ctban="' + p.ctban[i]._id.$id + '">');
            $row.append('<td>Voucher <input type="text" name="voucher" size="7"/></td>');
            $row.append('<td>' + p.ctban[i].nomb + '</td>');
            $row.append('<td>' + (data.ctban[i].moneda == 'S' ? 'S/.' : '$') + '<input type="number" name="tot" size="7"/></td>');
            $row.append('<td>S/.0.00</td>');
            $row.find('[name=tot]').val(0).keyup(function() {
              if ($(this).val() == '')
                $(this).val(0);
              var moneda = $(this).closest('.item').data('moneda'),
                tot = moneda == 'S' ? $(this).val() : $(this).val() * p.tasa;
              $(this).closest('.item').find('td:eq(3)').html(ciHelper.formatMon(tot));
              $(this).closest('.item').data('total', parseFloat(tot));
            });
            $row.data('moneda', p.ctban[i].moneda).data('data', {
              _id: p.ctban[i]._id.$id,
              cod: p.ctban[i].cod,
              nomb: p.ctban[i].nomb,
              moneda: p.ctban[i].moneda,
              cod_banco: p.ctban[i].cod_banco
            });
            p.$w.find('[name=gridForm] tbody').append($row);
          }
          if (data.vouchers != null) {
            for (var i = 0, j = data.vouchers.length; i < j; i++) {
              var voucher = data.vouchers[i];
              for (var i = 0, j = p.ctban.length; i < j; i++) {
                if (voucher.cuenta_banco._id.$id == p.ctban[i]._id.$id) {
                  var $row = p.$w.find('[data-ctban=' + p.ctban[i]._id.$id + ']');
                  $row.find('[name=voucher]').val(voucher.num);
                  $row.find('[name=tot]').val(voucher.monto);
                }
              }
            }
          }
          K.unblock({
            $element: p.$w
          });
        }, 'json');
      }
    });
  },
};
define(
  ['mg/enti', 'mg/prog', 'mg/orga', 'mg/serv', 'cj/talo', 'cj/conc', 'in/movi', 'lg/prod'],
  function(mgEnti, mgProg, mgOrga, mgServ, cjTalo, cjConc, inMovi, lgProd) {
    return cjEcom;
  }
);
