<?php
//define('APPPATH',IndexPath.DS);
require_once APPPATH.'/libraries/conflux/ConfluxException.php';
class ConfluxInvoice {
	public function validate($data){
		try {
            $documento = array(
                //'porcentaje_igv'=>$model['porcentaje_igv'],
                'cliente_domic'=>'',
                'total_isc'=>0,
                'total_igv'=>0,
                'total_otros_tributos'=>0,
                'total_otros_cargos'=>0,
                'total_ope_inafectas'=>0,
                'total_ope_gravadas'=>0,
                'total_desc'=>0,
                //'desc_global'=>0,
                'total_ope_exoneradas'=>0,
                'total_ope_gratuitas'=>0,
                'total'=>0,
                'total_detraccion'=>0,
                'porcentaje_detraccion'=>0,
                'tipo_cambio'=>0,
                'items'=>array(),
                'estado'=>'BO',
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
            );
            $model = $data;
            if(!isset($model['tipo'])){
                throw new ConfluxException("El tipo de documento es obligatorio");
            }else{
                if(!in_array($model['tipo'], array('F','B','NC','ND'), true)){
                    throw new ConfluxException("El tipo de documento enviado es invalido");
                }else{
                    $documento['tipo'] = $model['tipo'];
                    if($model['tipo']=='F'||$model['tipo']=='B'){

                        if(!isset($model['tipo_oper'])){
                            throw new ConfluxException("El tipo de operacion de documento es obligatorio");
                        }else{
                            /*
                            01 Venta lnterna
                            02 Exportación
                            03 No Domiciliados
                            04 Venta Interna – Anticipos
                            05 Venta Itinerante
                            06 Factura Guía
                            07 Venta Arroz Pilado
                            08 Factura - Comprobante de Percepción
                            */

                            if(!in_array($model['tipo_oper'], array("01","02","03","04","05","06","07","08"), true)){
                                throw new ConfluxException("El tipo de operacion del documento enviado es invalido");
                            }else{
                                $documento['tipo_oper'] = $model['tipo_oper'];
                            }
                        }

                        if(!isset($model['descuento_global'])){
                            $model['descuento_global'] = 0;
                            //throw new ConfluxException("El descuento global es obligatorio");
                        }else{
                            if(!is_numeric($model['descuento_global'])){
                                throw new ConfluxException("El descuento global debe ser un valor numerico");
                            }
                            $model['descuento_global'] = floatval($model['descuento_global']);
                        }
                        $documento['descuento_global'] = $model['descuento_global'];

                        if($model['tipo']=='F'){
                            if(!isset($model['total_detraccion'])){
                                $model['total_detraccion'] = 0;
                            }else{
                                if(!is_numeric($model['total_detraccion'])){
                                    throw new ConfluxException("El total de detraccion debe ser un valor numerico");
                                }else{
                                    $model['total_detraccion'] = floatval($model['total_detraccion']);
                                }
                            }
                            $documento['total_detraccion'] = $model['total_detraccion'];

                            if(!isset($model['porcentaje_detraccion'])){
                                $model['porcentaje_detraccion'] = 0;
                            }else{
                                if(!is_numeric($model['porcentaje_detraccion'])){
                                    throw new ConfluxException("El porcentaje de detraccion debe ser un valor numerico");
                                }else{
                                    $model['porcentaje_detraccion'] = floatval($model['porcentaje_detraccion']);
                                }
                            }
                            $documento['porcentaje_detraccion'] = $model['porcentaje_detraccion'];

                            if(!isset($model['cliente_domic'])){
                                throw new ConfluxException("La direccion del cliente es obligatorio en caso de factura");
                            }else{
                                if($model['cliente_domic']==''){
                                    throw new ConfluxException("La direccion del cliente no puede ser una cadena vacia");
                                }
                            }
                            $documento['cliente_domic'] = $model['cliente_domic'];
                        }

                        if(isset($model['orden_compra'])){
                            if(count($model['orden_compra'])>0){
                                $documento['orden_compra'] = array();
                                foreach($model['orden_compra'] as $oi=>$orden){
                                    if(!isset($orden['numero'])){
                                        throw new ConfluxException("El campo numero en la orden de compra ".$oi." referenciada es obligatorio");
                                    }else{
                                        if(trim($orden['numero'])==''){
                                            throw new ConfluxException("El campo numero en la orden de compra ".$oi." referenciada no puede ser vacio");
                                        }
                                    }
                                    $documento['orden_compra'][] = array('numero'=>$orden['numero']);
                                }
                            }
                        }

                        if(isset($model['guia_remision'])){
                            if(count($model['guia_remision'])>0){
                                $documento['guia_remision'] = array();
                                foreach($model['guia_remision'] as $oi=>$guia){
                                    if(!isset($guia['numero'])){
                                        throw new ConfluxException("El campo numero en la guia de remision ".$oi." referenciada es obligatorio");
                                    }else{
                                        if(trim($guia['numero'])==''){
                                            throw new ConfluxException("El campo numero en la guia de remision ".$oi." referenciada no puede ser vacio");
                                        }
                                    }

                                    if(!isset($guia['tipo'])){
                                        throw new ConfluxException("El campo numero en la guia de remision ".$oi." referenciada es obligatorio");
                                    }else{
                                        if(!in_array($guia['numero'], array('09','31'))){
                                            throw new ConfluxException("El campo numero en guia de remision ".$oi." referenciada tiene un valor invalido");
                                        }
                                    }
                                    $documento['guia_remision'][] = array('numero'=>$guia['numero'],'tipo'=>$guia['tipo']);
                                }
                            }
                        }

                        if(isset($model['placa_vehiculo'])){
                            if(count($model['placa_vehiculo'])>0){
                                $documento['placa_vehiculo'] = array();
                                foreach($model['placa_vehiculo'] as $oi=>$placa){
                                    if(!isset($placa['codigo'])){
                                        throw new ConfluxException("El campo numero de placa ".$oi." referenciada es obligatorio");
                                    }else{
                                        if(trim($placa['codigo'])==''){
                                            throw new ConfluxException("El campo numero de placa ".$oi." referenciada no puede ser vacio");
                                        }
                                    }
                                    $documento['placa_vehiculo'][] = array('codigo'=>$placa['codigo']);
                                }
                            }
                        }
                    }
                }
            }

            if(!isset($model['fecemi'])){
                $model['fecemi'] = date('Y-m-d');
            }else{
                if(DateTime::createFromFormat('Y-m-d', $model['fecemi'])==FALSE){
                    throw new ConfluxException("La fecha de emision debe ser tener el formato YYYY-MM-DD");
                }
            }
            $documento['fecemi'] = new MongoDate(strtotime($model['fecemi']));

            if(!isset($model['fecven'])){
                $model['fecven'] = date('Y-m-d');
            }else{
                if(DateTime::createFromFormat('Y-m-d', $model['fecven'])==FALSE){
                    throw new ConfluxException("La fecha de vencimiento debe ser tener el formato YYYY-MM-DD");
                }
            }
            $documento['fecven'] = new MongoDate(strtotime($model['fecven']));

            if(!isset($model['serie'])){
                throw new ConfluxException("La serie es obligatoria");
            }
            $documento['serie'] = $model['serie'];

            if(!isset($model['moneda'])){
                throw new ConfluxException("La moneda es obligatorio");
            }else{
                if(!in_array($model['moneda'], array('PEN','USD'), true)){
                    throw new ConfluxException("La moneda enviada es invalida");
                }else{
                    if($model['moneda']!='PEN'){
                        if(!isset($model['tipo_cambio'])){
                            throw new ConfluxException("La operacion es en moneda extranjera por lo tanto el tipo de cambio es obligatorio");
                        }else{
                            if(!is_numeric($model['tipo_cambio'])){
                                throw new ConfluxException("El tipo de cambio debe ser un valor numerico");
                            }
                            $model['tipo_cambio'] = floatval($model['tipo_cambio']);
                            $documento['tipo_cambio'] = $model['tipo_cambio'];
                        }
                    }
                }
            }
            $documento['moneda'] = $model['moneda'];
        
            if(!isset($model['cliente_nomb'])){
                throw new ConfluxException("El nombre/razon social del cliente es obligatorio");
            }else{
                if($model['cliente_nomb']==''){
                    throw new ConfluxException("El nombre/razon social del cliente no puede ser una cadena vacia incluso si la venta es anonima");
                }
            }
            $documento['cliente_nomb'] = $model['cliente_nomb'];

            if(!isset($model['cliente_doc'])){
                throw new ConfluxException("El documento de identidad del cliente es obligatorio");
            }
            $documento['cliente_doc'] = $model['cliente_doc'];

            if(!isset($model['tipo_doc'])){
                throw new ConfluxException("El tipo de documento de identidad del cliente es obligatorio");
            }else{
                if(!in_array($model['tipo_doc'], array('0','DNI','RUC','CED','PAS','CAE'), true)){
                    throw new ConfluxException("El tipo de documento de identidad del cliente enviado es invalido");
                }else{
                    if($model['tipo_doc']!='0'){
                        if($model['cliente_doc']==''){
                            throw new ConfluxException("El documento de identidad del cliente no puede ser vacio cuando el cliente no es anonimom");
                        }
                        if($model['tipo_doc']=='DNI'){
                            if(strlen($model['cliente_doc'])!=8){
                                throw new ConfluxException("El documento de identidad del cliente debe tener 8 caracteres");
                            }
                        }
                        if($model['tipo_doc']=='RUC'){
                            if(strlen($model['cliente_doc'])!=11){
                                throw new ConfluxException("El documento de identidad del cliente debe tener 11 caracteres");
                            }
                        }
                    }
                }
            }
            $documento['tipo_doc'] = $model['tipo_doc'];

            if($model['tipo']=='NC' || $model['tipo']=='ND'){
                if(!isset($model['documento_modificar'])){
                    throw new ConfluxException("El documento es de tipo Nota de credito/debito, por lo tanto debeb tener el capo doc_original");
                }else{
                    if(!isset($model['documento_modificar']['tipo'])){
                        throw new ConfluxException("El campo de tipo del documento a modificar en caso de notas es obligatorio");  
                    }
                    if(!isset($model['documento_modificar']['serie'])){
                        throw new ConfluxException("El campo serie del documento a modificar de la nota es obligatorio");
                    }
                    if(!isset($model['documento_modificar']['numero'])){
                        throw new ConfluxException("El campo numero del documento a modificar de la nota es obligatorio");
                    }
                    if(!isset($model['documento_modificar']['motivo'])){
                        throw new ConfluxException("El campo motivo del documento a modificar de la nota es obligatorio");
                    }
                    if(!isset($model['documento_modificar']['motivo_descripcion'])){
                        throw new ConfluxException("El campo descripcion del motivo del documento a modificar de la nota es obligatorio");
                    }else{
                        if($model['tipo']=='NC'){
                            /*
                            01 Anulación de la operación
                            02 Anulación por error en el RUC
                            03 Corrección por error en la descripción
                            04 Descuento global
                            05 Descuento por ítem
                            06 Devolución total
                            07 Devolución por ítem
                            08 Bonificación
                            09 Disminución en el valor
                            10 Otros Conceptos
                            */
                            if(!in_array($model['documento_modificar']['motivo'], array('01','02','03','04','05','05','06','07','08','09','10'), true)){
                                throw new ConfluxException("El moitvo de la nota de credito enviado es invalido");
                            }
                        }elseif($model['tipo']=='ND'){
                            /*
                            01 Intereses por mora
                            02 Aumento en el valor
                            03 Penalidades/ otros conceptos
                            */
                            if(!in_array($model['documento_modificar']['motivo'], array('01','02','03'), true)){
                                throw new ConfluxException("El motivo de la nota de debito enviado es invalido");
                            }
                        }
                    }
                    $documento['documento_modificar'] = $model['documento_modificar'];
                }
            }

            $documento['tipo'] = $model['tipo'];

            if(!isset($model['porcentaje_igv'])){
                $model['porcentaje_igv'] = 18;
            }else{
                if(!is_numeric($model['porcentaje_igv'])){
                    throw new ConfluxException("El campo porcentaje de igv debe ser un valor numerico");
                }
                $model['porcentaje_igv'] = floatval($model['porcentaje_igv']);
            }
            $documento['porcentaje_igv'] = $model['porcentaje_igv'];
            
            if(!isset($model['items'])){
                throw new ConfluxException("El documento no tiene items");
            }else{
                if(count($model['items'])==0){
                    throw new ConfluxException("El documento debe tener al menos un item");
                }else{
                    foreach($model['items'] as $i=>$item){
                        $_item = array();

                        if(!isset($item['codigo'])){
                            throw new ConfluxException("En el item ".$i." no se ha encontrado el campo Codigo");
                        }else{
                            if(strlen($item['codigo'])>30){
                                throw new ConfluxException("En el item ".$i." el codio no debe exceder los 30 caracteres");
                            }
                        }
                        $_item['codigo'] = $item['codigo'];

                        if(!isset($item['descr'])){
                            throw new ConfluxException("En el item ".$i." no se ha encontrado el campo Descripcion");
                        }else{
                            if(strlen($item['descr'])>250){
                                throw new ConfluxException("En el item ".$i." la descripcion no debe exceder los 250 caracteres");
                            }
                        }
                        $_item['descr'] = $item['descr'];

                        if(!isset($item['cod_unidad'])){
                            throw new ConfluxException("En el item ".$i." no se ha encontrado el campo Codigo de unidad de medida");
                        }else{
                            if(strlen($item['cod_unidad'])>4){
                                throw new ConfluxException("En el item ".$i." el codigo de unidad de medida no debe exceder los 4 caracteres");
                            }
                        }
                        $_item['cod_unidad'] = $item['cod_unidad'];

                        if(!isset($item['unidad'])){
                            throw new ConfluxException("En el item ".$i." no se ha encontrado el campo unidad de medida");
                        }else{
                            if(strlen($item['unidad'])>100){
                                throw new ConfluxException("En el item ".$i." la unidad de medida no debe exceder los 100 caracteres");
                            }
                        }
                        $_item['unidad'] = $item['unidad'];

                        if(!isset($item['cant'])){
                            throw new ConfluxException("En el item ".$i." no se ha encontrado el campo cantidad");
                        }else{
                            if(!is_numeric($item['cant'])){
                                throw new ConfluxException("En el item ".$i." la cantidad debe ser un valor numerico");
                            }else{
                                $item['cant'] = floatval($item['cant']);
                                $model['items'][$i]['cant'] = $item['cant'];
                                if($item['cant']<=0){
                                    throw new ConfluxException("En el item ".$i." la cantidad debe ser un numero mayor a cero");
                                }
                            }
                        }
                        $_item['cant'] = $item['cant'];

                        if(!isset($item['igv'])){
                            throw new ConfluxException("En el item ".$i." no se ha encontrado el campo igv");
                        }else{
                            if(!is_numeric($item['igv'])){
                                throw new ConfluxException("En el item ".$i." el campo igv debe ser un valor numerico");
                            }
                            $item['igv'] = floatval($item['igv']);
                            $model['items'][$i]['igv'] = $item['igv'];
                        }
                        $_item['igv'] = $item['igv'];

                        if(!isset($item['isc'])){
                            //throw new ConfluxException("En el item ".$i." no se ha encontrado el campo ISC");
                            $item['isc'] = 0;
                            $model['items'][$i]['isc'] = 0;
                        }else{
                            if(!is_numeric($item['isc'])){
                                throw new ConfluxException("En el item ".$i." el campo ISC debe ser un valor numerico");
                            }else{
                                $item['isc'] = floatval($item['isc']);
                                $model['items'][$i]['isc'] = $item['isc'];
                                if($item['isc']>0){
                                    if(!isset($item['tipo_isc'])){
                                        throw new ConfluxException("En el item ".$i." no se ha encontrado el tipo de calculo del ISC");
                                    }else{
                                        /*
                                        01 Sistema al valor (Apéndice IV, lit. A – T.U.O IGV e ISC)
                                        02 Aplicación del Monto Fijo (Apéndice IV, lit. B – T.U.O IGV e ISC)
                                        03 Sistema de Precios de Venta al Público (Apéndice IV, lit. C – T.U.O IGV e ISC)
                                        */
                                        if(!in_array($item['tipo_isc'], array('01','02','03'), true)){
                                            throw new ConfluxException("En el item ".$i." el tipo de sistema de calculo del ISC es incorrecto");
                                        }
                                    }
                                    $_item['tipo_isc'] = $item['tipo_isc'];
                                }
                            }
                        }
                        $_item['isc'] = $item['isc'];

                        if(!isset($item['descuento'])){
                            $item['descuento'] = 0;
                            $model['items'][$i]['descuento'] = 0;
                        }else{
                            if(!is_numeric($item['descuento'])){
                                throw new ConfluxException("En el item ".$i." el campo descuento debe ser un valor numerico");
                            }else{
                                $item['descuento'] = floatval($item['descuento']);
                                $model['items'][$i]['descuento'] = $item['descuento'];
                            }
                        }
                        $_item['descuento'] = $item['descuento'];

                        if(!isset($item['otros_tributos'])){
                            //throw new ConfluxException("En el item ".$i." no se ha encontrado el campo Otros tributos");
                            $item['otros_tributos'] = 0;
                            $model['items'][$i]['otros_tributos'] = 0;
                        }else{
                            if(!is_numeric($item['otros_tributos'])){
                                throw new ConfluxException("En el item ".$i." el campo Otros tributos debe ser un valor numerico");
                            }else{
                                $item['otros_tributos'] = floatval($item['otros_tributos']);
                                $model['items'][$i]['otros_tributos'] = $item['otros_tributos'];
                            }
                        }
                        $_item['otros_tributos'] = $item['otros_tributos'];
            
                        if(!isset($item['otros_cargos'])){
                            //throw new ConfluxException("En el item ".$i." no se ha encontrado el campo Otros cargos");
                            $item['otros_cargos'] = 0;
                            $model['items'][$i]['otros_cargos'] = 0;
                        }else{
                            if(!is_numeric($item['otros_cargos'])){
                                throw new ConfluxException("En el item ".$i." el campo Otros cargos debe ser un valor numerico");
                            }else{
                                $item['otros_cargos'] = floatval($item['otros_cargos']);
                                $model['items'][$i]['otros_cargos'] = $item['otros_cargos'];
                            }
                        }
                        $_item['otros_cargos'] = $item['otros_cargos'];

                        if(!isset($item['tipo_igv'])){
                            throw new ConfluxException("En el item ".$i." no se ha encontrado el campo tipo de afectacion igv");
                        }else{
                            /*
                            10 Gravado - Operación Onerosa
                            11 Gravado – Retiro por premio
                            12 Gravado – Retiro por donación
                            13 Gravado – Retiro
                            14 Gravado – Retiro por publicidad
                            15 Gravado – Bonificaciones
                            16 Gravado – Retiro por entrega a trabajadores
                            17 Gravado – IVAP
                            20 Exonerado - Operación Onerosa
                            21 Exonerado – Transferencia Gratuita
                            30 Inafecto - Operación Onerosa
                            31 Inafecto – Retiro por Bonificación 
                            32 Inafecto – Retiro
                            33 Inafecto – Retiro por Muestras Médicas
                            34 Inafecto - Retiro por Convenio Colectivo
                            35 Inafecto – Retiro por premio
                            36 Inafecto - Retiro por publicidad
                            40 Exportación
                            */
                            if(!in_array($item['tipo_igv'], array('10','11','12','13','14','15','16','17','20','21','30','31','32','33','34','35','36','40'), true)){
                                throw new ConfluxException("En el item ".$i." el tipo de afectacion al igv es incorrecto");
                            }else{
                                if($item['tipo_igv']=='10'){
                                    if($item['igv']==0){
                                        throw new ConfluxException("El item ".$i." tiene un tipo de afectacion al IGV indicando que debe tener un monto de IGV mayor a cero");
                                    }
                                }else{
                                    if($item['igv']!=0){
                                        throw new ConfluxException("El item ".$i." tiene un tipo de afectacion al IGV indicando que debe tener un monto de igv igual a cero");
                                    }
                                }
                            }
                        }
                        $_item['tipo_igv'] = $item['tipo_igv'];


                        /*******************************************
                        - valor_unitario

                        Se consignará el importe correspondiente al valor o monto unitario del bien vendido, cedidoo servicio prestado, indicado en una  línea o ítem de la factura. Este  importe no incluye  los tributos (IGV, ISC y otros Tributos) ni los cargos globales.
                        ********************************************/
                        if(!isset($item['valor_unitario'])){
                            throw new ConfluxException("En el item ".$i." no se ha encontrado el campo valor unitario");
                        }else{
                            if(!is_numeric($item['valor_unitario'])){
                                throw new ConfluxException("En el item ".$i." el campo valor unitario debe ser un valor numerico");
                            }else{
                                $item['valor_unitario'] = floatval($item['valor_unitario']);
                                $model['items'][$i]['valor_unitario'] = $item['valor_unitario'];
                                if(in_array($item['tipo_igv'], array('10','20','30','40'), true)){
                                    if($item['valor_unitario']<=0){
                                        throw new ConfluxException("En el item ".$i." el valor unitario debe ser mayor a cero segun el tipo de afectacion al igv enviado");
                                    }
                                }
                            }
                        }
                        $_item['valor_unitario'] = $item['valor_unitario'];

                        /********************************************
                        - precio_venta_unitario

                        es el monto correspondiente al precio unitario facturado  del  bien  vendido  o  servicio  vendido.  Este  monto  es  la  suma  total  que  queda obligado  a  pagar  el adquirente o  usuario por  cada bien o servicio. Esto incluye  los tributos (IGV, ISC y otros Tributos) y la deducción de descuentos por item
                        *********************************************/
                        if(!isset($item['precio_venta_unitario'])){
                            throw new ConfluxException("En el item ".$i." no se ha encontrado el campo precio de venta unitario");
                        }else{
                            if(!is_numeric($item['precio_venta_unitario'])){
                                throw new ConfluxException("En el item ".$i." el campo precio de venta unitario debe ser un valor numerico");
                            }else{
                                $item['precio_venta_unitario'] = floatval($item['precio_venta_unitario']);
                                $model['items'][$i]['precio_venta_unitario'] = $item['precio_venta_unitario'];
                                if(in_array($item['tipo_igv'], array('10','20','30','40'), true)){
                                    if($item['precio_venta_unitario']<=0){
                                        throw new ConfluxException("En el item ".$i." el precio de venta unitario debe ser mayor a cero segun el tipo de afectacion al igv enviado");
                                    }
                                }
                            }
                        }
                        $_item['precio_venta_unitario'] = $item['precio_venta_unitario'];

                        if(!isset($item['gratuito'])){
                            //throw new ConfluxException("En el item ".$i." no se ha encontrado el campo gratuito");
                            $item['gratuito'] = false;
                            $model['items'][$i]['gratuito'] = false;
                            $item['valor_gratuito'] = 0;
                            $model['items'][$i]['valor_gratuito'] = 0;
                        }else{
                            if(is_string($item['gratuito'])){
                                if(strtolower($item['gratuito'])==='true'){
                                    $item['gratuito'] = true;
                                    $model['items'][$i]['gratuito'] = true;
                                }elseif(strtolower($item['gratuito'])==='false'){
                                    $item['gratuito'] = false;
                                    $model['items'][$i]['gratuito'] = false;
                                }
                            }
                            if(!is_bool($item['gratuito'])){
                                throw new ConfluxException("En el item ".$i." el campo gratuito no es un dato tipo boolean");
                            }else{
                                if($item['gratuito']){
                                    if(!isset($item['valor_gratuito'])){
                                        throw new ConfluxException("En el item ".$i." no se ha encontrado el campo valor gratuito");
                                    }else{
                                        if(!is_numeric($item['valor_gratuito'])){
                                            throw new ConfluxException("En el item ".$i." el campo valor gratuito debe ser un valor numerico");
                                        }else{
                                            $item['valor_gratuito'] = floatval($item['valor_gratuito']);
                                            $model['items'][$i]['valor_gratuito'] = $item['valor_gratuito'];
                                            if($item['valor_gratuito']<=0){
                                                throw new ConfluxException("En el item ".$i." el campo valor gratuito debe ser un monto mayor a cero ya que el item especifica que es una operacion gratuita");
                                            }
                                            if($item['valor_unitario']!=0){
                                                throw new ConfluxException("En el item ".$i." el campo valor unitario debe ser un monto igual a cero ya que el item especifica que es una operacion gratuita");
                                            }
                                            if(!in_array($item['tipo_igv'], array('11','12','13','14','15','16','17','21','31','32','33','34','35','36'), true)){
                                                throw new ConfluxException("En el item ".$i." el tipo de afectacion al IGV enviado no corresponde a una operacion gratuita");
                                            }
                                        }
                                    }
                                }else{
                                    $item['valor_gratuito'] = 0;
                                    $model['items'][$i]['valor_gratuito'] = 0;
                                }
                            }
                        }
                        $_item['gratuito'] = $item['gratuito'];
                        $_item['valor_gratuito'] = $item['valor_gratuito'];

                        if(in_array($item['tipo_igv'],array('10','17'))){//verificar la validez del 17
                            $documento['total_ope_gravadas']+=$item['valor_unitario']*$item['cant']-$model['items'][$i]['descuento'];
                        }elseif(in_array($item['tipo_igv'],array('20'))){
                            $documento['total_ope_exoneradas']+=$item['valor_unitario']*$item['cant']-$model['items'][$i]['descuento'];
                        }elseif(in_array($item['tipo_igv'],array('30','40'))){
                            $documento['total_ope_inafectas']+=$item['valor_unitario']*$item['cant']-$model['items'][$i]['descuento'];
                        }elseif(in_array($item['tipo_igv'],array('11','12','13','14','15','16','21','31','32','33','34','35','36'), true)){
                            $documento['total_ope_gratuitas']+=$item['valor_gratuito']*$item['cant'];
                        }

                        $documento['total_igv']+=$item['igv'];
                        $documento['total_isc']+=$item['isc'];
                        $documento['total_otros_tributos']+=$item['otros_tributos'];
                        $documento['total_otros_cargos']+=$item['otros_cargos'];

                        //$model['items'][$i]['desc'] = 0;//floatval($model['items'][$i]['desc'])
                        $model['items'][$i]['valor_venta'] = $model['items'][$i]['cant']*$model['items'][$i]['valor_unitario']-$model['items'][$i]['descuento'];
                        $_item['valor_venta'] = $model['items'][$i]['cant']*$model['items'][$i]['valor_unitario']-$model['items'][$i]['descuento'];

                        $verify_precio_venta_unitario = $model['items'][$i]['valor_unitario']-($model['items'][$i]['descuento']/$model['items'][$i]['cant'])+($model['items'][$i]['igv']/$model['items'][$i]['cant'])+($model['items'][$i]['isc']/$model['items'][$i]['cant'])+($model['items'][$i]['otros_tributos']/$model['items'][$i]['cant']);

                        if(abs($model['items'][$i]['precio_venta_unitario']-$verify_precio_venta_unitario)>=1){
                            throw new ConfluxException("En el item ".$i." el precio de venta unitario enviado no coincide con nuestros calculos (tolerancia 1.00)");
                        }
                        $documento['items'][] = $_item;
                    }
                }
            }

            if(isset($model['total_ope_gravadas'])){
                if(!is_numeric($model['total_ope_gravadas'])){
                    throw new ConfluxException("El campo total de operaciones gravadas debe ser un valor numerico");
                }else{
                    $model['total_ope_gravadas'] = floatval($model['total_ope_gravadas']);
                    if($model['total_ope_gravadas']<0){
                        throw new ConfluxException("El campo total de operaciones gravadas debe tener un monto mayor o igual a cero");
                    }
                    $documento['total_ope_gravadas'] = $model['total_ope_gravadas'];
                }
            }

            if(isset($model['total_ope_exoneradas'])){
                if(!is_numeric($model['total_ope_exoneradas'])){
                    throw new ConfluxException("El campo total de operaciones exoneradas debe ser un valor numerico");
                }else{
                    $model['total_ope_exoneradas'] = floatval($model['total_ope_exoneradas']);
                    if($model['total_ope_exoneradas']<0){
                        throw new ConfluxException("El campo total de operaciones exoneradas debe tener un monto mayor o igual a cero");
                    }
                    $documento['total_ope_exoneradas'] = $model['total_ope_exoneradas'];
                }
            }

            if(isset($model['total_ope_inafectas'])){
                if(!is_numeric($model['total_ope_inafectas'])){
                    throw new ConfluxException("El campo total de operaciones inafectas debe ser un valor numerico");
                }else{
                    $model['total_ope_inafectas'] = floatval($model['total_ope_inafectas']);
                    if($model['total_ope_inafectas']<0){
                        throw new ConfluxException("El campo total de operaciones inafectas debe tener un monto mayor o igual a cero");
                    }
                    $documento['total_ope_inafectas'] = $model['total_ope_inafectas'];
                }
            }

            if(isset($model['total_ope_gratuitas'])){
                if(!is_numeric($model['total_ope_gratuitas'])){
                    throw new ConfluxException("El campo total de operaciones gratuitas debe ser un valor numerico");
                }else{
                    $model['total_ope_gratuitas'] = floatval($model['total_ope_gratuitas']);
                    if($model['total_ope_gratuitas']<0){
                        throw new ConfluxException("El campo total de operaciones gratuitas debe tener un monto mayor o igual a cero");
                    }
                    $documento['total_ope_gratuitas'] = $model['total_ope_gratuitas'];
                }
            }

            if(isset($model['total_igv'])){
                if(!is_numeric($model['total_igv'])){
                    throw new ConfluxException("El campo total igv debe ser un valor numerico");
                }else{
                    $model['total_igv'] = floatval($model['total_igv']);
                    if($model['total_igv']<0){
                        throw new ConfluxException("El campo total igv debe tener un monto mayor o igual a cero");
                    }
                    $documento['total_igv'] = $model['total_igv'];
                }
            }

            if(isset($model['total_isc'])){
                if(!is_numeric($model['total_isc'])){
                    throw new ConfluxException("El campo total isc debe ser un valor numerico");
                }else{
                    $model['total_isc'] = floatval($model['total_isc']);
                    if($model['total_isc']<0){
                        throw new ConfluxException("El campo total isc debe tener un monto mayor o igual a cero");
                    }
                    $documento['total_isc'] = $model['total_isc'];
                }
            }

            if(isset($model['total_otros_tributos'])){
                if(!is_numeric($model['total_otros_tributos'])){
                    throw new ConfluxException("El campo total otros tributos debe ser un valor numerico");
                }else{
                    $model['total_otros_tributos'] = floatval($model['total_otros_tributos']);
                    if($model['total_otros_tributos']<0){
                        throw new ConfluxException("El campo total otros tributos debe tener un monto mayor o igual a cero");
                    }
                    $documento['total_otros_tributos'] = $model['total_otros_tributos'];
                }
            }

            if(isset($model['total_otros_cargos'])){
                if(!is_numeric($model['total_otros_cargos'])){
                    throw new ConfluxException("El campo total otros cargos debe ser un valor numerico");
                }else{
                    $model['total_otros_cargos'] = floatval($model['total_otros_cargos']);
                    if($model['total_otros_cargos']<0){
                        throw new ConfluxException("El campo total otros cargos debe tener un monto mayor o igual a cero");
                    }
                    $documento['total_otros_cargos'] = $model['total_otros_cargos'];
                }
            }

            if($documento['total_igv']>0){
                if(abs(($documento['total_ope_gravadas']*$documento['porcentaje_igv']/100)-$documento['total_igv'])>=1){
                    throw new ConfluxException("El calculo del IGV calculado por el sistema excede el limite de tolerancia del total del igv enviado");
                }
            }

            $documento['total_desc']+=$model['descuento_global'];
            $documento['total_desc']=floatval($documento['total_desc']);
            $documento['total']=$documento['total_ope_gravadas']+$documento['total_ope_inafectas']+$documento['total_ope_exoneradas']+$documento['total_isc']+$documento['total_igv']+$documento['total_otros_tributos']+$documento['total_otros_cargos'];
            /*if($documento['total']<=0){
                throw new ConfluxException("La sumatoria ha calculado un monto menor o igual a cero");
            }*/
            if(isset($model['total'])){
                if(!is_numeric($model['total'])){
                    throw new ConfluxException("El campo total debe ser un valor numerico");
                }else{
                    $model['total'] = floatval($model['total']);
                    if($model['total']<0){
                        throw new ConfluxException("El campo total debe tener un monto mayor o igual a cero");
                    }
                    $documento['total'] = $model['total'];
                }
            }
            return array(
            	'status'=>'success',
            	'success_message'=>'La informacion enviada fue validada correctamente',
            	'success_data'=>$documento
            );
        } catch (ConfluxException $e) {
            return array(
                'status'=>'error',
                'error_message'=>$e->getMessage(),
                'error_data'=>$e->getData()
            );
        }
	}
}
?>