/*
 * MOVIMIENTOS DE INMUEBLES
 */
inAler = {
  meses_cobranza_dudosa: 12,
  estado_pago: {
    P: {
      descr: "Pendiente",
      color: "green",
      label: '<span class="label label-default">Pendiente</span>'
    },
    C: {
      descr: "Cancelado",
      color: "green",
      label: '<span class="label label-success">Cancelado</span>'
    },
    X: {
      descr: "Anulado",
      color: "#CCCCCC",
      label: '<span class="label label-warning">Anulado</span>'
    }
  },
  entidad: {
    P: {
      descr: "Persona",
      color: "green",
      label: '<span class="label label-default">Persona</span>'
    },
    E: {
      descr: "Empresa",
      color: "green",
      label: '<span class="label label-success">Empresa</span>'
    }
  },
  situacion: {
    'O': {
      descr: 'OCUPADO'
    },
    'U': {
      descr: 'CESION EN USO'
    },
    'C': {
      descr: 'CONVENIO'
    },
    'D': {
      descr: 'DESOCUPADO'
    },
    'M': {
      descr: 'COMODATO'
    }
  },
  tipo_doc_gar: {
    RI: 'Recibo de Ingresos',
    RC: 'Recibo de Caja',
    NC: 'Nota Contable',
    FACT: 'Factura',
    CP: 'Comprobante de Pago',
    BV: 'Boleta de Venta'
  },
  /**************************************************************************************************************************
   *
   * OBTENER GLOSA DE MOTIVO DE CONTRATO
   *
   **************************************************************************************************************************/
  get_motivo: function(motivo_id) {
    var texto_pago = '';
    switch (motivo_id) {
      case '55316577bc795ba80100003b':
        //SIN CONTRATO
        texto_pago = 'POR OCUPACION';
        break;
      case '55316565bc795ba801000037':
        //RENOVACION SIN CONTRATO
        texto_pago = 'POR OCUPACION';
        break;
      case '5531652fbc795ba80100002d':
        //ACTA DE CONCILIACION
        texto_pago = 'POR OCUPACION';
        break;
      case '5531656fbc795ba801000039':
        //RENOVACION
        texto_pago = 'ALQUILER';
        break;
      case '5531654cbc795ba801000033':
        //NUEVO
        texto_pago = 'ALQUILER';
        break;
      case '5540f3c3bc795b7801000029':
        //convenios
        texto_pago = 'ALQUILER';
        break;
      case '55316543bc795ba801000031':
        //convenios
        texto_pago = 'POR AUTORIZACION';
        break;
      case "55316553bc795ba801000035":
        texto_pago = 'PENALIDAD';
        break;
      default:
        texto_pago = 'XXXXXXXXXXXXX';
    }
    return texto_pago;
  },
  /**************************************************************************************************************************
   *
   * VISTA INICIAL DE ALERTAS DE DEUDORES
   *
   **************************************************************************************************************************/
  init: function(p) {
    if (p == null) p = {};
    K.initMode({
      mode: 'in',
      action: 'inAler',
      titleBar: {
        title: 'Alerta de deudores'
      }
    });

    new K.Panel({
      contentURL: 'in/aler',
      onContentLoaded: function() {
        p.$w = $('#mainPanel');
        //p.$w.find('[name=tipo],[name=sublocal],[name=inmueble]').chosen();
        p.$w.find('[name=tipo]').change(function() {
          var $this = $(this),
            val = p.tipo[$this.find('option:selected').val()]._id.$id,
            $cbo = p.$w.find('[name=sublocal]').empty();
          if (p.sublocal != null) {
            for (var i = 0, j = p.sublocal.length; i < j; i++) {
              if (p.sublocal[i].tipo._id.$id == val)
                $cbo.append('<option value="' + i + '">' + p.sublocal[i].nomb + '</option>');
            }
          }
          $cbo.chosen().trigger("chosen:updated");
          $cbo.change();
        });
        p.$w.find('[name=sublocal]').change(function() {
          p.$w.find('[name=inmueble]').empty();
          if ($(this).find('option').length == 0) {
            p.$w.find('[name=inmueble]').change();
            return K.msg({
              title: ciHelper.titles.infoReq,
              text: 'Debe escoger un local v&aacute;lido!',
              type: 'error'
            });
          }
          K.block();
          $.post('in/inmu/get_all_sub', {
            _id: p.sublocal[$(this).find('option:selected').val()]._id.$id
          }, function(data) {
            var $cbo = p.$w.find('[name=inmueble]').empty();
            if (data != null) {
              for (var i = 0, j = data.length; i < j; i++) {
                $cbo.append('<option value="' + data[i]._id.$id + '">' + data[i].abrev + '</option>');
                $cbo.find('option:last').data('data', data[i]);
              }
            }
            $cbo.chosen().trigger("chosen:updated");
            $cbo.change();
            K.unblock();
          }, 'json');
        });
        p.$w.find('[name=btnRefresh]').click(function(e) {
          e.preventDefault();
          p.$w.find('[name=inmueble]').change();
        });
        p.$w.find('[name=btnExport]').click(function(e) {
          e.preventDefault();
          var inmueble = p.$w.find('[name=inmueble]').find('option:selected').data('data');
          window.open('in/aler/VencimientoPago?excel&sublocal='+p.sublocal[p.$w.find('[name=sublocal]').find('option:selected').val()]._id.$id+'&inmueble='+inmueble._id.$id);
        });
        p.$w.find('[name=inmueble]').change(function() {
          var inmueble = $(this).find('option:selected').data('data');
          if (inmueble != null) {
            $.jStorage.set('in/aler/VencimientoPagoDias', inInmu.dbRel(inmueble));
            K.block();
            /*PAGOS A PUNTO DE VENCER*/
            $.post('in/aler/VencimientoPagoDias', {
              inmueble: inmueble._id.$id,
              dias: 28
            }, function(data) {
              p.contratos = data.contratos;
              p.actas = data.actas;
              p.titulares = [];
              p.$w.find('[name=gridProx] tbody').empty();
              p.$w.find('[name=gridInfo] tbody').empty();
              p.$w.find('[name=gridCont] tbody').empty();
              if (p.contratos != null) {
                for (var i = 0, j = p.contratos.length; i < j; i++) {
                  var $row = $('<tr class="item" data-titu="' + p.contratos[i].titular._id.$id + '">');
                  $row.append('<td>' + mgEnti.formatName(p.contratos[i].titular) + '</td>');
                  $row.append('<td>' + mgEnti.formatDNI(p.contratos[i].titular) + '</td>');
                  $row.append('<td>' + mgEnti.formatRUC(p.contratos[i].titular) + '</td>');
                  $row.append('<td><kbd>' + ciHelper.formatMon(p.contratos[i].importe, p.contratos[i].moneda) + '</kbd></td>');
                  $row.append('<td>' + ciHelper.date.format.bd_ymd(p.contratos[i].fecpago) + '</td>');
                  $row.append('<td>' + p.contratos[i].dias_rest + '</td>');
									if (p.contratos[i].dias_rest >= 20) {
										$row.append('<td style="background-color: #4CAF50;">' + '</td>');
									}else if (p.contratos[i].dias_rest >= 10) {
										$row.append('<td style="background-color: #FFBE00;">' + '</td>');
									}else if (p.contratos[i].dias_rest >= 0) {
										$row.append('<td style="background-color: #E50A00;">' + '</td>');
									}else {
										$row.append('<td>' + '</td>');
									}


                  $row.data('data', p.contratos[i]);

                  $row.click(function() {
                    var contrato = $(this).closest('.item').data('data');
                    var datInfo = p.$w.find('[name=formInfo]');
                    $.post('in/movi/get_cont', {
                      _id: contrato.contrato.$id
                    }, function(cont) {
                      /**
                       * Datos Generales del Contrato
                       * @var array Array DOM tabla
                       */
                      datInfo.find('[name=cont_fecmod]').html(ciHelper.date.format.bd_ymd(cont.fecmod));
                      datInfo.find('[name=cont_trabajador]').html(mgEnti.formatName(cont.trabajador));
                      datInfo.find('[name=cont_fecini]').html(ciHelper.date.format.bd_ymd(cont.fecini));
                      datInfo.find('[name=cont_fecfin]').html(ciHelper.date.format.bd_ymd(cont.fecfin));
                      datInfo.find('[name=cont_situacion]').html(inAler.situacion[cont.situacion].descr);
                      datInfo.find('[name=cont_importe]').html('<kbd>' + ciHelper.formatMon(cont.importe, cont.moneda) + '</kbd>');
                      datInfo.find('[name=cont_motivo]').html(cont.motivo.nomb);
                      $.post('in/inmu/get', {
                        _id: cont.inmueble._id.$id
                      }, function(inmu) {
                        /**
                         * Datos Generales del Inmueble
                         * @var array Array DOM tabla
                         */
                        datInfo.find('[name=inmu_direccion]').html(inmu.direccion);
                        datInfo.find('[name=inmu_sublocal]').html(inmu.sublocal.nomb);
                        datInfo.find('[name=inmu_tipo]').html(inmu.tipo.nomb);
                        $.post('mg/enti/get', {
                          _id: cont.titular._id.$id
                        }, function(data) {
                          /**
                           * Datos Generales del Arrendatario
                           * @var array Array DOM tabla
                           */
                          datInfo.find('[name=arrendatario]').html(mgEnti.formatName(data));
                          datInfo.find('[name=tipo_entidad]').html(inAler.entidad[data.tipo_enti].label);
                          datInfo.find('[name=docidents]').empty();
                          if (data.docident !== null) {
                            data.docident.forEach(function(doc) {
                              var docident = datInfo.find('[name=docidents]');
                              docident.append('<tr style="border-bottom:1px solid black" class="item" info-titu="' + doc.tipo + '">');
                              docident.append('<td><label>Tipo de documento:	</label></td>');
                              docident.append('<td width="130px"><a name="clasif" style="text-decoration: underline;cursor: pointer;"></a>' + doc.tipo + '</td>');
                              docident.append('<td><label>Numero:</label></td>');
                              docident.append('<td>' + doc.num + '</td>');
                            });
                          }
                          datInfo.find('[name=domicilios]').empty();
                          if (data.domicilios != null) {
                            data.domicilios.forEach(function(domic) {
                              var domicilios = datInfo.find('[name=domicilios]');
                              domicilios.append('<tr style="border-bottom:1px solid black" class="item" info-titu="' + domic.direccion + '">');
                              if (domic.tipo != null) {
                                domicilios.append('<td><label>Tipo:	</label></td>');
                                domicilios.append('<td width="130px"><a style="text-decoration: underline;cursor: pointer;"></a>' + domic.tipo + '</td>');
                              }
                              domicilios.append('<td><label>Direccion:	</label></td>');
                              domicilios.append('<td width="130px"><a style="text-decoration: underline;cursor: pointer;"></a>' + domic.direccion + '</td>');
                            });
                          }
                          datInfo.find('[name=telefonos]').empty();
                          if (data.telefonos != null) {
                            data.telefonos.forEach(function(tele) {
                              var telefonos = datInfo.find('[name=telefonos]');
                              telefonos.append('<tr style="border-bottom:1px solid black" class="item" info-titu="' + tele.num + '">');
                              if (tele.tipo != null) {
                                telefonos.append('<td><label>Tipo:	</label></td>');
                                telefonos.append('<td width="130px"><a style="text-decoration: underline;cursor: pointer;"></a>' + tele.tipo + '</td>');
                              }
                              telefonos.append('<td><label>Telefono:	</label></td>');
                              telefonos.append('<td width="130px"><a style="text-decoration: underline;cursor: pointer;"></a>' + tele.num + '</td>');
                            });
                          }
                          datInfo.find('[name=emails]').empty();
                          if (data.emails != null) {
                            data.emails.forEach(function(email) {
                              var emails = datInfo.find('[name=emails]');
                              emails.append('<tr style="border-bottom:1px solid black" class="item" info-titu="' + email.direc + '">');
                              if (email.tipo != null) {
                                emails.append('<td><label>Tipo:	</label></td>');
                                emails.append('<td width="130px"><a style="text-decoration: underline;cursor: pointer;"></a>' + email.tipo + '</td>');
                              }
                              emails.append('<td><label>e-mail:	</label></td>');
                              emails.append('<td width="130px"><a style="text-decoration: underline;cursor: pointer;"></a>' + email.direc + '</td>');
                            });
                          }
                          datInfo.find('[name=websites]').empty();
                          if (data.urls != null) {
                            data.urls.forEach(function(web) {
                              var websites = datInfo.find('[name=websites]');
                              websites.append('<tr style="border-bottom:1px solid black" class="item" info-titu="' + web.direc + '">');
                              if (websites.tipo != null) {
                                websites.append('<td><label>Tipo:	</label></td>');
                                websites.append('<td width="130px"><a style="text-decoration: underline;cursor: pointer;"></a>' + web.tipo + '</td>');
                              }
                              if (websites.desc != null) {
                                websites.append('<td><label>Desc:	</label></td>');
                                websites.append('<td width="130px"><a style="text-decoration: underline;cursor: pointer;"></a>' + web.tipo + '</td>');
                              }
                              websites.append('<td><label>e-mail:	</label></td>');
                              websites.append('<td width="130px"><a style="text-decoration: underline;cursor: pointer;"></a>' + web.direc + '</td>');
                            });
                          }
                        }, 'json');
                      }, 'json');
                    }, 'json');



                  });

                  p.$w.find('[name=gridProx] tbody').append($row);
                  if (p.titulares.indexOf(p.contratos[i].titular) == -1) {
                    p.titulares.push(p.contratos[i].titular);
                  }
                }
              }
              p.$w.find('[name=gridProx] .item:eq(0)').click();
              K.unblock();
            }, 'json');
            $.post('in/aler/VencimientoPago', {
              sublocal: p.sublocal[p.$w.find('[name=sublocal]').find('option:selected').val()]._id.$id,
              inmueble: inmueble._id.$id,
            }, function(data) {
              p.contratos_v = data.contratos;
              p.actas_v = data.actas;
              p.titulares_v = [];
              p.$w.find('[name=gridVenc] tbody').empty();
              p.$w.find('[name=gridConts] tbody').empty();
              p.$w.find('[name=gridDeta] tbody').empty();
              if (p.contratos_v != null) {
                for (var i = 0, j = p.contratos_v.length; i < j; i++) {
                  /**
                   * Llenado de contratos
                   * @var {[type]}
                   */
                  var $row = $('<tr class="item" data-titu="' + p.contratos_v[i].titular._id.$id + '">');

                  $row.append('<td>' + ciHelper.date.format.bd_ymd(p.contratos_v[i].nro_contrato) + '</td>');
                  $row.append('<td>' + p.contratos_v[i].inmueble.direccion + '</td>');
                  $row.append('<td><kbd>' + ciHelper.formatMon(p.contratos_v[i].total, p.contratos_v[i].moneda) + '</kbd></td>');
                  $row.append('<td>' + '</td>');

                  $row.data('data', p.contratos_v[i]);
                  /**
                   * PAGOS DEL CONTRATO
                   * @return {[type]} [description]
                   */
                  $row.find('td:eq(0),td:eq(1),td:eq(2),td:eq(3)').click(function() {
                    p.$w.find('[name=gridDeta] tbody .item').remove();

                    var contrato = $(this).closest('.item').data('data');

                    if (contrato.pagos != null) {
                      for (var i = 0; i < contrato.pagos.length; i++) {
                        var tmp_impor = null;
                        //
                        // * En contratos de fecha de inicio se limita el importe por fecha de desocupacion
                        //

                        var $row = $('<tr class="item">');
                        $row.append('<td>' + ciHelper.date.format.bd_ymd(contrato.pagos[i].fecpago) + '</td>');
                        $row.append('<td>' + ciHelper.formatMon(contrato.pagos[i].importe, contrato.moneda) + '</td>');
                        $row.append('<td>' + contrato.pagos[i].dias_sobr + '</td>');
                        $row.data('data', contrato.pagos[i]);
                        p.$w.find('[name=gridDeta] tbody').append($row);
                      }
                    }

                  });
                  p.$w.find('[name=gridConts] tbody').append($row);
                  if (p.titulares_v.indexOf(p.contratos_v[i].titular) == -1) {
                    p.titulares_v.push(p.contratos_v[i].titular);
                  }
                  if (p.$w.find('[name=' + p.contratos_v[i].titular._id.$id + ']').length == 0) {
                    /**
                     * Llenado de Titulares si no hay uno registrado
                     * @var {[type]}
                     */
                    var $row = $('<tr class="item" name="' + p.contratos_v[i].titular._id.$id + '">');
                    $row.append('<td>' + mgEnti.formatName(p.contratos_v[i].titular) + '</td>');
                    $row.append('<td>' + mgEnti.formatDNI(p.contratos_v[i].titular) + '</td>');
                    $row.append('<td>' + mgEnti.formatRUC(p.contratos_v[i].titular) + '</td>');
                    $row.append('<td></td>');
                    $row.data('data', p.contratos_v[i].titular);
                    $row.click(function() {
                      var titular = $(this).closest('.item').data('data');
                      p.$w.find('[name=gridConts] tbody tr[data-titu=' + titular._id.$id + ']').show();
                      p.$w.find('[name=gridConts] tbody tr[data-titu!=' + titular._id.$id + ']').hide();
                      p.$w.find('[name=gridConts] .item:visible:eq(0) td:first').click();
                    });
                    p.$w.find('[name=gridVenc] tbody').append($row);
                  }
                }
              }
              p.$w.find('[name=gridVenc] .item:visible:eq(0) td:first').click();
              p.$w.find('[name=gridConts] .item:visible:eq(0) td:first').click();
              K.unblock();
            }, 'json');
          }
        });
        new K.grid({
          $el: p.$w.find('[name=gridProx]'),
          search: false,
          pagination: false,
          cols: ['Raz&oacute;n Social / Nombres', 'DNI', 'RUC', 'Importe', 'Fecha de pago', 'Resto de dias', ''],
          onlyHtml: true,
          toolbarHTML: '<h3>ARRENDATARIOS CON PAGOS PROXIMOS A VENCER</h3>'
        });
        new K.grid({
          $el: p.$w.find('[name=gridVenc]'),
          search: false,
          pagination: false,
          cols: ['Raz&oacute;n Social / Nombres', 'DNI', 'RUC', ''],
          onlyHtml: true,
          toolbarHTML: '<h3>ARRENDATARIOS CON PAGOS VENCIDO</h3>'
        });
        new K.grid({
          $el: p.$w.find('[name=gridConts]'),
          search: false,
          pagination: false,
          cols: ['Contrato', 'Inmueble', 'Importe', ''],
          onlyHtml: true,
          toolbarHTML: '<h3>Contratos</h3>'
        });
        new K.grid({
          $el: p.$w.find('[name=gridDeta]'),
          search: false,
          pagination: false,
          cols: ['Fecha de Pago', 'Importe', 'Días desde el ultimo pago', ''],
          onlyHtml: true,
          toolbarHTML: '<h3>PAGOS VENCIDOS</h3>'
        });

        $.post('in/movi/get_tipo_sub', function(data) {
          p.$w.find('[name=gridVenc] .fuelux:first,[name=gridDeta] .fuelux:first').css('max-height', '420px');
          p.tipo = data.tipo;
          p.sublocal = data.sublocal;
          var $cbo = p.$w.find('[name=tipo]');
          for (var i = 0, j = p.tipo.length; i < j; i++) {
            $cbo.append('<option value="' + i + '">' + p.tipo[i].nomb + '</option>');
          }
          var inmu_tmp = $.jStorage.get('in/movi/get_tipo_sub');
          if (p.inmueble == null) {
            if (inmu_tmp != null) p.inmueble = inmu_tmp;
          }
          if (p.inmueble == null) {
            p.$w.find('[name=tipo]').chosen();
            $cbo.change();
          } else {
            for (var i = 0, j = p.tipo.length; i < j; i++) {
              if (p.tipo[i]._id.$id == p.inmueble.tipo._id) {
                p.$w.find('[name=tipo] option').eq(i).attr('selected', 'selected');
                break;
              }
            }
            p.$w.find('[name=tipo]').chosen();
            var val = p.inmueble.tipo._id,
              $cbo = p.$w.find('[name=sublocal]').empty();
            if (p.sublocal != null) {
              for (var i = 0, j = p.sublocal.length; i < j; i++) {
                if (p.sublocal[i].tipo._id.$id == val) {
                  $cbo.append('<option value="' + i + '">' + p.sublocal[i].nomb + '</option>');
                  if (p.sublocal[i]._id.$id == p.inmueble.sublocal._id)
                    $cbo.find('option:last').attr('selected', 'selected');
                }
              }
            }
            p.$w.find('[name=sublocal]').chosen();
            p.$w.find('[name=inmueble]').empty();
            $.post('in/aler/VencimientoPagoDias', {
              _id: p.inmueble.sublocal._id
            }, function(data) {
              var $cbo = p.$w.find('[name=inmueble]').empty();
              if (data != null) {
                for (var i = 0, j = data.length; i < j; i++) {
                  $cbo.append('<option value="' + data[i]._id.$id + '">' + data[i].direccion + '</option>');
                  $cbo.find('option:last').data('data', data[i]);
                }
              }
              $cbo.selectVal(p.inmueble._id);
              $cbo.chosen().change();
            }, 'json');
          }
        }, 'json');
      }
    });
  },
};

define(
  ['mg/serv', 'in/inmu', 'in/moti', 'cj/talo', 'cj/conc'],
  function(mgServ, inInmu, inMoti, cjTalo, cjConc) {
    return inAler;
  }
);