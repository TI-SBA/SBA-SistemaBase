<?php
define('APPPATH', IndexPath.DS);
error_reporting(E_ALL);
ini_set('display_errors', 1);
include APPPATH."/libraries/xmlseclibs/autoload.php";
require APPPATH.'/libraries/Number.php';
require APPPATH.'/libraries/conflux/ConfluxSee.php';
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use RobRichards\XMLSecLibs\XMLSecEnc;

class Controller_cj_ecom extends Controller
{
    public function execute_lista()
    {
        global $f;
        $params = array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows);
        if (isset($f->request->data['tipo'])) {
            $params['tipo'] = $f->request->data['tipo'];
        }
        if (isset($f->request->data['estado'])) {
            $params['estado'] = $f->request->data['estado'];
        }
        if (isset($f->request->data['texto'])) {
            if ($f->request->data['texto']!='') {
                $params['texto'] = $f->request->data['texto'];
            }
        }
        if (isset($f->request->data['caja'])) {
            $params['caja'] = new MongoId($f->request->data['caja']);
        }
        #FILTRADO REALIZADO POR GIANCARLO, CON COMPROBANTES QUE NO DEBEN APARECER COMO LA SERIE DE DESARROLLO B005 Y DEMAS PRUEBAS INTERNAS
        /*
        if($f->session->userDB['_id']->{'$id'}!="597f2a463e603743328b4569"){
            $params['serie'] = array('$nin'=>array(
                "B005",
            ));
            $params['_id'] = array('$nin'=> array(
                new MongoId("5a7b95ac86bf00bc0800002f"),
            ));
        }*/
        $model = $f->model("cj/ecom")->params($params)->get("lista");
        $f->response->json($model);
    }
    public function execute_get()
    {
        global $f;
        $model = $f->model('cj/ecom')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
        if (isset($f->request->data['forma'])) {
            #ENVIAR CUENTAS BANCARIAS PARA MODIFICA PAGOS
            $model['ctban'] = $f->model("ts/ctban")->get("all")->items;
        }
        $f->response->json($model);
    }
    public function execute_save()
    {
        global $f;
        $data = $f->request->data;
        $trabajador = $f->session->userDB;
        $response = array(
            'status'=>'error',
            'message'=>'',
            'data'=>array(
                'id'=>'',
                'estado'=>'',
                'estado_resumen'=>'',
                'estado_baja'=>'',
                'sunat_note'=>'',
                'sunat_description'=>'',
                'sunat_responsecode'=>'',
                'sunat_faultcode'=>'',
                'sunat_soap_error'=>'',
                'digest_value'=>'',
                'signature_value'=>'',
                'codigo_barras'=>'',
                'codigo_barras_pdf'=>'',
                'ruta_zip_firmado'=>'',
                'ruta_xml_firmado'=>'',
                'ruta_cdr_xml'=>'',
                'ruta_pdf'=>''
            )
        );
        if (isset($data['cliente_id'])) {
            $data['cliente_id'] = new MongoId($data['cliente_id']);
        }
        if (isset($data['cliente_nomb'])) {
            $data['cliente_nomb'] = trim($data['cliente_nomb']);
        }

        if (isset($data['caja']['_id'])) {
            $data['caja']['_id'] = new MongoId($data['caja']['_id']);
        }
        if (isset($data['efectivos'])) {
            foreach ($data['efectivos'] as $i=>$ef) {
                $data['efectivos'][$i]['monto'] = floatval($ef['monto']);
            }
        }
        if (isset($data['vouchers'])) {
            foreach ($data['vouchers'] as $i=>$vou) {
                $data['vouchers'][$i]['monto'] = floatval($vou['monto']);
                $data['vouchers'][$i]['cuenta_banco']['_id'] = new MongoId($vou['cuenta_banco']['_id']);
            }
        }
        if (!isset($data['descuento_global'])) {
            $data['descuento_global'] = 0;
        }
        if (isset($data['items'])) {
            foreach ($data['items'] as $i => $item) {
                if (isset($item['cant'])) {
                    $data['items'][$i]['cant'] = floatval($item['cant']);
                }
                if (isset($item['valor_unitario'])) {
                    $data['items'][$i]['valor_unitario'] = floatval($item['valor_unitario']);
                }
                if (isset($item['igv'])) {
                    $data['items'][$i]['igv'] = floatval($item['igv']);
                }
                if (isset($item['precio_venta_unitario'])) {
                    $data['items'][$i]['precio_venta_unitario'] = floatval($item['precio_venta_unitario']);
                }
                if (isset($item['precio_total'])) {
                    $data['items'][$i]['precio_total'] = floatval($item['precio_total']);
                }

                $data['items'][$i]['descuento'] = 0;
                $data['items'][$i]['gratuito'] = false;
                $data['items'][$i]['valor_venta'] = $item['valor_unitario']*$item['cant'];
                $data['items'][$i]['valor_gratuito'] = 0;
                $data['items'][$i]['isc'] = 0;

                if (isset($item['subitems'])) {
                    if (count($item['subitems'])) {
                        foreach ($item['subitems'] as $j => $subitem) {
                            if (isset($subitem['cant'])) {
                                $data['items'][$i]['subitems'][$j]['cant'] = floatval($subitem['cant']);
                            }
                            if (isset($subitem['valor_unitario'])) {
                                $data['items'][$i]['subitems'][$j]['valor_unitario'] = floatval($subitem['valor_unitario']);
                            }
                            if (isset($subitem['igv'])) {
                                $data['items'][$i]['subitems'][$j]['igv'] = floatval($subitem['igv']);
                            }
                            if (isset($subitem['precio_venta_unitario'])) {
                                $data['items'][$i]['subitems'][$j]['precio_venta_unitario'] = floatval($subitem['precio_venta_unitario']);
                            }
                            if (isset($subitem['precio_total'])) {
                                $data['items'][$i]['subitems'][$j]['precio_total'] = floatval($subitem['precio_total']);
                            }
                            $data['items'][$i]['subitems'][$j]['descuento'] = 0;
                            $data['items'][$i]['subitems'][$j]['gratuito'] = false;
                            $data['items'][$i]['subitems'][$j]['valor_venta'] = $subitem['valor_unitario']*$subitem['cant'];
                            $data['items'][$i]['subitems'][$j]['valor_gratuito'] = 0;
                            $data['items'][$i]['subitems'][$j]['isc'] = 0;
                        }
                    }
                }

                if (isset($item['tipo'])) {
                    switch ($item['tipo']) {
                        case 'pago_meses':
                            foreach ($item['conceptos'] as $j => $concepto) {
                                if (isset($concepto['cuenta']['_id'])) {
                                    if (is_string($concepto['cuenta']['_id'])) {
                                        $data['items'][$i]['conceptos'][$j]['cuenta']['_id'] = new MongoId($concepto['cuenta']['_id']);
                                    }
                                }
                                if (isset($concepto['alquiler']['contrato'])) {
                                    if (is_string($concepto['alquiler']['contrato'])) {
                                        $data['items'][$i]['conceptos'][$j]['alquiler']['contrato'] = new MongoId($concepto['alquiler']['contrato']);
                                    }
                                }

                                if (isset($concepto['item'])) {
                                    $data['items'][$i]['conceptos'][$j]['item'] = floatval($concepto['item']);
                                }

                                if (isset($concepto['parent'])) {
                                    $data['items'][$i]['conceptos'][$j]['parent'] = floatval($concepto['parent']);
                                }

                                if (isset($concepto['monto'])) {
                                    $data['items'][$i]['conceptos'][$j]['monto'] = floatval($concepto['monto']);
                                }

                                if (isset($concepto['igv'])) {
                                    $data['items'][$i]['conceptos'][$j]['igv'] = floatval($concepto['igv']);
                                }

                                if (isset($concepto['porcentaje_mora'])) {
                                    $data['items'][$i]['conceptos'][$j]['porcentaje_mora'] = floatval($concepto['porcentaje_mora']);
                                }
                            }

                            break;
                        case 'pago_parcial':

                            foreach ($item['conceptos'] as $j => $concepto) {
                                if (isset($concepto['cuenta']['_id'])) {
                                    if (is_string($concepto['cuenta']['_id'])) {
                                        $data['items'][$i]['conceptos'][$j]['cuenta']['_id'] = new MongoId($concepto['cuenta']['_id']);
                                    }
                                }
                                if (isset($concepto['alquiler']['contrato'])) {
                                    if (is_string($concepto['alquiler']['contrato'])) {
                                        $data['items'][$i]['conceptos'][$j]['alquiler']['contrato'] = new MongoId($concepto['alquiler']['contrato']);
                                    }
                                }

                                if (isset($concepto['item'])) {
                                    $data['items'][$i]['conceptos'][$j]['item'] = floatval($concepto['item']);
                                }

                                if (isset($concepto['parent'])) {
                                    $data['items'][$i]['conceptos'][$j]['parent'] = floatval($concepto['parent']);
                                }

                                if (isset($concepto['monto'])) {
                                    $data['items'][$i]['conceptos'][$j]['monto'] = floatval($concepto['monto']);
                                }

                                if (isset($concepto['igv'])) {
                                    $data['items'][$i]['conceptos'][$j]['igv'] = floatval($concepto['igv']);
                                }

                                if (isset($concepto['porcentaje_mora'])) {
                                    $data['items'][$i]['conceptos'][$j]['porcentaje_mora'] = floatval($concepto['porcentaje_mora']);
                                }
                            }
                            break;
                        case 'pago_acta':
                            foreach ($item['conceptos'] as $j => $concepto) {
                                if (isset($concepto['cuenta']['_id'])) {
                                    if (is_string($concepto['cuenta']['_id'])) {
                                        $data['items'][$i]['conceptos'][$j]['cuenta']['_id'] = new MongoId($concepto['cuenta']['_id']);
                                    }
                                }
                                if (isset($concepto['alquiler']['contrato'])) {
                                    if (is_string($concepto['alquiler']['contrato'])) {
                                        $data['items'][$i]['conceptos'][$j]['alquiler']['contrato'] = new MongoId($concepto['alquiler']['contrato']);
                                    }
                                }

                                if (isset($concepto['item'])) {
                                    $data['items'][$i]['conceptos'][$j]['item'] = floatval($concepto['item']);
                                }

                                if (isset($concepto['parent'])) {
                                    $data['items'][$i]['conceptos'][$j]['parent'] = floatval($concepto['parent']);
                                }

                                if (isset($concepto['monto'])) {
                                    $data['items'][$i]['conceptos'][$j]['monto'] = floatval($concepto['monto']);
                                }

                                if (isset($concepto['monto_mora'])) {
                                    $data['items'][$i]['conceptos'][$j]['monto_mora'] = floatval($concepto['monto_mora']);
                                }

                                if (isset($concepto['igv'])) {
                                    $data['items'][$i]['conceptos'][$j]['igv'] = floatval($concepto['igv']);
                                }

                                if (isset($concepto['porcentaje_mora'])) {
                                    $data['items'][$i]['conceptos'][$j]['porcentaje_mora'] = floatval($concepto['porcentaje_mora']);
                                }
                                if (isset($concepto['acta_conciliacion'])) {
                                    $data['items'][$i]['conceptos'][$j]['acta_conciliacion'] = new MongoId($concepto['acta_conciliacion']);
                                }
                            }
                            break;
                        case 'cuenta_cobrar':
                            if (isset($item['cuenta_cobrar']['_id'])) {
                                $data['items'][$i]['cuenta_cobrar']['_id'] = $item['cuenta_cobrar']['_id'];
                            }
                            if (isset($item['cuenta_cobrar']['servicio']['_id'])) {
                                $data['items'][$i]['cuenta_cobrar']['servicio']['_id'] = $item['cuenta_cobrar']['servicio']['_id'];
                            }
                            foreach ($item['conceptos'] as $j => $concepto) {
                                if (isset($concepto['cuenta']['_id'])) {
                                    if (is_string($concepto['cuenta']['_id'])) {
                                        $data['items'][$i]['conceptos'][$j]['cuenta']['_id'] = new MongoId($concepto['cuenta']['_id']);
                                        unset($data['items'][$i]['conceptos'][$j]['cuenta']['tipo']);
                                        unset($data['items'][$i]['conceptos'][$j]['cuenta']['cuentas']);
                                        unset($data['items'][$i]['conceptos'][$j]['cuenta']['fecreg']);
                                    }
                                }

                                if (isset($concepto['item'])) {
                                    $data['items'][$i]['conceptos'][$j]['item'] = floatval($concepto['item']);
                                }

                                if (isset($concepto['parent'])) {
                                    $data['items'][$i]['conceptos'][$j]['parent'] = floatval($concepto['parent']);
                                }

                                if (isset($concepto['monto'])) {
                                    $data['items'][$i]['conceptos'][$j]['monto'] = floatval($concepto['monto']);
                                }

                                if (isset($concepto['igv'])) {
                                    $data['items'][$i]['conceptos'][$j]['igv'] = floatval($concepto['igv']);
                                }

                                if (isset($concepto['cant'])) {
                                    $data['items'][$i]['conceptos'][$j]['cant'] = floatval($concepto['cant']);
                                }
                                if (isset($concepto['cuenta_cobrar'])) {
                                    if (isset($concepto['cuenta_cobrar']['_id'])) {
                                        $data['items'][$i]['conceptos'][$j]['cuenta_cobrar']['_id'] = new MongoId($concepto['cuenta_cobrar']['_id']);
                                    }
                                    if (isset($concepto['cuenta_cobrar']['servicio']['_id'])) {
                                        $data['items'][$i]['conceptos'][$j]['cuenta_cobrar']['servicio']['_id'] = new MongoId($concepto['cuenta_cobrar']['servicio']['_id']);
                                    }
                                }
                            }
                            break;
                        case 'agua_chapi':
                            //EL METODO DIFERENCIA SE APLICARA, POR LO QUE EL VALOR_VENTA SE ENVIARA
                            $data['items'][$i]['valor_venta'] = round($item['valor_unitario']*$item['cant'], 2);

                            foreach ($item['conceptos'] as $j => $concepto) {
                                if (isset($concepto['cuenta']['_id'])) {
                                    if (is_string($concepto['cuenta']['_id'])) {
                                        $data['items'][$i]['conceptos'][$j]['cuenta']['_id'] = new MongoId($concepto['cuenta']['_id']);
                                        unset($data['items'][$i]['conceptos'][$j]['cuenta']['tipo']);
                                        unset($data['items'][$i]['conceptos'][$j]['cuenta']['cuentas']);
                                        unset($data['items'][$i]['conceptos'][$j]['cuenta']['fecreg']);
                                    }
                                }

                                if (isset($concepto['producto']['_id'])) {
                                    $data['items'][$i]['conceptos'][$j]['producto']['_id'] = new MongoId($concepto['producto']['_id']);
                                }

                                if (isset($concepto['item'])) {
                                    $data['items'][$i]['conceptos'][$j]['item'] = floatval($concepto['item']);
                                }

                                if (isset($concepto['parent'])) {
                                    $data['items'][$i]['conceptos'][$j]['parent'] = floatval($concepto['parent']);
                                }

                                if (isset($concepto['monto'])) {
                                    $data['items'][$i]['conceptos'][$j]['monto'] = floatval($concepto['monto']);
                                }

                                if (isset($concepto['igv'])) {
                                    $data['items'][$i]['conceptos'][$j]['igv'] = floatval($concepto['igv']);
                                }

                                if (isset($concepto['cant'])) {
                                    $data['items'][$i]['conceptos'][$j]['cant'] = floatval($concepto['cant']);
                                }

                                if (isset($concepto['valor_unitario'])) {
                                    $data['items'][$i]['conceptos'][$j]['valor_unitario'] = floatval($concepto['valor_unitario']);
                                }
                            }
                            break;
                        case 'farmacia':
                            foreach ($item['conceptos'] as $j => $concepto) {
                                if (isset($concepto['cuenta']['_id'])) {
                                    if (is_string($concepto['cuenta']['_id'])) {
                                        $data['items'][$i]['conceptos'][$j]['cuenta']['_id'] = new MongoId($concepto['cuenta']['_id']);
                                        unset($data['items'][$i]['conceptos'][$j]['cuenta']['tipo']);
                                        unset($data['items'][$i]['conceptos'][$j]['cuenta']['cuentas']);
                                        unset($data['items'][$i]['conceptos'][$j]['cuenta']['fecreg']);
                                    }
                                }

                                if (isset($concepto['producto']['_id'])) {
                                    $data['items'][$i]['conceptos'][$j]['producto']['_id'] = new MongoId($concepto['producto']['_id']);
                                }

                                if (isset($concepto['item'])) {
                                    $data['items'][$i]['conceptos'][$j]['item'] = floatval($concepto['item']);
                                }

                                if (isset($concepto['parent'])) {
                                    $data['items'][$i]['conceptos'][$j]['parent'] = floatval($concepto['parent']);
                                }

                                if (isset($concepto['monto'])) {
                                    $data['items'][$i]['conceptos'][$j]['monto'] = floatval($concepto['monto']);
                                }

                                if (isset($concepto['igv'])) {
                                    $data['items'][$i]['conceptos'][$j]['igv'] = floatval($concepto['igv']);
                                }

                                if (isset($concepto['cant'])) {
                                    $data['items'][$i]['conceptos'][$j]['cant'] = floatval($concepto['cant']);
                                }

                                if (isset($concepto['valor_unitario'])) {
                                    $data['items'][$i]['conceptos'][$j]['valor_unitario'] = floatval($concepto['valor_unitario']);
                                }

                                #SOLUCION PARCHE
                                if (isset($concepto['prec_comp'])) {
                                    //$data['items'][$i]['valor_venta'] = floatval($concepto['monto']);
                                    unset($data['items'][$i]['conceptos'][$j]['prec_comp']);
                                }
                            }
                            break;
                        case 'servicio':
                            foreach ($item['conceptos'] as $j => $concepto) {
                                if (isset($concepto['cuenta']['_id'])) {
                                    if (is_string($concepto['cuenta']['_id'])) {
                                        $data['items'][$i]['conceptos'][$j]['cuenta']['_id'] = new MongoId($concepto['cuenta']['_id']);
                                        unset($data['items'][$i]['conceptos'][$j]['cuenta']['tipo']);
                                        unset($data['items'][$i]['conceptos'][$j]['cuenta']['cuentas']);
                                        unset($data['items'][$i]['conceptos'][$j]['cuenta']['fecreg']);
                                    }
                                }

                                if (isset($concepto['_id'])) {
                                    $data['items'][$i]['conceptos'][$j]['_id'] = new MongoId($concepto['_id']);
                                }

                                if (isset($concepto['servicio']['_id'])) {
                                    $data['items'][$i]['conceptos'][$j]['servicio']['_id'] = new MongoId($concepto['servicio']['_id']);
                                }

                                if (isset($concepto['item'])) {
                                    $data['items'][$i]['conceptos'][$j]['item'] = floatval($concepto['item']);
                                }

                                if (isset($concepto['parent'])) {
                                    $data['items'][$i]['conceptos'][$j]['parent'] = floatval($concepto['parent']);
                                }

                                if (isset($concepto['monto'])) {
                                    $data['items'][$i]['conceptos'][$j]['monto'] = floatval($concepto['monto']);
                                }

                                if (isset($concepto['igv'])) {
                                    $data['items'][$i]['conceptos'][$j]['igv'] = floatval($concepto['igv']);
                                }
                            }
                            break;
                    }
                }
            }
        }

        if (isset($data['total_isc'])) {
            $data['total_isc'] = floatval($data['total_isc']);
        }
        if (isset($data['total_igv'])) {
            $data['total_igv'] = floatval($data['total_igv']);
        }
        if (isset($data['total_otros_tributos'])) {
            $data['total_otros_tributos'] = floatval($data['total_otros_tributos']);
        }
        if (isset($data['total_otros_cargos'])) {
            $data['total_otros_cargos'] = floatval($data['total_otros_cargos']);
        }
        if (isset($data['total_ope_inafectas'])) {
            $data['total_ope_inafectas'] = floatval($data['total_ope_inafectas']);
        }
        if (isset($data['total_ope_gravadas'])) {
            $data['total_ope_gravadas'] = floatval($data['total_ope_gravadas']);
        }
        if (isset($data['total_desc'])) {
            $data['total_desc'] = floatval($data['total_desc']);
        }
        if (isset($data['total_ope_exoneradas'])) {
            $data['total_ope_exoneradas'] = floatval($data['total_ope_exoneradas']);
        }
        if (isset($data['total_ope_gratuitas'])) {
            $data['total_ope_gratuitas'] = floatval($data['total_ope_gratuitas']);
        }
        if (isset($data['total'])) {
            $data['total'] = floatval($data['total']);
        }
        if (isset($data['total_detraccion'])) {
            $data['total_detraccion'] = floatval($data['total_detraccion']);
        }
        if (isset($data['porcentaje_detraccion'])) {
            $data['porcentaje_detraccion'] = floatval($data['porcentaje_detraccion']);
        }

        //Direccion de local
        if (!isset($data['establecimiento_anexo'])) {
            $data['establecimiento_anexo'] = "0012";
            //Filtrado por serie de comprobante
            switch ($data['serie']) {
                case 'B001': //Alquiler de Inmuebles
                    $data['establecimiento_anexo'] = "0012";    //AV. GOYONECHE 339
                    break;
                case 'F001': //Alquiler de Inmuebles
                    $data['establecimiento_anexo'] = "0012";    //AV. GOYONECHE 339
                    break;
                case 'B002': //Venta de Farmacia
                    $data['establecimiento_anexo'] = "0005";    //URB. CERRO COLORADO AV. PUMACAHUA Km S N
                    break;
                case 'F002': //Venta de Farmacia
                    $data['establecimiento_anexo'] = "0005";    //URB. CERRO COLORADO AV. PUMACAHUA Km S N
                    break;
                case 'B003': //Venta de botellas de Agua
                    $data['establecimiento_anexo'] = "0012";    //AV. GOYONECHE 339
                    break;
                case 'F003': //Venta de botellas de Agua
                    $data['establecimiento_anexo'] = "0012";    //AV. GOYONECHE 339
                    break;
                case 'B004':
                    switch ($f->session->userDB['_id'] -> {'$id'}) {
                        #EN CASO DE LAS PLAYAS CAMBIARA EL ESTABLECIMIENTO ANEXO POR EL AUTOR DE LA PLAYA, ESTO SE HACE YA QUE CADA AUTOR REPRESENTA UNA PLAYA.
                        # "_id":ObjectId("5a5cbea13e603742398b456a"), PLAYA FILTRO EXTERIOR CON CAL. EL FILTRO 500 Y ESTABLECIMIENTO 0022,
                        # "_id":ObjectId("5a71df613e603745448b4568"), PLAYA LA PAZ CON AV. LA PAZ 511 Y ESTABLECIMIENTO 0027,
                        # "_id":ObjectId("5a71dead3e603728448b4568"), PLAYA PAUCARPATA CON CAL. PAUCARPATA -- Y ESTABLECIMIENTO 0026,
                        # "_id":ObjectId("5a5cbecf3e603748398b4568"), PLAYA SANTA FE NO SE ENCONTROL ESTABLECIMIENTO ANEXO, SE ASUME AV GOYENECHE 339,
                        # "_id":ObjectId("5a5cbf3b3e603758398b456e"), PLAYA PIEROLA EXTERIOR CON CAL. PIEROLA 203 Y ESTABLECIMIENTO 0025,
                        # "_id":ObjectId("5a5cbe3e3e603734398b4568"), PLAYA PIEROLA SOTANO CON CAL. PIEROLA 205 Y ESTABLECIMIENTO 0023,
                        case '5a5cbea13e603742398b456a':
                            $data['establecimiento_anexo'] = "0022";    //CAL. EL FILTRO 500
                            break;
                        case '5a71df613e603745448b4568':
                            $data['establecimiento_anexo'] = "0027";    //AV. LA PAZ 511
                            break;
                        case '5a71dead3e603728448b4568':
                            $data['establecimiento_anexo'] = "0026";    //CAL. PAUCARPATA --
                            break;
                        case '5a5cbecf3e603748398b4568':
                            $data['establecimiento_anexo'] = "0012";    //PLAYA SANTA FE -- NO ENCONTRE DOMICILIO FISCAL
                            break;
                        case '5a5cbf3b3e603758398b456e':
                            $data['establecimiento_anexo'] = "0025";    //CAL. PIEROLA 203
                            break;
                        case '5a5cbe3e3e603734398b4568':
                            $data['establecimiento_anexo'] = "0023";    //CAL. PIEROLA 205
                            break;
                    }
                    break;
                case 'F004':
                    switch ($f->session->userDB['_id'] -> {'$id'}) {
                        #EN CASO DE LAS PLAYAS CAMBIARA EL ESTABLECIMIENTO ANEXO POR EL AUTOR DE LA PLAYA, ESTO SE HACE YA QUE CADA AUTOR REPRESENTA UNA PLAYA.
                        # "_id":ObjectId("5a5cbea13e603742398b456a"), PLAYA FILTRO EXTERIOR CON CAL. EL FILTRO 500 Y ESTABLECIMIENTO 0022,
                        # "_id":ObjectId("5a71df613e603745448b4568"), PLAYA LA PAZ CON AV. LA PAZ 511 Y ESTABLECIMIENTO 0027,
                        # "_id":ObjectId("5a71dead3e603728448b4568"), PLAYA PAUCARPATA CON CAL. PAUCARPATA -- Y ESTABLECIMIENTO 0026,
                        # "_id":ObjectId("5a5cbecf3e603748398b4568"), PLAYA SANTA FE NO SE ENCONTROL ESTABLECIMIENTO ANEXO, SE ASUME AV GOYENECHE 339,
                        # "_id":ObjectId("5a5cbf3b3e603758398b456e"), PLAYA PIEROLA EXTERIOR CON CAL. PIEROLA 203 Y ESTABLECIMIENTO 0025,
                        # "_id":ObjectId("5a5cbe3e3e603734398b4568"), PLAYA PIEROLA SOTANO CON CAL. PIEROLA 205 Y ESTABLECIMIENTO 0023,
                        case '5a5cbea13e603742398b456a':
                            $data['establecimiento_anexo'] = "0022";    //CAL. EL FILTRO 500
                            break;
                        case '5a71df613e603745448b4568':
                            $data['establecimiento_anexo'] = "0027";    //AV. LA PAZ 511
                            break;
                        case '5a71dead3e603728448b4568':
                            $data['establecimiento_anexo'] = "0026";    //CAL. PAUCARPATA --
                            break;
                        case '5a5cbecf3e603748398b4568':
                            $data['establecimiento_anexo'] = "0012";    //PLAYA SANTA FE -- NO ENCONTRE DOMICILIO FISCAL
                            break;
                        case '5a5cbf3b3e603758398b456e':
                            $data['establecimiento_anexo'] = "0025";    //CAL. PIEROLA 203
                            break;
                        case '5a5cbe3e3e603734398b4568':
                            $data['establecimiento_anexo'] = "0023";    //CAL. PIEROLA 205
                            break;
                    }
                    break;
            }
        }

        //$f->model('cj/ecom')->params(array('data'=>$data))->save('insert');
        //print_r($data);
        $ConfluxSee = new ConfluxSee();
        //JSON to PHP: Cuando la data viene codificada en base64 la convertimos a un arreglo PHP
        $serialized_data = $data;
        //Validacion Conflux: validamos la informacion de acuerdo a la documentacion API REST
        //print_r($serialized_data);
        $ConfluxSee->init($serialized_data);
        //print_r($ConfluxSee->getOutput());
        //$this->set_response($model, REST_Controller::HTTP_OK);
        $conflux_output = $ConfluxSee->getOutput();
        //if(isset($data['fecemi'])) $data['fecemi'] = new MongoDate(strtotime($data['fecemi']));
        //if(isset($data['fecven'])) $data['fecven'] = new MongoDate(strtotime($data['fecven']));

        if (isset($data['caja']['modulo']) && $data['caja']['modulo']==='PA'){
          $data['fecemi'] = (isset($data['fecemi'])) ? new MongoDate(strtotime($data['fecemi'])) : new MongoDate(strtotime(date('Y-m-d'))) ;
          $data['fecven'] = (isset($data['fecven'])) ? new MongoDate(strtotime($data['fecven'])) : new MongoDate(strtotime(date('Y-m-d'))) ;
        }else{
          $data['fecemi'] = new MongoDate(strtotime(date('Y-m-d')));
          $data['fecven'] = new MongoDate(strtotime(date('Y-m-d')));
        }
        if ($conflux_output['status']=='success') {
            $insert_document = $data;
            if (!isset($f->request->data['_id'])) {
                $insert_document['_id'] = new MongoId();
                $insert_document['fecreg'] = new MongoDate();
                $insert_document['autor'] = $f->session->userDB;
                $insert_document['estado']='BO';
                $insert_document['estado_resumen']='';
                $insert_document['estado_baja']='';
                $insert_document['sunat_note']='';
                $insert_document['sunat_description']='';
                $insert_document['sunat_responsecode']='';
                $insert_document['sunat_faultcode']='';
                $insert_document['sunat_soap_error']='';
                $insert_document['digest_value']='';
                $insert_document['signature_value']='';
                $insert_document['codigo_barras']='';
                $insert_document['codigo_barras_pdf']='';
                $insert_document['ruta_zip_firmado']='';
                $insert_document['ruta_xml_firmado']='';
                $insert_document['ruta_cdr_xml']='';
                $insert_document['ruta_pdf']='';

                $f->model("cj/ecom")->params(array('data'=>$insert_document))->save("insert")->items;
                $f->model('ac/log')->params(array(
                    'modulo'=>'CJ',
                    'bandeja'=>'Pago de comprobante electr&oacute;nico',
                    'descr'=>'Se cre&oacute; el borrador de comprobante electr&oacute;nico de la serie <b>'.$data['serie'].'</b> en la caja <b>'.$data['caja']['nomb'].'</b>.'
                ))->save('insert');
            } else {
                $insert_document['_id'] = new MongoId($f->request->data['_id']);
                $insert_document['fecmod'] = new MongoDate();
                $insert_document['estado']='BO';
                $insert_document['autor_modifcacion'] = $f->session->userDB;
                $f->model("cj/ecom")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$insert_document))->save("update");
                $f->model('ac/log')->params(array(
                    'modulo'=>'CJ',
                    'bandeja'=>'Pago de comprobante electr&oacute;nico',
                    'descr'=>'Se actualiz&oacute; el borrador de comprobante electr&oacute;nico de la serie <b>'.$data['serie'].'</b> en la caja <b>'.$data['caja']['nomb'].'</b>.'
                ))->save('insert');
            }
            /***************************************
            * APROBADO POR USUARIO
            ***************************************/
            $response['document_ecom']=$insert_document;
            if (isset($insert_document['_id'])) {
                $response['status'] = 'success';
                $response['message'] = 'El comprobante fue guardado correctamente';
                $response['data']['id'] = $insert_document['_id']->{'$id'};
                $response['data']['estado'] = $insert_document['estado'];
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Ha ocurrido un error al intentar guardar el comprobante en nuestra base de datos, contactese inmediatamente a contacto@conflux.pe';
            }
        } else {
            $response['message'] = $conflux_output['error_message'];
        }
        $f->response->json($response);
    }
    public function execute_save_pago()
    {
        global $f;
        $data = $f->request->data;
        $trabajador = $f->session->userDB;
        $response = array(
            'status'=>'error',
            'message'=>'',
            'data'=>array(
                'id'=>'',
            )
        );
        if (isset($data['efectivos'])) {
            foreach ($data['efectivos'] as $i=>$ef) {
                $data['efectivos'][$i]['monto'] = floatval($ef['monto']);
            }
        }
        if (isset($data['vouchers'])) {
            foreach ($data['vouchers'] as $i=>$vou) {
                $data['vouchers'][$i]['monto'] = floatval($vou['monto']);
                $data['vouchers'][$i]['cuenta_banco']['_id'] = new MongoId($vou['cuenta_banco']['_id']);
            }
        }
        $insert_document = $data;
        $insert_document['_id'] = new MongoId($f->request->data['_id']);
        $insert_document['fecmod'] = new MongoDate();
        $insert_document['autor_modifcacion'] = $f->session->userDB;
        $f->model("cj/ecom")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$insert_document))->save("update");

        if (isset($insert_document['_id'])) {
            $response['status'] = 'success';
            $response['message'] = 'El comprobante fue guardado correctamente';
            $response['data']['id'] = $insert_document['_id']->{'$id'};
        } else {
            $response['status'] = 'error';
            $response['message'] = 'No se envio el identificador del comprobante ';
        }
        $f->response->json($response);
    }
    public function execute_confirmar()
    {
        global $f;
        $data = $f->request->data;
        $model = $f->model('cj/ecom')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;
        if ($model!=null) {
            if ($model['serie'][0]=="F" || $model['serie'][0]=="B" || $model['serie'][0]=="NC" || $model['serie'][0]=="ND") {
                $this->execute_confirmar_electronico($data);
            } else {
                $this->execute_confirmar_manual($data);
            }
        }
    }
    /**
    *   FUNCION QUE COMUNICA EL COMPROBANTE ELECTRONICO AL SERVIDOR DE FACTURACION
    */
    public function execute_confirmar_electronico($data)
    {
        global $f;
        //$data = $f->request->data;
        $ConfluxSee = new ConfluxSee();
        $response = array(
            'status'=>'error',
            'message'=>'',
            'data'=>array(
                'id'=>'',
                'estado'=>'',
                'estado_resumen'=>'',
                'estado_baja'=>'',
                'sunat_note'=>'',
                'sunat_description'=>'',
                'sunat_responsecode'=>'',
                'sunat_faultcode'=>'',
                'sunat_soap_error'=>'',
                'digest_value'=>'',
                'signature_value'=>'',
                'codigo_barras'=>'',
                'codigo_barras_image'=>'',
                'ruta_zip_firmado'=>'',
                'ruta_xml_firmado'=>'',
                'ruta_cdr_xml'=>'',
                'ruta_pdf'=>''
            )
        );
        if (isset($data['_id'])) {
            $document = $f->model('cj/ecom')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;
            $document['horemi'] = date('H:i:s');
            /**
            *   FORMATEAR EL COMPROBANTE PARA SER ENVIADO
            *   - NO DEBE INCLUIR CLAVES CON NOMBRE '$id' (Objetos MongoId)
            *   - NO DEBERIA ENVIARSE 'OBJETOS', SINO FORZARLOS A SER ARRAY
            */
            #formatting to send rest-server
            $document['fecemi'] = date('Y-m-d', $document['fecemi']->sec);
            $document['fecven'] = date('Y-m-d', $document['fecven']->sec);
            $document['metadata'] = array();
            #Hora de un servidor NTP
            //$document['metadata']['hour'] = date('H:i:s', $f->model("mg/wser")->get("timestamp")->items);
            if (isset($document['efectivos'])) {
                $document['metadata']['efectivos']=$document['efectivos'];
            }
            if (isset($document['vouchers'])) {
                //$document['metadata']['vouchers']=$document['vouchers'];
                foreach ($document['vouchers'] as $i=>$vouch) {
                    if (isset($vouch['cuenta_banco'])) {
                        unset($document['vouchers'][$i]['cuenta_banco']);
                    }
                }
                $document['metadata']['vouchers']=$document['vouchers'];
            }
            $items = array();
            if (count($document['items'])) {
                foreach ($document['items'] as $item) {
                    if (isset($item['conceptos'])) {
                        # CONCEPTOS
                        unset($item['conceptos']);
                    }
                    $items[] = $item;
                    if (isset($item['subitems'])) {
                        if (count($item['subitems'])>0) {
                            foreach ($item['subitems'] as $subitem) {
                                $items[] = $subitem;
                            }
                        }
                    }
                }
            }
            $document['items'] = $items;
            #A PARTIR DEL 06-03-2018 SE LE HACE UNSET DE CAMPOS INNECESARIOS QUE SERAN ENVIADO AL SEE
            #Estos seran enviados por el SEE mediante el campo metadata

            //$document['metadata']=array(
            //'caja'=>$document['caja'],
            //'autor'=>$document['autor'],
            //);

            if (isset($document['_id'])) {
                unset($document['_id']);
            }
            if (isset($document['caja'])) {
                unset($document['caja']);
            }
            if (isset($document['autor'])) {
                unset($document['autor']);
            }

            if (isset($document['autor_modifcacion'])) {
                //$document['metadata']['autor_modificacion']=$document['autor_modificacion'];
                unset($document['autor_modifcacion']);
            }
            if (isset($document['fecmod'])) {
                //$document['metadata']['fecmod']=$document['fecmod'];
                unset($document['fecmod']);
            }
            if (isset($document['fecreg'])) {
                //$document['metadata']['fecreg']=$document['fecreg'];
                unset($document['fecreg']);
            }
            //unset($document['metadata']);


            /**
            * ENVIAR EL DOCUMENTO MEDIANTE LA URL
            */

            $curl_handle = curl_init();
            //curl_setopt($curl_handle, CURLOPT_URL, 'http://916426f5.ngrok.io/index.php/api/documents/invoice/format/json/');
            //curl_setopt($curl_handle, CURLOPT_URL, 'http://35.193.115.148/index.php/api/documents/invoice/format/json/');
            curl_setopt($curl_handle, CURLOPT_URL, 'https://einvoice.conflux.pe/index.php/api/documents/invoice/format/json/');
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl_handle, CURLOPT_POST, 1);
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
                'Authorization: Token xfNxKIW6BjaCTS2CbeEhltuK2X6iIhWL'
            ));
            //$request = $this->input->post(NULL,TRUE);
            //$request['fecreg'] = strtotime($request['fecreg']);
            //print_r($document);
            $data_to_rest = json_encode($document);
            $data_base64 = base64_encode($data_to_rest);
            /*if(isset($request['enviar']))
                curl_setopt($curl_handle, CURLOPT_POSTFIELDS,"data=".$data_base64."&enviar=1");
            else*/
            curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "data=".$data_base64."&enviar=1");
            $buffer = curl_exec($curl_handle);
            if ($buffer===false) {
                curl_close($curl_handle);
                $response['status'] = 'error';
                $response['message'] = 'El servidor no respondio a la solicitud enviada!';
            } else {
                curl_close($curl_handle);
                $result = json_decode($buffer, true);
                if ($result === null && json_last_error() !== JSON_ERROR_NONE) {
                    $response['status'] = 'error';
                    $response['message'] = 'El servidor dio un error inesperado que no puede ser parseado!';
                    $response['data'] = $buffer;
                } else {
                    if (isset($result['status']) && isset($result['data'])) {
                        if ($result['status'] != "error") {
                            $update_documento['numero']=$result['data']['numero'];
                            //$update_documento['horemi']=$result['data']['horemi'];
                            //$update_documento['codigo_barras']=$result['data']['codigo_barras'];
                            //$update_documento['ruta_zip_firmado']=$result['data']['ruta_zip_firmado'];
                            //$update_documento['signature_value']=$result['data']['signature_value'];
                            //$update_documento['ruta_pdf']=$result['data']['ruta_pdf'];
                            //$update_documento['ruta_xml_firmado']=$result['data']['ruta_xml_firmado'];
                            //$update_documento['metadata_respuesta']=$result['data']['metadata'];

                            //SE DEDCIDIO NO GUARDAR ESTE CAMPO YA QUE ES UN STRING EN BASE64 DE LA IMAGEN DEL CODIGO DE BARRAS
                            //$update_documento['codigo_barras_pdf']=$result['data']['codigo_barras_pdf'];
                            //$update_documento['supplier']=$result['data']['supplier'];
                            $update_documento['estado']="FI";
                            $update_documento['digest_value']=$result['data']['digest_value'];
                            $update_documento['conflux_see_id']=$result['data']['_id'];

                            $update_documento['tipo_comprobante']='ELECTRONICO';
                            $update_documento['feccon']=new MongoDate();
                            $update_documento['autor_con']=$f->session->userDB;
                            $f->model('cj/ecom')->params(array('_id'=>new MongoId($data['_id']),'data'=>$update_documento))->save('update');

                            //EN CASO DE QUE LA FIRMA SEA EXITOSA APLICAR LOS CAMBIOS
                            $document = $f->model('cj/ecom')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;
                            if (isset($document['items']) && ($document['tipo']!="NC" || $document['tipo']!="ND")) {
                                foreach ($document['items'] as $i => $item) {
                                    if (isset($item['conceptos'])) {
                                        foreach ($item['conceptos'] as $c => $concepto) {
                                            /*************************************************************************************************
                                            * EN CASO SEA UN PAGO DE ALQUILER DE INMUEBLES
                                            *************************************************************************************************/
                                            if (isset($concepto['alquiler'])) {
                                                if (!isset($concepto['parent'])) {
                                                    $tmp_pay=array();
                                                    $tmp_pago=array();
                                                    $tmp_parent=array();
                                                    $contrato = $f->model('in/cont')->params(array(
                                                        '_id'=>$concepto['alquiler']['contrato'],
                                                    ))->get('one')->items;
                                                    $tmp_parent['parent']=$concepto['item'];
                                                    $tmp_pay['alquiler']=$concepto['monto'];
                                                    $tmp_pago['pago']=$concepto['pago'];
                                                } else {
                                                    if ($concepto['item']-$tmp_parent['parent']==1) {
                                                        $tmp_pay['igv']=$concepto['monto'];
                                                    }
                                                    #SE CORRIGIO UN ERROR EN EL CUAL NO SE COLOCA EN EL STRING LAS MORAS 06/02/2018
                                                    //if($concepto['item']-$tmp_parent['parent']==2) $tmp_pay['monto']=$concepto['monto'];
                                                    if ($concepto['item']-$tmp_parent['parent']==2) {
                                                        $tmp_pay['moras']=$concepto['monto'];
                                                    }
                                                    if (isset($item['conceptos'][$c+1]) || count($item['conceptos'])==($c+1)) {
                                                        if (!isset($item['conceptos'][$c+1]['parent']) || count($item['conceptos'])==($c+1)) {
                                                            foreach ($contrato['pagos'] as $kp=>$pago) {
                                                                if ($pago['mes']==$tmp_pago['pago']['mes']&&$pago['ano']==$tmp_pago['pago']['ano']) {
                                                                    /***********************************************************
                                                                    * EN CASO DE SER UN REGISTRO DE COBRO COMPLETO
                                                                    ***********************************************************/
                                                                    if (isset($item['tipo']) && ($item['tipo']=="pago_meses")) {
                                                                        $f->model('in/cont')->params(array(
                                                                            '_id'=>$contrato['_id'],
                                                                            'data'=>array(
                                                                                'pagos.'.$kp.'.estado'=>'C',
                                                                                'pagos.'.$kp.'.comprobante'=>array(
                                                                                    '_id'=>$document['_id'],
                                                                                    'tipo'=>$document['tipo'],
                                                                                    'serie'=>$document['serie'],
                                                                                    'num'=>$document['numero']
                                                                                ),
                                                                                'pagos.'.$kp.'.item_c'=>$i,
                                                                                'pagos.'.$kp.'.detalle'=>$tmp_pay
                                                                            )
                                                                        ))->save('update');
                                                                    }
                                                                    /***********************************************************
                                                                    * EN CASO DE SER UN REGISTRO DE COBRO PARCIAL
                                                                    ***********************************************************/
                                                                    elseif (isset($item['tipo']) && ($item['tipo']=="pago_parcial")) {
                                                                        $estado_tmp = 'P';
                                                                        //$total_tmp = floatval($item['conceptos'][$tmp_parent['parent']]['monto']);
                                                                        $total_tmp = floatval($item['conceptos'][0]['monto']);
                                                                        if (isset($pago['total'])) {
                                                                            $total_tmp += floatval($pago['total']);
                                                                        }
                                                                        if ($total_tmp==floatval($contrato['importe'])) {
                                                                            $estado_tmp = 'C';
                                                                        }
                                                                        $f->model('in/cont')->params(array(
                                                                            '_id'=>$contrato['_id'],
                                                                            'data'=>array(
                                                                                'pagos.'.$kp.'.estado'=>$estado_tmp,
                                                                                'pagos.'.$kp.'.total'=>$total_tmp,
                                                                                'pagos.'.$kp.'.item_c'=>$i
                                                                            )
                                                                        ))->save('update');
                                                                        if (isset($pago['comprobante'])) {
                                                                            $pago['comprobante']['detalle'] = $pago['detalle'];
                                                                            $f->model('in/cont')->params(array(
                                                                                '_id'=>$contrato['_id'],
                                                                                'data'=>array('$push'=>
                                                                                    array(
                                                                                        'pagos.'.$kp.'.comprobantes'=>$pago['comprobante']
                                                                                    )
                                                                                )
                                                                            ))->save('custom');
                                                                            $f->model('in/cont')->params(array(
                                                                                '_id'=>$contrato['_id'],
                                                                                'data'=>array('$unset'=>
                                                                                    array(
                                                                                        'pagos.'.$kp.'.detalle'=>true,
                                                                                        'pagos.'.$kp.'.comprobante'=>true
                                                                                    )
                                                                                )
                                                                            ))->save('custom');
                                                                            $estado_tmp = 'P';
                                                                            //$total_tmp = floatval($item['conceptos'][$tmp_parent['parent']]['monto']);
                                                                            $total_tmp = floatval($item['conceptos'][0]['monto']);
                                                                            if (isset($pago['detalle']['alquiler'])) {
                                                                                $total_tmp += floatval($pago['detalle']['alquiler']);
                                                                            }
                                                                            if ($total_tmp==floatval($contrato['importe'])) {
                                                                                $estado_tmp = 'C';
                                                                            }
                                                                            $f->model('in/cont')->params(array(
                                                                                '_id'=>$contrato['_id'],
                                                                                'data'=>array(
                                                                                    'pagos.'.$kp.'.estado'=>$estado_tmp,
                                                                                    'pagos.'.$kp.'.total'=>$total_tmp,
                                                                                    'pagos.'.$kp.'.item_c'=>$i
                                                                                )
                                                                            ))->save('update');
                                                                        }
                                                                        /***********************************************************
                                                                        * EN CASO DE SER HISTORICO
                                                                        ***********************************************************/
                                                                        elseif (isset($pago['historico'])) {
                                                                            #NO ENCONTRE CODIGO QUE GENERE PAGOS EN LOS HISTORICOS
                                                                        }

                                                                        $f->model('in/cont')->params(array(
                                                                            '_id'=>$contrato['_id'],
                                                                            'data'=>array('$push'=>
                                                                                array(
                                                                                    'pagos.'.$kp.'.comprobantes'=>array(
                                                                                        '_id'=>$document['_id'],
                                                                                        'tipo'=>$document['tipo'],
                                                                                        'serie'=>$document['serie'],
                                                                                        'num'=>$document['numero'],
                                                                                        'detalle'=>$tmp_pay
                                                                                    )
                                                                                )
                                                                            )
                                                                        ))->save('custom');
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            /*************************************************************************************************
                                            * EN CASO SEA UN PAGO DE ACTA DE CONCILIACION DE INMUEBLES
                                            *************************************************************************************************/
                                            if (isset($concepto['acta_conciliacion'])) {
                                                $acta = $f->model('in/acta')->params(array('_id'=>$concepto['acta_conciliacion']))->get('one')->items;
                                                foreach ($acta['items'] as $kp=>$pago) {
                                                    if ($pago['num']==$concepto['pago']['num']) {
                                                        $f->model('in/acta')->params(array(
                                                            '_id'=>$acta['_id'],
                                                            'data'=>array(
                                                                'items.'.$kp.'.estado'=>'C',
                                                                'items.'.$kp.'.comprobante'=>array(
                                                                    '_id'=>$document['_id'],
                                                                    'tipo'=>$document['tipo'],
                                                                    'serie'=>$document['serie'],
                                                                    'num'=>$document['numero']
                                                                )
                                                            )
                                                        ))->save('update');
                                                    }
                                                }
                                            }
                                            /*************************************************************************************************
                                            * EN CASO SEA UNA CUENTA POR COBRAR
                                            *************************************************************************************************/
                                            if (isset($concepto['cuenta_cobrar'])) {
                                                if ($item['tipo']=="cuenta_cobrar") {
                                                    $total = 0;
                                                    if (!isset($concepto['parent'])) {
                                                        $f->model('cj/cuen')->params(array(
                                                            '_id'=>$concepto['cuenta_cobrar']['_id'],
                                                            'data'=>array('$push'=>array('comprobantes'=>$document['_id']))
                                                        ))->save('custom');
                                                    }
                                                    $cuenta = $f->model('cj/cuen')->params(array('_id'=>$concepto['cuenta_cobrar']['_id']))->get('one')->items;
                                                    $f->model('cj/cuen')->params(array(
                                                        '_id'=>$concepto['cuenta_cobrar']['_id'],
                                                        'data'=>array('$set'=>array(
                                                            'estado'=>'C',
                                                            'saldo'=>0,
                                                        ))
                                                    ))->save('custom');
                                                }
                                            }
                                            /************************************************************************************************
                                            * EN CASO SEA UNA COMPRA DE FARMACIA (KARDEX)
                                            ************************************************************************************************/
                                            if (isset($item['tipo']) && $item['tipo']=="farmacia") {
                                                if (isset($concepto['producto'])) {
                                                    $producto_get = $f->model('lg/prod')->params(array('_id'=>$concepto['producto']['_id']))->get('one')->items;
                                                    # EL UNICO ALMACEN DE FARMACIA
                                                    $alma=$f->model("lg/alma")->get("farmacia")->items;
                                                    if (isset($concepto['alamacen'])) {
                                                        $concepto['alamacen']['_id'] = new MongoId($concepto['alamacen']['_id']);
                                                        $alma = $f->model("lg/alma")->params(array("_id"=>$concepto['alamacen']['_id']))->get("one")->items;
                                                        $id_almacen=$alma['_id'];
                                                    } else {
                                                        $id_almacen = $alma['_id'];
                                                    }

                                                    #Quitar variables que no serviran
                                                    unset($alma['descr']);
                                                    unset($alma['fecmod']);
                                                    unset($alma['trabajador']);
                                                    unset($alma['fecreg']);
                                                    unset($alma['autor']);
                                                    unset($alma['estado']);
                                                    unset($alma['aplicacion']);

                                                    $stock = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$id_almacen,"producto"=>$concepto['producto']['_id'])))->get('one_custom')->items;
                                                    if ($stock==null) {
                                                        $stock = array(
                                                            '_id'=>new MongoId(),
                                                            'producto'=>$concepto['producto']['_id'],
                                                            'almacen'=>$id_almacen,
                                                            'stock'=>0,
                                                            'costo'=>0
                                                        );
                                                        $f->model('lg/stck')->params(array('data'=>$stock))->save('insert');
                                                    }

                                                    /************************************************************************************
                                                    *   OBTENER ULTIMO SALDO DEL MOVIMIENTO
                                                    ************************************************************************************/
                                                    #ALMACEN
                                                    $fecreg = new MongoDate();
                                                    $temp=array('_id'=>$id_almacen);
                                                    $nomb_almacen = $alma['nomb'];

                                                    //$saldo = $f->model("lg/movi")->params(array(
                                                    //    'filter'=>array(
                                                    //        'stock'=>$stock['_id']
                                                    //    ),
                                                    //    'sort'=>array(
                                                    //        'fecreg'=>-1)
                                                    //))->get('custom')->items;
                                                    $saldo = $f->model("lg/movi")->params(array(
                                                        'filter'=>array(
                                                            'stock'=>$stock['_id'],
                                                            'almacen._id'=>$id_almacen,
                                                            'producto._id'=>$producto_get['_id'],
                                                            'modulo'=>'FA',
                                                        ),
                                                        'sort'=>array(
                                                            'fecreg'=>-1
                                                        )
                                                    ))->get('custom')->items;

                                                    $saldo_cant = 0;
                                                    $saldo_monto = 0;
                                                    if ($saldo!=null) {
                                                        if (count($saldo)>0) {
                                                            $saldo_cant = $saldo[0]['saldo'];
                                                            $saldo_monto = $saldo[0]['saldo_imp'];
                                                        }
                                                    }

                                                    $stock_actual = floatval($saldo_cant);
                                                    $precio_unitario=$producto_get['precio'];

                                                    //$lote = $f->model('fa/lote')->params(array(
                                                    //    'producto'=>$concepto['producto']['_id']
                                                    //))->get('lote')->items;
                                                    /************************************************************
                                                    *   REGISTRAR EL MOVIMIENTO DE SALIDA-> KARDEX
                                                    ************************************************************/
                                                    /*COMO FUNCIONA LOS MOVIMIENTOS*/
                                                    //ENTRADA FISICO O SALIDA FISICO = cant
                                                    //ENTRADA VALORADO O SALIDA VALORADO = total
                                                    //PRECIO UNITARIO O COSTO PROMEDIO = precio_unitario
                                                    //SALDO FISICO = saldo
                                                    //SALDO VALORADO = saldo_imp
                                                    $f->model("lg/movi")->params(array('data'=>array(
                                                        'glosa'=>'SALIDA DE PRODUCTOS CON COMPROBANTE ELECTRONICO '.$document['tipo'].' '.$document['serie'].' '.$document['numero'],
                                                        'organizacion'=>array(
                                                            '_id'=>new MongoId('57b325908e73582808000032'),
                                                            'nomb'=>utf8_encode('Botica Moises Heresi')
                                                        ),
                                                        'documento'=>array(
                                                            'tipo'=>$document['tipo'],
                                                            '_id'=>$document['_id'],
                                                            'serie'=>$document['serie'],
                                                            'numero'=>$document['numero'],
                                                            'cod'=>$document['numero']
                                                        ),
                                                        'producto'=>array(
                                                            "_id"=>$producto_get['_id'],
                                                            "cod"=>$producto_get['cod'],
                                                            "nomb"=>$producto_get['nomb']
                                                        ),
                                                        'clasif'=>array(
                                                            "_id" => new MongoId("51f281b04d4a13c4040000a9"),
                                                            "cod" => "2.3.1.8.1.99",
                                                            "nomb" => "OTROS PRODUCTOS SIMILARES",
                                                        ),
                                                        'cuenta'=>array(
                                                            "_id" => new MongoId("51a8ff654d4a13540a0000b7"),
                                                            "cod" => "1201.0301",
                                                            "nomb" => "Venta de Bienes",
                                                        ),
                                                        'stock'=>$stock['_id'],
                                                        'tipo'=>'S',
                                                        "almacen"=>array(
                                                                "_id"=>$id_almacen,
                                                                "nomb"=>$nomb_almacen,
                                                        ),
                                                        'autor'=>$f->session->userDBMin,
                                                        'trabajador'=>$f->session->userDBMin,
                                                        'fecreg'=>new MongoDate(),
                                                        'fecmod'=>new MongoDate(),
                                                        'comprobante'=>array(
                                                            '_id'=>$document['_id'],
                                                            'serie'=>$document['serie'],
                                                            'numero'=>$document['numero'],
                                                        ),
                                                        'modulo'=>'FA',
                                                        'fec'=>date("Y-m-d", $fecreg->sec),
                                                        //COSTO PROMEDIO
                                                        'precio_unitario'=>floatval($precio_unitario),
                                                        //SALIDA FISICA
                                                        'cant'=>floatval($item['cant']),
                                                        //SALDO FISICO
                                                        'saldo'=>$stock_actual-floatval($item['cant']),
                                                        //SALIDA VALORADA
                                                        'total'=>floatval($item['cant'])*floatval($precio_unitario),
                                                        //SALDO VALORADO
                                                        'saldo_imp'=>$saldo_monto-floatval($item['cant'])*floatval($precio_unitario),
                                                        //'lote'=>$lote['_id'],
                                                    )))->save("insert")->items;

                                                    $f->model("lg/stck")->params(array(
                                                        '_id'=>$stock['_id'],
                                                        'data'=>array(
                                                            'stock'=>$stock_actual-floatval($item['cant']),
                                                        )
                                                    ))->save("update");
                                                    /********************************************************************
                                                    * REGISTRAR EL MOVIMIENTO -> LOTE
                                                    *********************************************************************/
                                                    //$lote = $f->model('fa/lote')->params(array(
                                                    //    'producto'=>$concepto['producto']['_id']
                                                    //))->get('lote')->items;

                                                    //$f->model('fa/lote')->params(array(
                                                    //    '_id'=>$lote['_id'],
                                                    //    'data'=>array('$inc'=>array(
                                                    //        'cant'=>-$concepto['cant']
                                                    //    ))
                                                    //))->save('custom');
                                                }
                                            }
                                            /***********************************************************************************************
                                            * EN CASO SEA UNA COMPRA DE AGUA CHAPI (KARDEX)
                                            ************************************************************************************************/
                                            if (isset($item['tipo']) && $item['tipo']=="agua_chapi") {
                                                if (isset($concepto['producto'])) {
                                                    $producto_get = $f->model('lg/prod')->params(array('_id'=>$concepto['producto']['_id']))->get('one')->items;
                                                    #ES EL UNICO ALMACEN DE AGUA CHAPI
                                                    $alma=$f->model("lg/alma")->get("agua_chapi")->items;
                                                    if (isset($concepto['alamacen'])) {
                                                        $concepto['alamacen']['_id'] = new MongoId($concepto['alamacen']['_id']);
                                                        $alma = $f->model("lg/alma")->params(array("_id"=>$concepto['alamacen']['_id']))->get("one")->items;
                                                        $id_almacen=$alma['_id'];
                                                    } else {
                                                        $id_almacen = $alma['_id'];
                                                    }
                                                    #Quitar variables que no serviran
                                                    unset($alma['descr']);
                                                    unset($alma['fecmod']);
                                                    unset($alma['trabajador']);
                                                    unset($alma['fecreg']);
                                                    unset($alma['autor']);
                                                    unset($alma['estado']);
                                                    unset($alma['aplicacion']);

                                                    $stock = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$id_almacen,"producto"=>$concepto['producto']['_id'])))->get('one_custom')->items;
                                                    if ($stock==null) {
                                                        $stock = array(
                                                            '_id'=>new MongoId(),
                                                            'producto'=>$concepto['producto']['_id'],
                                                            'almacen'=>$id_almacen,
                                                            'stock'=>0,
                                                            'costo'=>0
                                                        );
                                                        $f->model('lg/stck')->params(array('data'=>$stock))->save('insert');
                                                    }
                                                    /************************************************************************************
                                                    *   DISMINUIR STOCKS DE LOGISTICAS (Coleccion Prod y Almacen)
                                                    ************************************************************************************/
                                                    #ALMACEN
                                                    $fecreg = new MongoDate();
                                                    $temp=array('_id'=>$id_almacen);
                                                    $nomb_almacen = $alma['nomb'];

                                                    //$saldo = $f->model("lg/movi")->params(array(
                                                    //    'filter'=>array(
                                                    //        'stock'=>$stock['_id']
                                                    //    ),'sort'=>array(
                                                    //        'fecreg'=>-1
                                                    //    )
                                                    //))->get('custom')->items;
                                                    $saldo = $f->model("lg/movi")->params(array(
                                                        'filter'=>array(
                                                            'stock'=>$stock['_id'],
                                                            'almacen._id'=>$id_almacen,
                                                            'producto._id'=>$producto_get['_id'],
                                                            'modulo'=>'AG',
                                                        ),
                                                        'sort'=>array(
                                                            'fecreg'=>-1
                                                        )
                                                    ))->get('custom')->items;

                                                    $saldo_cant = 0;
                                                    $saldo_monto = 0;
                                                    if ($saldo!=null) {
                                                        if (count($saldo)>0) {
                                                            $saldo_cant = $saldo[0]['saldo'];
                                                            $saldo_monto = $saldo[0]['saldo_imp'];
                                                        }
                                                    }

                                                    $stock_actual = floatval($saldo_cant);
                                                    $precio_unitario=$producto_get['precio'];

                                                    #REDUCIR LOTE DE AGUA CHAPI
                                                    //$lote = $f->model('lg/lote')->params(array(
                                                    //    'producto'=>$producto_get['_id']
                                                    //))->get('lote')->items;
                                                    //$f->model('lg/lote')->params(array(
                                                    //   '_id'=>$lote['_id'],
                                                    //   'data'=>array('$inc'=>array(
                                                    //       'cant'=>-$item['cant']
                                                    //    ))
                                                    //))->save('custom');
                                                    /************************************************************
                                                    * REGISTRAR EL MOVIMIENTO DE SALIDA-> KARDEX                *
                                                    ************************************************************/
                                                    /*COMO FUNCIONA LOS MOVIMIENTOS*/
                                                    //ENTRADA FISICO O SALIDA FISICO = cant
                                                    //ENTRADA VALORADO O SALIDA VALORADO = total
                                                    //PRECIO UNITARIO O COSTO PROMEDIO = precio_unitario
                                                    //SALDO FISICO = saldo
                                                    //SALDO VALORADO = saldo_imp
                                                    $f->model("lg/movi")->params(array('data'=>array(
                                                        'glosa'=>'SALIDA DE PRODUCTOS CON COMPROBANTE ELECTRONICO '.$document['tipo'].' '.$document['serie'].' '.$document['numero'],
                                                        'organizacion'=>array(
                                                            '_id'=>new MongoId('57b3250f8e73583808000038'),
                                                            'nomb'=>utf8_encode('Actividades comerciales DGRE')
                                                        ),
                                                        'documento'=>array(
                                                            'tipo'=>$document['tipo'],
                                                            '_id'=>$document['_id'],
                                                            'serie'=>$document['serie'],
                                                            'numero'=>$document['numero'],
                                                            'cod'=>$document['numero']
                                                        ),
                                                        'producto'=>array(
                                                            "_id"=>$producto_get['_id'],
                                                            "cod"=>$producto_get['cod'],
                                                            "nomb"=>$producto_get['nomb']
                                                        ),
                                                        'clasif'=>array(
                                                            "_id" => new MongoId("51f281b04d4a13c4040000a9"),
                                                            "cod" => "2.3.1.8.1.99",
                                                            "nomb" => "OTROS PRODUCTOS SIMILARES",
                                                        ),
                                                        'cuenta'=>array(
                                                            "_id" => new MongoId("51a8ff654d4a13540a0000b7"),
                                                            "cod" => "1201.0301",
                                                            "nomb" => "Venta de Bienes",
                                                        ),
                                                        'stock'=>$stock['_id'],
                                                        //'lote'=>$lote['_id'],
                                                        'tipo'=>'S',
                                                        'modulo'=>'AG',
                                                        "almacen"=>array(
                                                                "_id"=>$id_almacen,
                                                                "nomb"=>$nomb_almacen,
                                                        ),
                                                        'autor'=>$f->session->userDBMin,
                                                        'trabajador'=>$f->session->userDBMin,
                                                        'fecreg'=>new MongoDate(),
                                                        'fecmod'=>new MongoDate(),
                                                        'comprobante'=>array(
                                                            '_id'=>$document['_id'],
                                                            'serie'=>$document['serie'],
                                                            'numero'=>$document['numero'],
                                                        ),
                                                        'fec'=>date("Y-m-d", $fecreg->sec),
                                                        //COSTO PROMEDIO
                                                        'precio_unitario'=>floatval($precio_unitario),
                                                        //SALIDA FISICA
                                                        'cant'=>floatval($item['cant']),
                                                        //SALDO FISICO
                                                        'saldo'=>$stock_actual-floatval($item['cant']),
                                                        //SALIDA VALORADA
                                                        'total'=>floatval($item['cant'])*floatval($precio_unitario),
                                                        //SALDO VALORADO
                                                        'saldo_imp'=>$saldo_monto-floatval($item['cant'])*floatval($precio_unitario),
                                                    )))->save("insert")->items;

                                                    $f->model("lg/stck")->params(array(
                                                        '_id'=>$stock['_id'],
                                                        'data'=>array(
                                                            'stock'=>$stock_actual-floatval($item['cant']),
                                                        )
                                                    ))->save("update");
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $f->model('ac/log')->params(array(
                                'modulo'=>'CJ',
                                'bandeja'=>'Pago de comprobante electr&oacute;nico',
                                'descr'=>'Se firmó el comprobante electr&oacute;nico de la serie <b>'.$document['serie'].'</b> y n&uoacute;mero <b>'.$document['numero'].'</b>.'
                            ))->save('insert');
                        }
                    }
                    $response = $result;
                }
            }
            //$response['status'] = 'success';
            //$response['message'] = "El comprobante electronico fue actualizado en nuestro sistema";
        } else {
            $response['status'] = 'error';
            $response['message'] = "El identificador del comprobante electronica a firmar y enviar es obligatorio";
        }

        $f->response->json($response);
    }
    /**
    *   NO SE UTILIZA AUN ESTE CONTROLADOR
    */
    public function execute_confirmar_manual($data)
    {
        global $f;
        //$data = $f->request->data;
        $ConfluxSee = new ConfluxSee();
        $response = array(
            'status'=>'error',
            'message'=>'',
            'data'=>array(
                'id'=>'',
                'estado'=>'',
                'estado_resumen'=>'',
                'estado_baja'=>'',
                'sunat_note'=>'',
                'sunat_description'=>'',
                'sunat_responsecode'=>'',
                'sunat_faultcode'=>'',
                'sunat_soap_error'=>'',
                'digest_value'=>'',
                'signature_value'=>'',
                'codigo_barras'=>'',
                'codigo_barras_image'=>'',
                'ruta_zip_firmado'=>'',
                'ruta_xml_firmado'=>'',
                'ruta_cdr_xml'=>'',
                'ruta_pdf'=>''
            )
        );
        //print_r($data);
        if (isset($data['_id'])) {
            $document = $f->model('cj/ecom')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;
            $update_documento = array(
                'tipo_comprobante'=>'MANUAL',
                'estado'=>'CO',
                'feccon'=>new MongoDate(),
                'autor_con'=>$f->session->userDB
            );
            $f->model('cj/ecom')->params(array('_id'=>new MongoId($data['_id']),'data'=>$update_documento))->save('update');
        } else {
            $response['status'] = 'error';
            $response['message'] = "El identificador del comprobante electronica a firmar y enviar es obligatorio";
        }

        $f->response->json($response);
    }
    public function execute_anular()
    {
        global $f;
        $data = $f->request->data;
        $ConfluxSee = new ConfluxSee();
        $response = array(
            'status'=>'error',
            'message'=>'',
            'data'=>array(),
        );
        if (isset($data['_id'])) {
            $document = $f->model('cj/ecom')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;
            if ($document['tipo_comprobante']=='ELECTRONICO') {
                if (isset($document['conflux_see_id'])) {
                    /**
                    *   PARA REALIZAR EL PEDIDO DE BAJA SE UTILIZAN 2 WEBSERVICE
                    */
                    $curl_handle = curl_init();
                    if ($document['tipo']=="B") {
                        $anular=array(
                            '_id' => $document['conflux_see_id'],
                        );
                        //curl_setopt($curl_handle, CURLOPT_URL, 'http://35.193.115.148/index.php/api/documents/invoice/void/');
                        curl_setopt($curl_handle, CURLOPT_URL, 'http://einvoice.conflux.pe/index.php/api/void/invoice/format/json/');
                        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($curl_handle, CURLOPT_POST, 1);
                        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
                            //'X-CONFLUX-API-KEY: 1f3870be274f6c49b3e31a0c6728957f'
                            'Authorization: Token xfNxKIW6BjaCTS2CbeEhltuK2X6iIhWL'
                        ));
                        //curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "_id=".$anular['_id']);
                        $data_to_rest = json_encode($anular);
                        $data_base64 = base64_encode($data_to_rest);
                        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "data=".$data_base64."&enviar=1");
                    } else {
                        $anular=array(
                            '_id' => $document['conflux_see_id'],
                            //'fecres' => date('Y-m-d', $document['fecemi']->sec),
                            'motivo' => (isset($data['motivo_baja']))? $data['motivo_baja'] : 'EL USUARIO NO INTRODUJO UN MOTIVO DE BAJA',
                            //'tipo' =>'RA',
                            // 'fecgen' => date('Y-m-d'),
                        );
                        curl_setopt($curl_handle, CURLOPT_URL, 'http://einvoice.conflux.pe/index.php/api/void/invoice/format/json/');
                        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($curl_handle, CURLOPT_POST, 1);
                        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
                        curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
                            //'X-CONFLUX-API-KEY: 1f3870be274f6c49b3e31a0c6728957f'
                            'Authorization: Token xfNxKIW6BjaCTS2CbeEhltuK2X6iIhWL'
                        ));
                        $data_to_rest = json_encode($anular);
                        $data_base64 = base64_encode($data_to_rest);
                        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, "data=".$data_base64."&enviar=1");
                    }

                    #curl_setopt($curl_handle, CURLOPT_URL, 'http://35.193.115.148/index.php/api/void/invoice/format/json/');
                    #curl_setopt($curl_handle, CURLOPT_URL, 'http://72d70e06.ngrok.io/index.php/api/void/invoice/format/json/');

                    //curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                    //curl_setopt($curl_handle, CURLOPT_POST, 1);
                    //curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
                    //curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
                    //curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
                    //    'X-CONFLUX-API-KEY: 1f3870be274f6c49b3e31a0c6728957f'
                    //));

                    //$request = $this->input->post(NULL,TRUE);
                    //$request['fecreg'] = strtotime($request['fecreg']);
                    //$data_to_rest = json_encode($document);

                    #$data_to_rest = json_encode($anular);

                    //if ($document['tipo']=="B") {
                    //    curl_setopt($curl_handle, CURLOPT_POSTFIELDS,"_id=".$anular['_id']);
                    //} else {
                    //    $data_to_rest = json_encode($anular);
                    //    $data_base64 = base64_encode($data_to_rest);
                    //    curl_setopt($curl_handle, CURLOPT_POSTFIELDS,"data=".$data_base64."&enviar=1");
                    //}

                    #$data_base64 = base64_encode($data_to_rest);
                    /*if(isset($request['enviar']))
                        curl_setopt($curl_handle, CURLOPT_POSTFIELDS,"data=".$data_base64."&enviar=1");
                    else*/
                    #curl_setopt($curl_handle, CURLOPT_POSTFIELDS,"data=".$data_base64."&enviar=1");

                    $buffer = curl_exec($curl_handle);

                    if ($buffer===false) {
                        curl_close($curl_handle);
                        $response['status'] = 'error';
                        $response['message'] = 'El servidor no respondio a la solicitud enviada!';
                    } else {
                        curl_close($curl_handle);
                        $result = json_decode($buffer, true);
                        if ($result === null && json_last_error() !== JSON_ERROR_NONE) {
                            $response['status'] = 'error';
                            $response['message'] = 'El servidor dio un error inesperado que no puede ser parseado!';
                            $response['data'] = $buffer;
                        } else {
                            if (isset($result['status']) && isset($result['data'])) {
                                if ($result['status']!="error") {
                                    //$update_documento = $result['data'];
                                    $update_documento['estado']='X';
                                    $update_documento['fecanu']=new MongoDate();
                                    $update_documento['autor_anu']=$f->session->userDB;
                                    //print_r($result);
                                    /*if (isset($result['data']['verificacion_ticket']['sunat_code'])) {
                                        $update_documento['sunat_code']=$result['data']['verificacion_ticket']['sunat_code'];
                                    }
                                    if (isset($result['data']['verificacion_ticket']['sunat_description'])) {
                                        $update_documento['sunat_description']=$result['data']['verificacion_ticket']['sunat_description'];
                                    }
                                    if (isset($result['data']['verificacion_ticket']['sunat_responsecode'])) {
                                        $update_documento['sunat_responsecode']=$result['data']['verificacion_ticket']['sunat_responsecode'];
                                    }
                                    if (isset($result['data']['verificacion_ticket']['sunat_soap_error'])) {
                                        $update_documento['sunat_soap_error']=$result['data']['verificacion_ticket']['sunat_soap_error'];
                                    }*/
                                    //$update_documento['digest_value']=$result['data']['digest_value'];
                                    //$update_documento['ruta_zip_firmado']=$result['data']['ruta_zip_firmado'];
                                    //$update_documento['ruta_xml_firmado']=$result['data']['ruta_xml_firmado'];
                                    //$update_documento['signature_value']=$result['data']['signature_value'];
                                    //$update_documento['ruta_cdr_xml']=$result['data']['ruta_cdr_xml'];
                                    if (isset($result['data']['ticket_sunat'])) {
                                        $update_documento['ticket_sunat']=$result['data']['ticket_sunat'];
                                    }

                                    //EN CASO DE ANULACION DE BOLETAS, SE ASUME QUE EL SERVIDOR DE BOLETAS LAS ANULO
                                    if (isset($result['data']['tipo'])) {
                                        $update_documento['tipo'] = $result['data']['tipo'];
                                    } elseif ($document['tipo'] == "B") {
                                        $update_documento['tipo'] = 'RA';
                                    }
                                    #$update_documento['conflux_see_anulacion_id']=$result['data']['_id'];
                                    if (isset($result['data']['_id'])) {
                                        $update_documento['conflux_see_anulacion_id']=$result['data']['_id'];
                                    } elseif ($document['tipo'] == "B") {
                                        $update_documento['conflux_see_anulacion_id']=$document['conflux_see_id'];

                                        $update_documento['motivo_anulacion'] = (isset($data['motivo_baja']))? $data['motivo_baja'] : 'EL USUARIO NO INTRODUJO UN MOTIVO DE BAJA';
                                    }
                                    $f->model('cj/ecom')->params(array('_id'=>new MongoId($data['_id']),'data'=>$update_documento))->save('update');

                                    //EN CASO DE QUE LA FIRMA SEA EXITOSA APLICAR LOS CAMBIOS
                                    $document = $f->model('cj/ecom')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;
                                    if (isset($document['items'])) {
                                        foreach ($document['items'] as $i => $item) {
                                            if (isset($item['conceptos'])) {
                                                foreach ($item['conceptos'] as $c => $concepto) {
                                                    /*************************************************************************************************
                                                    * EN CASO SEA UN PAGO DE ALQUILER DE INMUEBLES
                                                    *************************************************************************************************/
                                                    if (isset($concepto['alquiler'])) {
                                                        if (!isset($concepto['parent'])) {
                                                            $tmp_pay=array();
                                                            $tmp_pago=array();
                                                            $tmp_parent=array();
                                                            $contrato = $f->model('in/cont')->params(array(
                                                                '_id'=>$concepto['alquiler']['contrato'],
                                                            ))->get('one')->items;
                                                            $tmp_parent['parent']=$concepto['item'];
                                                            $tmp_pay['alquiler']=$concepto['monto'];
                                                            $tmp_pago['pago']=$concepto['pago'];
                                                        } else {
                                                            if ($concepto['item']-$tmp_parent['parent']==1) {
                                                                $tmp_pay['igv']=$concepto['monto'];
                                                            }
                                                            if ($concepto['item']-$tmp_parent['parent']==2) {
                                                                $tmp_pay['monto']=$concepto['monto'];
                                                            }
                                                            if (isset($item['conceptos'][$c+1]) || count($item['conceptos'])==($c+1)) {
                                                                if (!isset($item['conceptos'][$c+1]['parent']) || count($item['conceptos'])==($c+1)) {
                                                                    foreach ($contrato['pagos'] as $kp=>$pago) {
                                                                        if ($pago['mes']==$tmp_pago['pago']['mes']&&$pago['ano']==$tmp_pago['pago']['ano']) {
                                                                            /***********************************************************
                                                                            * EN CASO DE SER UN REGISTRO DE COBRO COMPLETO
                                                                            ***********************************************************/
                                                                            if (isset($item['tipo']) && ($item['tipo']=="pago_meses")) {
                                                                                #ENCONTRE UNA POSIBLE SOLUCION :/
                                                                                foreach ($contrato['pagos'] as $key => $value) {
                                                                                    if (isset($value['comprobante'])) {
                                                                                        if ($document['_id']==$value['comprobante']['_id']) {
                                                                                            $index = $key;
                                                                                            $tmp = $value;
                                                                                            $f->model('in/cont')->params(array('_id'=>$contrato['_id'],'data'=>array(
                                                                                                'pagos.'.$index=>array(
                                                                                                    'item'=>$tmp['item'],
                                                                                                    'ano'=>$tmp['ano'],
                                                                                                    'mes'=>$tmp['mes']
                                                                                                )
                                                                                            )))->save('update')->items;
                                                                                            //break;
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                            /***********************************************************
                                                                            * EN CASO DE SER UN REGISTRO DE COBRO PARCIAL
                                                                            ***********************************************************/
                                                                            elseif (isset($item['tipo']) && ($item['tipo']=="pago_parcial")) {
                                                                                foreach ($contrato['pagos'] as $key => $value) {
                                                                                    if (isset($value['comprobantes'])) {
                                                                                        foreach ($value['comprobantes'] as $k => $comp_p) {
                                                                                            if ($document['_id']==$comp_p['_id']) {
                                                                                                $index = $key;
                                                                                                $index_p = $k;
                                                                                                $tmp = $value;
                                                                                                $tmp['estado'] = 'P';
                                                                                                unset($tmp['comprobantes'][$k]);
                                                                                                if (sizeof($tmp['comprobantes'])==0) {
                                                                                                    unset($tmp['comprobantes']);
                                                                                                    unset($tmp['total']);
                                                                                                }
                                                                                                break;
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                                $f->model('in/cont')->params(array('_id'=>$document['contrato'],'data'=>array(
                                                                                    'pagos.'.$index=>$tmp
                                                                                )))->save('update')->items;
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                    /*************************************************************************************************
                                                    * EN CASO SEA UN PAGO DE ACTA DE CONCILIACION DE INMUEBLES
                                                    *************************************************************************************************/
                                                    if (isset($concepto['acta_conciliacion'])) {
                                                        //NO ENCONTRE CODIGO QUE ANULE ACTAS DE CONCILIACION EN COMPROBANTES MANUALES:/
                                                    }
                                                    //***********************************************************************************************
                                                    //* EN CASO SEA UNA CUENTA POR COBRAR
                                                    //***********************************************************************************************
                                                    if (isset($concepto['cuenta_cobrar'])) {
                                                        if ($item['tipo']=="cuenta_cobrar") {
                                                            if (!isset($item['parent'])) {
                                                                $f->model('cj/cuen')->params(array(
                                                                    '_id'=>$concepto['cuenta_cobrar']['_id'],
                                                                    'data'=>array('$pull'=>array('comprobantes'=>$document['_id']))
                                                                ))->save('custom');
                                                            }
                                                            $cuenta = $f->model('cj/cuen')->params(array('_id'=>$concepto['cuenta_cobrar']['_id']))->get('one')->items;
                                                            $f->model('cj/cuen')->params(array(
                                                                '_id'=>$concepto['cuenta_cobrar']['_id'],
                                                                'data'=>array('$set'=>array('estado'=>'P'))
                                                            ))->save('custom');
                                                        }
                                                    }
                                                    #************************************************************************************************
                                                    # EN CASO SEA UNA COMPRA DE FARMACIA (KARDEX)
                                                    #************************************************************************************************
                                                    if (isset($item['tipo']) && $item['tipo']=="farmacia") {
                                                        if (isset($concepto['producto'])) {
                                                            $producto_get = $f->model('lg/prod')->params(array('_id'=>$concepto['producto']['_id']))->get('one')->items;
                                                            # EL UNICO ALMACEN DE FARMACIA
                                                            $alma=$f->model("lg/alma")->get("farmacia")->items;
                                                            if (isset($concepto['alamacen'])) {
                                                                $concepto['alamacen']['_id'] = new MongoId($concepto['alamacen']['_id']);
                                                                $alma = $f->model("lg/alma")->params(array("_id"=>$concepto['alamacen']['_id']))->get("one")->items;
                                                                $id_almacen=$alma['_id'];
                                                            } else {
                                                                $id_almacen = $alma['_id'];
                                                            }

                                                            #Quitar variables que no serviran
                                                            unset($alma['descr']);
                                                            unset($alma['fecmod']);
                                                            unset($alma['trabajador']);
                                                            unset($alma['fecreg']);
                                                            unset($alma['autor']);
                                                            unset($alma['estado']);
                                                            unset($alma['aplicacion']);

                                                            $stock = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$id_almacen,"producto"=>$concepto['producto']['_id'])))->get('one_custom')->items;
                                                            if ($stock==null) {
                                                                $stock = array(
                                                                    '_id'=>new MongoId(),
                                                                    'producto'=>$concepto['producto']['_id'],
                                                                    'almacen'=>$id_almacen,
                                                                    'stock'=>0,
                                                                    'costo'=>0
                                                                );
                                                                $f->model('lg/stck')->params(array('data'=>$stock))->save('insert');
                                                            }

                                                            /************************************************************************************
                                                            *   ANULAR ULTIMO MOVIMIENTO (Coleccion Prod y Almacen)
                                                            ************************************************************************************/
                                                            #ALMACEN
                                                            $fecreg = new MongoDate();
                                                            $temp=array('_id'=>$id_almacen);
                                                            $nomb_almacen = $alma['nomb'];

                                                            $movimiento = $f->model("lg/movi")->params(array(
                                                                'filter'=>array(
                                                                    'comprobante._id'=>$document['_id'],
                                                                ),
                                                            ))->get('custom')->items[0];
                                                            $movimiento['feceli'] = new MongoDate();
                                                            $movimiento['coleccion'] = 'lg_movimientos';
                                                            $movimiento['trabajador_delete'] = $f->session->userDB;
                                                            $f->datastore->temp_del->insert($movimiento);
                                                            $f->datastore->lg_movimientos->remove(array('_id'=>$movimiento['_id']));

                                                            /************************************************************************************
                                                            *   OBTENER ULTIMO MOVIMIENTO (Coleccion Prod y Almacen) Y ASIGNAR ESE SALDO
                                                            ************************************************************************************/
                                                            /************************************************************************************
                                                            *   SI NO REDUCE LOS MOVIMIENTOS, LO MEJOR ES HACER UN REINICIO DE STOCK
                                                            ************************************************************************************/
                                                            $saldo = $f->model("lg/movi")->params(array(
                                                                'filter'=>array(
                                                                    'stock'=>$stock['_id'],
                                                                    'almacen._id'=>$id_almacen,
                                                                    'producto._id'=>$producto_get['_id'],
                                                                    'modulo'=>'FA',
                                                                ),
                                                                'sort'=>array(
                                                                    'fecreg'=>-1
                                                                )
                                                            ))->get('custom')->items;

                                                            $saldo_cant = 0;
                                                            $saldo_monto = 0;
                                                            if ($saldo!=null) {
                                                                if (count($saldo)>0) {
                                                                    $saldo_cant = $saldo[0]['saldo'];
                                                                    $saldo_monto = $saldo[0]['saldo_imp'];
                                                                }
                                                            }

                                                            $stock_actual = floatval($saldo_cant);
                                                            $precio_unitario=$producto_get['precio'];

                                                            /*$f->model("lg/stck")->params(array(
                                                                '_id'=>$stock['_id'],
                                                                'data'=>array(
                                                                    'stock'=>$stock_actual-floatval($item['cant']),
                                                                )
                                                            ))->save("update");*/
                                                            $f->model("lg/stck")->params(array(
                                                                '_id'=>$stock['_id'],
                                                                'data'=>array(
                                                                    'stock'=>$stock_actual,
                                                                    'costo'=>$saldo_monto,
                                                                )
                                                            ))->save("update");
                                                        }
                                                    }
                                                    /***********************************************************************************************
                                                    * EN CASO SEA UNA COMPRA DE AGUA CHAPI (KARDEX)
                                                    ************************************************************************************************/
                                                    if (isset($item['tipo']) && $item['tipo']=="agua_chapi") {
                                                        if (isset($concepto['producto'])) {
                                                            $producto_get = $f->model('lg/prod')->params(array('_id'=>$concepto['producto']['_id']))->get('one')->items;
                                                            #ES EL UNICO ALMACEN DE AGUA CHAPI
                                                            $alma=$f->model("lg/alma")->get("agua_chapi")->items;
                                                            if (isset($concepto['alamacen'])) {
                                                                $concepto['alamacen']['_id'] = new MongoId($concepto['alamacen']['_id']);
                                                                $alma = $f->model("lg/alma")->params(array("_id"=>$concepto['alamacen']['_id']))->get("one")->items;
                                                                $id_almacen=$alma['_id'];
                                                            } else {
                                                                $id_almacen = $alma['_id'];
                                                            }
                                                            #Quitar variables que no serviran
                                                            unset($alma['descr']);
                                                            unset($alma['fecmod']);
                                                            unset($alma['trabajador']);
                                                            unset($alma['fecreg']);
                                                            unset($alma['autor']);
                                                            unset($alma['estado']);
                                                            unset($alma['aplicacion']);

                                                            $stock = $f->model("lg/stck")->params(array("filter"=>array("almacen"=>$id_almacen,"producto"=>$concepto['producto']['_id'])))->get('one_custom')->items;
                                                            if ($stock==null) {
                                                                $stock = array(
                                                                    '_id'=>new MongoId(),
                                                                    'producto'=>$concepto['producto']['_id'],
                                                                    'almacen'=>$id_almacen,
                                                                    'stock'=>0,
                                                                    'costo'=>0
                                                                );
                                                                $f->model('lg/stck')->params(array('data'=>$stock))->save('insert');
                                                            }

                                                            /************************************************************************************
                                                            *   ANULAR ULTIMO MOVIMIENTO (Coleccion Prod y Almacen)
                                                            ************************************************************************************/
                                                            #ALMACEN
                                                            $fecreg = new MongoDate();
                                                            $temp=array('_id'=>$id_almacen);
                                                            $nomb_almacen = $alma['nomb'];

                                                            $movimiento = $f->model("lg/movi")->params(array(
                                                                'filter'=>array(
                                                                    'comprobante._id'=>$document['_id'],
                                                                ),
                                                            ))->get('custom')->items[0];
                                                            $movimiento['feceli'] = new MongoDate();
                                                            $movimiento['coleccion'] = 'lg_movimientos';
                                                            $movimiento['trabajador_delete'] = $f->session->userDB;
                                                            $f->datastore->temp_del->insert($movimiento);
                                                            $f->datastore->lg_movimientos->remove(array('_id'=>$movimiento['_id']));

                                                            /************************************************************************************
                                                            *   OBTENER ULTIMO MOVIMIENTO (Coleccion Prod y Almacen) Y ASIGNAR ESE SALDO
                                                            ************************************************************************************/
                                                            /************************************************************************************
                                                            *   SI NO REDUCE LOS MOVIMIENTOS, LO MEJOR ES HACER UN REINICIO DE STOCK
                                                            ************************************************************************************/
                                                            $saldo = $f->model("lg/movi")->params(array(
                                                                'filter'=>array(
                                                                    'stock'=>$stock['_id'],
                                                                    'almacen._id'=>$id_almacen,
                                                                    'producto._id'=>$producto_get['_id'],
                                                                    'modulo'=>'AG',
                                                                ),
                                                                'sort'=>array(
                                                                    'fecreg'=>-1
                                                                )
                                                            ))->get('custom')->items;

                                                            $saldo_cant = 0;
                                                            $saldo_monto = 0;
                                                            if ($saldo!=null) {
                                                                if (count($saldo)>0) {
                                                                    $saldo_cant = $saldo[0]['saldo'];
                                                                    $saldo_monto = $saldo[0]['saldo_imp'];
                                                                }
                                                            }

                                                            $stock_actual = floatval($saldo_cant);
                                                            $precio_unitario=$producto_get['precio'];

                                                            /*$f->model("lg/stck")->params(array(
                                                                '_id'=>$stock['_id'],
                                                                'data'=>array(
                                                                    'stock'=>$stock_actual-floatval($item['cant']),
                                                                )
                                                            ))->save("update");*/
                                                            $f->model("lg/stck")->params(array(
                                                                '_id'=>$stock['_id'],
                                                                'data'=>array(
                                                                    'stock'=>$stock_actual,
                                                                    'costo'=>$saldo_monto,
                                                                )
                                                            ))->save("update");
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    $f->model('ac/log')->params(array(
                                        'modulo'=>'CJ',
                                        'bandeja'=>'Pago de comprobante electr&oacute;nico',
                                        //'descr'=>'Se anuló el comprobante electr&oacute;nico de la serie <b>'.$document['serie'].'</b> y n&uoacute;mero <b>'.$document['numero'].'</b> en la caja <b>'.$data['caja']['nomb'].'</b>.'
                                    ))->save('insert');
                                }
                            }
                            $response = $result;
                        }
                    }
                } else {
                    $response['status'] = 'error';
                    $response['message'] = "El comprobante electronico no puede ser anulado ya que no contiene un identificador del servidor de facturacion";
                }
            } else {
                $update_documento = array();
                $update_documento['estado']='X';
                $update_documento['fecanu']=new MongoDate();
                $update_documento['autor_anu']=$f->session->userDB;
                $f->model('cj/ecom')->params(array('_id'=>new MongoId($data['_id']),'data'=>$update_documento))->save('update');
                $response['status'] = 'success';
                $response['message'] = 'Comprobante anulado correctamente';
            }
            //$response['status'] = 'success';
            //$response['message'] = "El comprobante electronico fue actualizado en nuestro sistema";
        } else {
            $response['status'] = 'error';
            $response['message'] = "El identificador del comprobante electronica a firmar y enviar es obligatorio";
        }
        $f->response->json($response);
    }
    public function execute_verificacion_estado()
    {
        /**
        *
        */
        global $f;
        $data = $f->request->data;

        # PARAMETROS
        $direccion_ip = "https://einvoice.conflux.pe";
        //$direccion_ip = "http://dc4b60f2.ngrok.io";
        $url_verificacion = "api/v/1/account_einvoice/invoice/";
        //https://einvoice.conflux.pe/api/v/1/account_einvoice/invoice/gggg/

        $ConfluxSee = new ConfluxSee();
        $response = array(
            'status'=>'error',
            'message'=>'',
            'data'=>array(),
        );

        if (isset($data['_id'])) {
            $document = $f->model('cj/ecom')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;

            if ($document['tipo_comprobante']=='ELECTRONICO') {
                if (isset($document['conflux_see_id'])) {
                    $curl_handle = curl_init();
                    //curl_setopt($curl_handle, CURLOPT_URL, $direccion_ip."/".$url_verificacion."?_id=".$document['conflux_see_id']);
                    curl_setopt($curl_handle, CURLOPT_URL, $direccion_ip."/".$url_verificacion.$document['conflux_see_id']."/");
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                    //curl_setopt($curl_handle, CURLOPT_POST, 1);
                    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
                        //'X-CONFLUX-API-KEY: 1f3870be274f6c49b3e31a0c6728957f'
                        'Authorization: Token xfNxKIW6BjaCTS2CbeEhltuK2X6iIhWL'
                    ));

                    //curl_setopt($curl_handle, CURLOPT_POSTFIELDS,"_id=".$document['conflux_see_id']);

                    $buffer = curl_exec($curl_handle);
                    //var_dump($direccion_ip."/".$url_verificacion.$document['conflux_see_id']."/");
                    if ($buffer===false) {
                        curl_close($curl_handle);
                        $response['status'] = 'error';
                        $response['message'] = 'El servidor no respondio a la solicitud enviada!';
                    } else {
                        curl_close($curl_handle);
                        $result = json_decode($buffer, true);

                        if ($result === null && json_last_error() !== JSON_ERROR_NONE) {
                            $response['status'] = 'error';
                            $response['message'] = 'El servidor dio un error inesperado que no puede ser parseado!';
                            $response['data'] = $buffer;
                        } else {
                            if (1) {

                                // $update_documento['estado']='X';
                                // $update_documento['fecanu']=new MongoDate();
                                // $update_documento['autor_anu']=$f->session->userDB;

                                /*if (isset($result['rpta']['sunat_note'])) {
                                    $update_documento['sunat_note']=$result['rpta']['sunat_note'];
                                }
                                if (isset($result['rpta']['sunat_soap_error'])) {
                                    $update_documento['sunat_soap_error']=$result['sunat_soap_error'];
                                }*/
                                if (isset($result['enlace_del_xml'])) {
                                    $update_documento['ruta_xml_firmado']=$result['enlace_del_xml'];
                                }
                                if (isset($result['enlace_del_cdr'])) {
                                    $update_documento['ruta_cdr_xml']=$result['enlace_del_cdr'];
                                }
                                if (isset($result['emision_aceptada'])) {
                                    $update_documento['estado']=($result['emision_aceptada'])?"ES":$document['estado'];
                                }
                                /*if (isset($result['rpta']['tipo'])) {
                                    $update_documento['tipo']=$result['rpta']['tipo'];
                                }
                                if (isset($result['rpta']['sunat_faultcode'])) {
                                    $update_documento['sunat_faultcode']=$result['rpta']['sunat_faultcode'];
                                }
                                if (isset($result['rpta']['sunat_description'])) {
                                    $update_documento['sunat_description']=$result['rpta']['sunat_description'];
                                }
                                if (isset($result['rpta']['estado_resumen'])) {
                                    $update_documento['estado_resumen']=$result['rpta']['estado_resumen'];
                                }
                                if (isset($result['rpta']['sunat_responsecode'])) {
                                    $update_documento['sunat_responsecode']=$result['rpta']['sunat_responsecode'];
                                }*/

                                //if(isset($result['data']['verificacion_ticket']['sunat_code'])) $update_documento['sunat_code']=$result['data']['verificacion_ticket']['sunat_code'];

                                //if(isset($result['data']['verificacion_ticket']['sunat_code'])) $update_documento['sunat_code']=$result['data']['verificacion_ticket']['sunat_code'];
                                //
                                //
                                //
                                // $update_documento['digest_value']=$result['data']['digest_value'];
                                // $update_documento['ruta_zip_firmado']=$result['data']['ruta_zip_firmado'];
                                //
                                // $update_documento['signature_value']=$result['data']['signature_value'];
                                //
                                // $update_documento['numero']=$result['data']['numero'];
                                // $update_documento['ticket_sunat']=$result['data']['ticket_sunat'];
                                //
                                // $update_documento['conflux_see_anulacion_id']=$result['data']['_id'];
                                $f->model('cj/ecom')->params(array('_id'=>new MongoId($data['_id']),'data'=>$update_documento))->save('update');
                            }
                            $f->model('ac/log')->params(array(
                                'modulo'=>'CJ',
                                'bandeja'=>'Pago de comprobante electr&oacute;nico',
                                //'descr'=>'Se verificó el estado del comprobante electr&oacute;nico con descripción de la sunat <b>'.$document['sunat_description'].'</b> de la serie <b>'.$document['serie'].'</b> y n&uoacute;mero <b>'.$document['numero'].'</b> en la caja <b>'.$data['caja']['nomb'].'</b>.'
                            ))->save('insert');
                            $response = $result;
                        }
                    }
                }
            } else {
                $response['status'] = 'error';
                $response['message'] = "El documento no es un comprobante de credito electronico";
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = "El identificador del comprobante electronica a firmar y enviar es obligatorio";
        }
        $f->response->json($response);
    }

    public function execute_get_vars()
    {
        global $f;
        $tc;
        $igv;
        $vars = array();
        $varss = $f->model("mg/vari")->params(array("fields"=>array(
            'cod'=>true,
            'nomb'=>true,
            'valor'=>true
        )))->get("all");
        foreach ($varss->items as $item) {
            $vars[] = array('cod'=>$item['cod'],'valor'=>floatval($item['valor']));
            if ($item['cod']=='TC') {
                $tc = array('cod'=>$item['cod'],'valor'=>floatval($item['valor']));
            }
        }
        $cajas = array();
        if (isset($f->session->enti['roles']['cajero'])) {
            foreach ($f->session->enti['roles']['cajero']['cajas'] as $caja) {
                $cajas[] = $f->model("cj/caja")->params(array("_id"=>new MongoId($caja)))->get("one")->items;
            }
        }
        $rpta = array(
            'cajas'=>$cajas,
            'tc'=>$tc,
            'vars'=>$vars,
            'ctban'=>$f->model("ts/ctban")->get("all")->items,
            'almacenes' =>$f->model('lg/alma')->params(array())->get('all')->items,
            'unidades' =>$f->model('lg/unid')->params(array())->get('all')->items
        );
        $f->response->json($rpta);
    }

    public function execute_reporte_sin_concepto()
    {
        global $f;
        $data = $f->request->data;
        $params = array(
            'filter' => array(
                "items.conceptos"=> array('$exists' => false),
                //"caja._id"=> new MongoId($data['caja']),
                "estado"=> array('$nin'=>array('X','BO')),
            ),
            'fields' => array(
                'tipo' => 1,
                'serie' => 1,
                'numero' => 1,
                'fecemi' => 1,
            )
        );
        $model = $f->model("cj/ecom")->params($params)->get("all")->items;
        header("Content-type:application/json");
        echo json_encode($model);
    }
    public function execute_reporte_continuacion()
    {
        global $f;
        $data = $f->request->data;
        $ecom = array();
        $cont = array();
        $params = array(
            'filter' => array(
                //"caja._id"=> new MongoId($data['caja']),
                "estado"=> array('$nin'=>array('BO')),
            ),
            'fields' => array(
                'tipo' => 1,
                'serie' => 1,
                'numero' => 1,
            )
        );
        $model = $f->model("cj/ecom")->params($params)->get("all")->items;
        foreach ($model as $e => $comp) {
            $ecom[$comp['serie']][$comp['numero']][]=$comp;
        }
        foreach ($ecom as $s => $serie) {
          for ($i=1; $i <= count($serie) ; $i++) {
            if (!isset($serie[$i])) {
              $cont[$s][]=$i;
            }
          }
        }
        header("Content-type:application/json");
        echo json_encode($cont);
    }
    public function execute_save_rein()
    {
        global $f;
        $data = $f->request->data;
        if (isset($data['fec'])) {
            $data['fec'] = new MongoDate(strtotime($data['fec']));
        }
        if (isset($data['fecfin'])) {
            $data['fecfin'] = new MongoDate(strtotime($data['fecfin']));
        }
        if (isset($data['total'])) {
            $data['total'] = floatval($data['total']);
        }
        if (isset($data['fuente']['_id'])) {
            $data['fuente']['_id'] = new MongoId($data['fuente']['_id']);
        }
        if (isset($data['organizacion']['_id'])) {
            $data['organizacion']['_id'] = new MongoId($data['organizacion']['_id']);
        }
        if (isset($data['organizacion']['componente']['_id'])) {
            $data['organizacion']['componente']['_id'] = new MongoId($data['organizacion']['componente']['_id']);
        }
        if (isset($data['organizacion']['actividad']['_id'])) {
            $data['organizacion']['actividad']['_id'] = new MongoId($data['organizacion']['actividad']['_id']);
        }
        if (isset($data['organizacion']['subprograma']['_id'])) {
            $data['organizacion']['subprograma']['_id'] = new MongoId($data['organizacion']['subprograma']['_id']);
        }
        if (isset($data['organizacion']['programa']['_id'])) {
            $data['organizacion']['programa']['_id'] = new MongoId($data['organizacion']['programa']['_id']);
        }
        if (isset($data['organizacion']['funcion']['_id'])) {
            $data['organizacion']['funcion']['_id'] = new MongoId($data['organizacion']['funcion']['_id']);
        }
        if (isset($data['detalle'])) {
            foreach ($data['detalle'] as $i=>$det) {
                if (isset($det['cuenta']['_id'])) {
                    $data['detalle'][$i]['cuenta']['_id'] = new MongoId($det['cuenta']['_id']);
                }
                if (isset($det['comprobante']['_id'])) {
                    $data['detalle'][$i]['comprobante']['_id'] = new MongoId($det['comprobante']['_id']);
                }
                if (isset($det['cuenta_cobrar'])) {
                    $data['detalle'][$i]['cuenta_cobrar'] = new MongoId($det['cuenta_cobrar']);
                }
            }
        }
        if (isset($data['comprobantes_anulados'])) {
            foreach ($data['comprobantes_anulados'] as $i=>$det) {
                if (isset($det['_id'])) {
                    $data['comprobantes_anulados'][$i]['_id'] = new MongoId($det['_id']);
                }
            }
        }
        if (isset($data['cont_patrimonial'])) {
            foreach ($data['cont_patrimonial'] as $i=>$det) {
                if (isset($det['cuenta']['_id'])) {
                    $data['cont_patrimonial'][$i]['cuenta']['_id'] = new MongoId($det['cuenta']['_id']);
                }
            }
        }
        if (isset($data['vouchers'])) {
            foreach ($data['vouchers'] as $i=>$det) {
                if (isset($det['cuenta_banco']['_id'])) {
                    $data['vouchers'][$i]['cuenta_banco']['_id'] = new MongoId($det['cuenta_banco']['_id']);
                }
                if (isset($det['cliente']['_id'])) {
                    $data['vouchers'][$i]['cliente']['_id'] = new MongoId($det['cliente']['_id']);
                }
            }
        }
        if (!isset($f->request->data['_id'])) {
            $data['fecreg'] = new MongoDate();
            $data['estado'] = 'RG';
            $data['autor'] = $f->session->userDB;
            $rein = $f->model("cj/rein")->params(array('data'=>$data))->save("insert")->items;
        } else {
            $f->model("cj/rein")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
        }
        $f->response->json($rein);
    }
    public function execute_reci_ing()
    {
        global $f;
        $f->library('helpers');
        $helper=new helper();
        $meses = array("Ene.","Feb.","Mar.","Abr.","May.","Jun.","Jul.","Ago.","Set.","Oct.","Nov.","Dic.");
        $model = $f->model("ct/pcon")->params(array('oficial'=>true))->get("lista");
        $recibo = $f->model('cj/rein')->params(array(
            '_id'=>new MongoId($f->request->data['_id'])
        ))->get('one')->items;
        $total_detraccion = 0;

        /**
        *   VALIDACIONES
        */
        if (!isset($recibo['modulo'])) {
            echo '<pre>';
            print_r("No existe el modulo del recibo");
            echo '</pre>';
        }


        /* PREPARAR LOS ELEMENTOS DEL RECIBO DE INGRESO */
        foreach ($recibo['detalle'] as $k => $row) {
            if (!isset($row['comprobante'])) {
                print_r($row);
                print_r($k);
                die();
            }
            $cmpbnt=$f->model('cj/ecom')->params(array(
                'filter' => array(
                    '_id' => $row['comprobante']['_id']
                ),'fields' => array()
            ))->get('one_custom')->items;
            #CAMPOS INNECESARIOS
            unset($cmpbnt['codigo_barras_pdf']);
            unset($cmpbnt['estado_resumen']);
            unset($cmpbnt['estado_baja']);
            unset($cmpbnt['sunat_note']);
            unset($cmpbnt['sunat_description']);
            unset($cmpbnt['sunat_responsecode']);
            unset($cmpbnt['sunat_faultcode']);
            unset($cmpbnt['sunat_soap_error']);
            unset($cmpbnt['digest_value']);
            unset($cmpbnt['signature_value']);
            unset($cmpbnt['codigo_barras']);
            unset($cmpbnt['codigo_barras_pdf']);
            unset($cmpbnt['ruta_zip_firmado']);
            unset($cmpbnt['ruta_xml_firmado']);
            unset($cmpbnt['ruta_cdr_xml']);
            unset($cmpbnt['ruta_pdf']);

            if (isset($cmpbnt['items'])) {
                foreach ($cmpbnt['items'] as $l=>$item) {
                    if (isset($item['conceptos'])) {
                        foreach ($item['conceptos'] as $m=>$conc) {
                        }
                    } else {
                        # No tener concepto es una falla
                        echo "Se encontro que ".$co['tipo']." ".$co['serie']." ".$co['numero']." no tiene el elemento conceptos";
                        die();
                    }
                }
            }
            $recibo['detalle'][$k]['comprobante'] = $cmpbnt;
            if (isset($recibo['detalle'][$k]['comprobante']['inmueble'])) {
                $recibo['detalle'][$k]['comprobante']['inmueble'] = $f->model('in/inmu')->params(array('_id'=>$recibo['detalle'][$k]['comprobante']['inmueble']['_id']))->get('one')->items;
            }
        }

        if (isset($recibo['vouchers'])) {
            foreach ($recibo['vouchers'] as $voucher) {
                if ($voucher['cuenta_banco']['cod']=='101-089983') {
                    $total_detraccion+=floatval($voucher['monto']);
                }
            }
        }

        //DETALLE
        $detalle_tmp = array();
        foreach ($recibo['detalle'] as $i=>$item) {
            if (isset($item['cuenta'])) {
                if (!isset($detalle_tmp[$item['cuenta']['cod']])) {
                    $detalle_tmp[$item['cuenta']['cod']]=$item['cuenta'];
                    $detalle_tmp[$item['cuenta']['cod']]['total'] = 0;
                    $detalle_tmp[$item['cuenta']['cod']]['items'] = array();
                }
                if (!isset($detalle_tmp[$item['cuenta']['cod']]['items'][$item['comprobante']['tipo']."_".$item['comprobante']['_id']->{'$id'}.$i])) {
                    $detalle_tmp[$item['cuenta']['cod']]['items'][$item['comprobante']['tipo']."_".$item['comprobante']['_id']->{'$id'}.$i] = $item;
                }
                $detalle_tmp[$item['cuenta']['cod']]['total']+=floatval($item['monto']);
            }
        }

        if ($total_detraccion>0) {
            if (!isset($detalle_tmp['2101.010501.'])) {
                $detalle_tmp['2101.010501.']=array(
                    '_id'=>new MongoId("53346aefee6f96841d000113"),
                    'cod'=>'2101.010501.',
                    'descr'=>'Unidad de Inmuebles - detraccion'
                );
                $detalle_tmp['2101.010501.']['total'] = $total_detraccion;
                $detalle_tmp['2101.010501.']['items']['45s56as45sq']=array(
                    '_id'=>new MongoId(),
                    'detraccion'=>true,
                    'monto'=>$total_detraccion,
                    'cuenta'=>array(
                        '_id'=>new MongoId("53346aefee6f96841d000113"),
                        'cod'=>'2101.010501.',
                        'descr'=>'Unidad de Inmuebles - detraccion'
                    )
                );
            } else {
                $detalle_tmp['2101.010501.']['total']+=$total_detraccion;
                $detalle_tmp['2101.010501.']['items']['45s56as45sq']=array(
                    '_id'=>new MongoId(),
                    'detraccion'=>true,
                    'monto'=>$total_detraccion,
                    'cuenta'=>array(
                        '_id'=>new MongoId("53346aefee6f96841d000113"),
                        'cod'=>'2101.010501.',
                        'descr'=>'Unidad de Inmuebles - detraccion'
                    )
                );
            }
        }

        $detalle = array();
        $detrac_flag = 0;
        foreach ($model->items as $i=>$item) {
            if (strlen($item["cod"])>2) {
                $find_acum = preg_filter("/^".$item["cod"]."/", '$0', array_keys($detalle_tmp));
                if (count($find_acum)>0) {
                    $cuenta = $item;
                    $cuenta['total'] = 0;
                    $cuenta['items'] = array();
                    foreach ($find_acum as $row) {
                        if (count($detalle_tmp[$row]['items'])>0) {
                            if (!isset($item['cuentas']['hijos'])) {
                                if ($item["cod"]!=$row) {
                                    continue;
                                }
                            }
                        }
                        if ($item["cod"]=='2101.010501') {
                            if ($row!='2101.010501.') {
                                $cuenta["total"] += $detalle_tmp[$row]['total'];
                                if ($item["cod"] == $row) {
                                    $sort_items = $detalle_tmp[$row]['items'];
                                    krsort($sort_items);
                                    array_push($cuenta['items'], $sort_items);
                                }
                            }
                        } else {
                            $cuenta["total"] += $detalle_tmp[$row]['total'];
                            if ($item["cod"] == $row) {
                                $sort_items = $detalle_tmp[$row]['items'];
                                krsort($sort_items);
                                array_push($cuenta['items'], $sort_items);
                            }
                        }
                        if ($item["cod"]=='2101.010501') {
                            if ($detrac_flag==0&&$total_detraccion>0) {
                                array_push($detalle, array(
                                    '_id'=>new MongoId("53346aefee6f96841d000113"),
                                    'cod'=>'2101.010501.',
                                    'descr'=>'Unidad de Inmuebles - detraccion',
                                    'total'=>$detalle_tmp['2101.010501.']['total'],
                                    'items'=>array($detalle_tmp['2101.010501.']['items'])
                                ));
                                $detrac_flag++;
                            }
                        }
                    }
                    if ($cuenta['total']>0) {
                        array_push($detalle, $cuenta);
                    }
                }
            }
        }

        foreach ($detalle as $i=>$item) {
            if ($this->execute_startsWith($item["cod"], "2101")) {
                if ($item["cod"]!="2101.010501.") {
                    $detalle[$i]["total"]-=$total_detraccion;
                }
            }
        }

        $recibo['detalle2'] = array();

        foreach ($detalle as $item) {
            $col_1 = '';
            $col_2 = $item['cod'];
            $col_3 = $item['descr'];
            $col_4 = '';
            $col_5 = '';
            $col_6 = '';
            $col_7 = '';
            $col_7 = number_format($item['total'], 2);
            $col_8 = '';
            $_item = array(
                        'col_1'=>array(
                            'opt'=>array(
                                'type'=>'',
                                'size'=>11,
                                'align'=>'L',
                                'w'=>25
                            ),
                            'value'=>$col_1
                        ),
                        'col_2'=>array(
                            'opt'=>array(
                                'type'=>'B',
                                'size'=>9,
                                'align'=>'L',
                                'w'=>30
                            ),
                            'value'=>$col_2
                        ),
                        'col_3'=>array(
                            'opt'=>array(
                                'type'=>'B',
                                'size'=>9,
                                'align'=>'L',
                                'w'=>110//96
                            ),
                            'value'=>$col_3
                        ),
                        'col_4'=>array(
                            'opt'=>array(
                                'type'=>'',
                                'size'=>11,
                                'align'=>'L',
                                'w'=>80//25
                            ),
                            'value'=>$col_4
                        ),
                        'col_5'=>array(
                            'opt'=>array(
                                'type'=>'',
                                'size'=>11,
                                'align'=>'L',
                                'w'=>25
                            ),
                            'value'=>$col_5
                        ),
                        'col_6'=>array(
                            'opt'=>array(
                                'type'=>'',
                                'size'=>11,
                                'align'=>'L',
                                'w'=>25
                            ),
                            'value'=>$col_6
                        ),
                        'col_7'=>array(
                            'opt'=>array(
                                'type'=>'B',
                                'size'=>10,
                                //'size'=>8,
                                'align'=>'R',
                                'w'=>18
                            ),
                            'value'=>$col_7
                        ),
                        'col_8'=>array(
                            'opt'=>array(
                                'type'=>'B',
                                //'size'=>8,
                                'size'=>10,
                                'align'=>'R',
                                'w'=>18
                            ),
                            'value'=>$col_8
                        ),
                        'cuenta'=>true,
                        'cuenta_detalle'=>$col_2,
                        'cuenta_monto'=>$col_7,
                        'hidden'=>false
                    );
            if (strlen($item['cod'])>=14 || $col_2=='2103.03.47.3' || $col_2=='1202.0901.47' || $col_2=='2101.010501') {
                $_item['col_2']['opt']['type'] = 'BU';
                $_item['col_3']['opt']['type'] = 'BU';
            }
            if (strlen($item['cod'])==4) {
                $_item['col_7']['value'] = '';
                $_item['col_8']['value'] = number_format($item['total'], 2);
            }
            array_push($recibo['detalle2'], $_item);

            /*ELEMENTOS */
            if (count($item['items'])>0) {
                foreach ($item['items'] as $comp) {
                    $col_1 = '';
                    $col_2 = '';
                    $col_3 = '';
                    $col_4 = '';
                    $col_5 = '';
                    $col_6 = '';
                    $col_7 = '';
                    $col_8 = '';
                    foreach ($comp as $det) {
                        $col_1 = $det['comprobante']['tipo'].' '.$det['comprobante']['serie'].'-'.$det['comprobante']['numero'];
                        $col_2 = $det['comprobante']['cliente_nomb'];

                        $_col_4='';
                        foreach ($det['comprobante']['items'] as $i => $det_item) {
                            $_col_4 = ''.$_col_4.$helper->format_word($det_item['descr'])."; ";
                            if (isset($det_item['conceptos'])) {
                                foreach ($det_item['conceptos'] as $j => $item_conc) {
                                }
                            }
                        }
                        $col_4 = $_col_4;
                        if (isset($det['comprobante']['moneda']) && $det['comprobante']['moneda']=='USD') {
                            $col_5.=" T.C. ".$det['comprobante']['tipo_cambio'];
                        }
                        $col_6 = number_format($det['monto'], 2);

                        $_item = array(
                            'col_1'=>array(
                                'opt'=>array(
                                    'type'=>'',
                                    'size'=>9,
                                    'align'=>'R',
                                    'w'=>25
                                ),
                                'value'=>$col_1
                            ),
                            'col_2'=>array(
                                'opt'=>array(
                                    'type'=>'',
                                    'size'=>7,
                                    'align'=>'L',
                                    'w'=>60
                                ),
                                'value'=>$helper->format_word($col_2)
                            ),
                            'col_3'=>array(
                                'opt'=>array(
                                    'type'=>'',
                                    'size'=>8,
                                    'align'=>'L',
                                    'w'=>25
                                ),
                                'value'=>$col_3
                            ),
                            'col_4'=>array(
                                'opt'=>array(
                                    'type'=>'',
                                    'size'=>6,
                                    'align'=>'L',
                                    'w'=>57
                                ),
                                'value'=>($col_4),
                            ),
                            'col_5'=>array(
                                'opt'=>array(
                                    'type'=>'',
                                    'size'=>7,
                                    'align'=>'L',
                                    'w'=>25
                                ),
                                'value'=>$helper->format_word($col_5)
                            ),
                            'col_6'=>array(
                                'opt'=>array(
                                    'type'=>'',
                                    'size'=>9,
                                    'align'=>'R',
                                    'w'=>25
                                ),
                                'value'=>$col_6
                            ),
                            'col_7'=>array(
                                'opt'=>array(
                                    'type'=>'',
                                    'size'=>9,
                                    'align'=>'R',
                                    'w'=>25
                                ),
                                'value'=>$col_7
                            ),
                            'col_8'=>array(
                                'opt'=>array(
                                    'type'=>'',
                                    'size'=>9,
                                    'align'=>'R',
                                    'w'=>25
                                ),
                                'value'=>$col_8
                            ),
                            'partial'=>true,
                            'partial_monto'=>$det['monto'],
                            'hidden'=>false
                        );
                        if ($det['cuenta']['cod']=='2101.010503.47') {
                            $_item['hidden'] = true;
                        }
                        if ($det['cuenta']['cod']=='2101.0105') {
                            $_item['hidden'] = true;
                        }
                        if ($det['cuenta']['cod']=='1202.0901.47') {
                            $_item['hidden'] = true;
                        }
                        if ($det['cuenta']['cod']=='2101.010501') {
                            $_item['hidden'] = true;
                        }
                        if ($det['cuenta']['cod']=='1202.0902') {
                            $_item['hidden'] = true;
                        }
                        if (isset($det['detraccion'])) {
                            $_item['hidden'] = true;
                        }
                        array_push($recibo['detalle2'], $_item);
                    }
                }
            }
        }
        unset($recibo['detalle']);
        $ctasp = array();
        $ctast = array();
        foreach ($recibo['cont_patrimonial'] as $k => $row) {
            $ctasp[$k] = substr($row['cuenta']['cod'], 0, 9);//$row['cuenta']['cod'];
            $ctast[$k] = $row['tipo'];
        }
        array_multisort($ctast, SORT_ASC, $ctasp, SORT_DESC, $recibo['cont_patrimonial']);

        if (isset($f->request->data['debug'])) {
            die();
        }
        $f->response->view("cj/repo.rein2.print", array('recibo'=>$recibo));
    }
    public function execute_startsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    public function execute_boleta()
    {
        global $f;
        $f->response->view('cj/ecom.boleta');
    }
    public function execute_edit()
    {
        global $f;
        $f->response->view('cj/ecom.edit');
    }
    public function execute_row_configuration()
    {
        global $f;
        $f->response->view('cj/ecom.row_configuration');
    }
    public function execute_confirm_edit()
    {
        global $f;
        $f->response->view('cj/ecom.preview');
    }
    public function execute_gen_bootstrap()
    {
        global $f;
        $f->response->view("cj/ecom.gen.bootstrap");
    }
    public function execute_print_preview()
    {
        global $f;
        $data = $f->model("cj/ecom")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one")->items;
        if (isset($f->request->data['debug'])) {
            header("Content-type:application/json");
            echo json_encode($data);
            die();
        }
        if (!isset($data['numero'])) {
            $data['numero'] = '';
        }
        if ($data['numero']==null) {
            $data['numero'] = '';
        }
        $f->response->view('cj/ecom.print', array('data'=>$data,'ruta_pdf'=>null));
    }

    public function execute_baja_edit()
    {
        global $f;
        $f->response->view('cj/ecom.razon.baja');
    }
    public function execute_eliminar()
    {
        global $f;
        $model = $f->model('cj/ecom')->params(array("_id"=>$f->request->data['_id']))->delete('ecom');
        $f->model('ac/log')->params(array(
            'modulo'=>'CJ',
            'bandeja'=>'Pago de comprobante electr&oacute;nico',
            'descr'=>'Se elimin&oacute; un borrador de comprobante electr&oacute;nico. '
        ))->save('insert');
        $f->response->print("true");
    }
}
