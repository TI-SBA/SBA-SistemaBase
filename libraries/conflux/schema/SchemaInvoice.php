<?php
//define('APPPATH',IndexPath.DS);
require_once APPPATH.'libraries/conflux/ConfluxException.php';
require_once APPPATH.'libraries/conflux/ConfluxConfig.php';
class SchemaInvoice {
	
	public function generate($data, $filename, $config){
        global $f;
        $ci = new ConfluxConfig();
		$xml = new DOMDocument("1.0", "ISO-8859-1");
		$Invoice = $xml->appendChild($xml->createElement("Invoice"));
        $Invoice->appendChild($xml->createAttribute("xmlns"))
            ->appendChild($xml->createTextNode('urn:oasis:names:specification:ubl:schema:xsd:Invoice-2'));

        $Invoice->appendChild($xml->createAttribute("xmlns:cac"))
            ->appendChild($xml->createTextNode('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2'));

        $Invoice->appendChild($xml->createAttribute("xmlns:cbc"))
            ->appendChild($xml->createTextNode('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2'));

        $Invoice->appendChild($xml->createAttribute("xmlns:ccts"))
            ->appendChild($xml->createTextNode('urn:un:unece:uncefact:documentation:2'));

        $Invoice->appendChild($xml->createAttribute("xmlns:ds"))
            ->appendChild($xml->createTextNode('http://www.w3.org/2000/09/xmldsig#'));

        $Invoice->appendChild($xml->createAttribute("xmlns:ext"))
            ->appendChild($xml->createTextNode('urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2'));

        $Invoice->appendChild($xml->createAttribute("xmlns:qdt"))
            ->appendChild($xml->createTextNode('urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2'));

        $Invoice->appendChild($xml->createAttribute("xmlns:sac"))
            ->appendChild($xml->createTextNode('urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1'));

        $Invoice->appendChild($xml->createAttribute("xmlns:udt"))
            ->appendChild($xml->createTextNode('urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2'));

        $Invoice->appendChild($xml->createAttribute("xmlns:xsi"))
            ->appendChild($xml->createTextNode('http://www.w3.org/2001/XMLSchema-instance'));

        $Invoice->appendChild($xml->createAttribute("xmlns:xsd"))
            ->appendChild($xml->createTextNode('http://www.w3.org/2001/XMLSchema'));


        $ext_UBLExtensions = $Invoice->appendChild($xml->createElement("ext:UBLExtensions"));
        $ext_UBLExtension = $ext_UBLExtensions->appendChild($xml->createElement("ext:UBLExtension"));
        $ext_ExtensionContent = $ext_UBLExtension->appendChild($xml->createElement("ext:ExtensionContent"));
        $sac_AdditionalInformation = $ext_ExtensionContent->appendChild($xml->createElement("sac:AdditionalInformation"));

        $ext_UBLExtension = $ext_UBLExtensions->appendChild($xml->createElement("ext:UBLExtension"));
        $ext_ExtensionContent = $ext_UBLExtension->appendChild($xml->createElement("ext:ExtensionContent"));

        /*****************************
        * DETALLE DEL DOCUMENTO
        ******************************/
        $number = new Number();
        $total = $data['total'];
        $total_importe_total = $total;
        $decimal = round((($total-((int)$total))*100),0);
        $en_letras = $number->lit($total).' CON '.$decimal.'/100 '.$config['monedas'][$data["moneda"]]["nomb"];

        /*Total valor de venta - operaciones gravadas*/
        $sac_AdditionalMonetaryTotal_1001 = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalMonetaryTotal"));
        $sac_AdditionalMonetaryTotal_1001_ID = $sac_AdditionalMonetaryTotal_1001->appendChild($xml->createElement("cbc:ID", "1001"));
        $sac_AdditionalMonetaryTotal_1001_PayableAmount = $sac_AdditionalMonetaryTotal_1001->appendChild($xml->createElement("cbc:PayableAmount", $data['total_ope_gravadas']));
        $sac_AdditionalMonetaryTotal_1001_PayableAmount->appendChild($xml->createAttribute("currencyID"))
            ->appendChild($xml->createTextNode($data['moneda']));

        /*Total valor de venta - operaciones inafectas*/
        if($data['total_ope_inafectas']>0){
            $sac_AdditionalMonetaryTotal_1002 = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalMonetaryTotal"));
            $sac_AdditionalMonetaryTotal_1002_ID = $sac_AdditionalMonetaryTotal_1002->appendChild($xml->createElement("cbc:ID", "1002"));
            $sac_AdditionalMonetaryTotal_1002_PayableAmount = $sac_AdditionalMonetaryTotal_1002->appendChild($xml->createElement("cbc:PayableAmount", $data['total_ope_inafectas']));
            $sac_AdditionalMonetaryTotal_1002_PayableAmount->appendChild($xml->createAttribute("currencyID"))
                ->appendChild($xml->createTextNode($data['moneda']));    
        }
        

        /*Total valor de venta - operaciones exoneradas*/
        if($data['total_ope_exoneradas']>0){
            $sac_AdditionalMonetaryTotal_1003 = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalMonetaryTotal"));
            $sac_AdditionalMonetaryTotal_1003_ID = $sac_AdditionalMonetaryTotal_1003->appendChild($xml->createElement("cbc:ID", "1003"));
            $sac_AdditionalMonetaryTotal_1003_PayableAmount = $sac_AdditionalMonetaryTotal_1003->appendChild($xml->createElement("cbc:PayableAmount", $data['total_ope_exoneradas']));
            $sac_AdditionalMonetaryTotal_1003_PayableAmount->appendChild($xml->createAttribute("currencyID"))
                ->appendChild($xml->createTextNode($data['moneda']));    
        }
        

        /*Total valor de venta - operaciones gratuitas*/
        $sac_AdditionalMonetaryTotal_1004 = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalMonetaryTotal"));
        $sac_AdditionalMonetaryTotal_1004_ID = $sac_AdditionalMonetaryTotal_1004->appendChild($xml->createElement("cbc:ID", "1004"));
        $sac_AdditionalMonetaryTotal_1004_PayableAmount = $sac_AdditionalMonetaryTotal_1004->appendChild($xml->createElement("cbc:PayableAmount", $data['total_ope_gratuitas']));
        $sac_AdditionalMonetaryTotal_1004_PayableAmount->appendChild($xml->createAttribute("currencyID"))
            ->appendChild($xml->createTextNode($data['moneda']));

        /*Total descuentos*/
        $sac_AdditionalMonetaryTotal_2005 = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalMonetaryTotal"));
        $sac_AdditionalMonetaryTotal_2005_ID = $sac_AdditionalMonetaryTotal_2005->appendChild($xml->createElement("cbc:ID", "2005"));
        $sac_AdditionalMonetaryTotal_2005_PayableAmount = $sac_AdditionalMonetaryTotal_2005->appendChild($xml->createElement("cbc:PayableAmount", $data['total_desc']));
        $sac_AdditionalMonetaryTotal_2005_PayableAmount->appendChild($xml->createAttribute("currencyID"))
            ->appendChild($xml->createTextNode($data['moneda']));

        /*Leyendas*/
        if($data['total_ope_gratuitas']>0){

            $sac_AdditionalProperty = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalProperty"));
            $sac_AdditionalProperty_ID = $sac_AdditionalProperty->appendChild($xml->createElement("cbc:ID", "1002"));
            $sac_AdditionalProperty_Value = $sac_AdditionalProperty->appendChild($xml->createElement("cbc:Value", "TRANSFERENCIA GRATUITA"));
        }
        /*$sac_AdditionalProperty = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalProperty"));
        $sac_AdditionalProperty_ID = $sac_AdditionalProperty->appendChild($xml->createElement("cbc:ID", "1000"));
        $sac_AdditionalProperty_Value = $sac_AdditionalProperty->appendChild($xml->createElement("cbc:Value", $en_letras));*/

        if(isset($model['placa_vehiculo'])){
            foreach($model['placa_vehiculo'] as $placa){
                $sac_SUNATCosts = $sac_AdditionalInformation->appendChild($xml->createElement("sac:SUNATCosts"));
                $cac_RoadTransport = $sac_AdditionalInformation->appendChild($xml->createElement("cac:RoadTransport"));
                $cbc_LicensePlateID = $sac_AdditionalProperty->appendChild($xml->createElement("cbc:LicensePlateID", $placa['codigo']));
            }
        }

        /*Leyendas*/
        if($data['tipo']=='F'){
            $sac_SUNATTransaction = $sac_AdditionalInformation->appendChild($xml->createElement("sac:SUNATTransaction"));
            $sac_SUNATTransaction_ID = $sac_SUNATTransaction->appendChild($xml->createElement("cbc:ID", $data['tipo_oper']));    
        }
        
        /*Versión del UBL*/
        $cbc_UBLVersionID = $Invoice->appendChild($xml->createElement("cbc:UBLVersionID", "2.0"));

        /*Versión de la estructura del documento*/
        $cbc_CustomizationID = $Invoice->appendChild($xml->createElement("cbc:CustomizationID", "1.0"));

        /*****************************
        * INFORMACION DEL COMPROBANTE
        ******************************/
                
        $cbc_ID = $Invoice->appendChild($xml->createElement("cbc:ID", $data['serie']."-".$data['numero']));

        /*Fecha de emisión*/
        $cbc_IssueDate = $Invoice->appendChild($xml->createElement("cbc:IssueDate", date('Y-m-d',$data['fecemi']->sec)));

        /*Tipo de documento (Boleta)*/
        $cbc_InvoiceTypeCode = $Invoice->appendChild($xml->createElement("cbc:InvoiceTypeCode", $config['tipos'][$data['tipo']]));//03 BOLETA | 01 FACTURA

        /*Numeración, conformada por serie y número correlativo*/
        $Invoice->appendChild($xml->createElement("cbc:DocumentCurrencyCode", $data['moneda']));// PEN: Nuevos soles | USD: Dolares estadounidanses

        /*****************************
        * DATOS DEL FIRMANTE
        ******************************/
        $cac_Signature = $Invoice->appendChild($xml->createElement("cac:Signature"));
        $cac_Signature->appendChild($xml->createElement("cbc:ID", $ci->item('conflux-ruc')));//RUC DEL FIRMANTE
        $cac_Signature->appendChild($xml->createElement("cbc:Note", $ci->item('conflux-nota_firma')));//NOTA DEL FIRMANTE
        $cac_Signature->appendChild($xml->createElement("cbc:ValidatorID", "167847"));//VALIDATOR ID
        $cac_SignatoryParty = $cac_Signature->appendChild($xml->createElement("cac:SignatoryParty"));
        $cac_PartyIdentification = $cac_SignatoryParty->appendChild($xml->createElement("cac:PartyIdentification"));
        $cac_PartyIdentification->appendChild($xml->createElement("cbc:ID", $ci->item('conflux-ruc')));//RUC DEL FIRMANTE
        $cac_PartyName = $cac_SignatoryParty->appendChild($xml->createElement("cac:PartyName"));
        $cac_PartyName->appendChild($xml->createElement("cbc:Name",$ci->item('conflux-razon_social')));
        
        $cac_AgentParty = $cac_SignatoryParty->appendChild($xml->createElement("cac:AgentParty"));
        $cac_PartyIdentification = $cac_AgentParty->appendChild($xml->createElement("cac:PartyIdentification"));
        $cac_PartyIdentification->appendChild($xml->createElement("cbc:ID", $ci->item('conflux-ruc')));

        $cac_PartyName = $cac_AgentParty->appendChild($xml->createElement("cac:PartyName"));
        $cac_PartyName->appendChild($xml->createElement("cbc:Name",$ci->item('conflux-razon_social')));

        $cac_PartyLegalEntity = $cac_AgentParty->appendChild($xml->createElement("cac:PartyLegalEntity"));
        $cac_PartyLegalEntity->appendChild($xml->createElement("cbc:RegistrationName",$ci->item('conflux-razon_social')));
        $cac_DigitalSignatureAttachment = $cac_Signature->appendChild($xml->createElement("cac:DigitalSignatureAttachment"));
        $cac_ExternalReference = $cac_DigitalSignatureAttachment->appendChild($xml->createElement("cac:ExternalReference"));
        $cac_ExternalReference->appendChild($xml->createElement("cbc:URI","SIGN"));


        /*****************************
        * DATOS DEL EMISOR
        ******************************/
        $cac_AccountingSupplierParty = $Invoice->appendChild($xml->createElement("cac:AccountingSupplierParty"));
        /*Número de RUC*/
        $cac_CustomerAssignedAccountID = $cac_AccountingSupplierParty->appendChild($xml->createElement("cbc:CustomerAssignedAccountID",$ci->item('conflux-ruc')));

        /*Tipo de documento de identidad*/
        $cac_AdditionalAccountID = $cac_AccountingSupplierParty->appendChild($xml->createElement("cbc:AdditionalAccountID","6"));

        $cac_Party = $cac_AccountingSupplierParty->appendChild($xml->createElement("cac:Party"));

        /*Nombre Comercial*/
        $cac_PartyName = $cac_Party->appendChild($xml->createElement("cac:PartyName"));
        $cbc_Name = $cac_PartyName->appendChild($xml->createElement("cbc:Name",$ci->item('conflux-razon_social')));

        //$cbc_Name->appendChild($xml->createCDATASection('SBPA')); 

        
        /*Domicilio Fiscal*/
        $cac_PostalAddress = $cac_Party->appendChild($xml->createElement("cac:PostalAddress"));
        $cac_PostalAddress->appendChild($xml->createElement("cbc:ID",$ci->item('conflux-ubigeo'))); //CODIGO LUGAR
        $cac_PostalAddress->appendChild($xml->createElement("cbc:StreetName",$ci->item('conflux-direccion')));
        //$cac_PostalAddress->appendChild($xml->createElement("cbc:CountrySubentity",$ci->item('conflux-departamento')));
        //$cac_PostalAddress->appendChild($xml->createElement("cbc:CityName",$ci->item('conflux-provincia')));
        //$cac_PostalAddress->appendChild($xml->createElement("cbc:District",$ci->item('conflux-distrito')));
        $cac_Country = $cac_PostalAddress->appendChild($xml->createElement("cac:Country"));
        $cac_Country->appendChild($xml->createElement("cbc:IdentificationCode","PE"));

        /*Apellidos y nombres, denominación o razón social*/
        $cac_PartyLegalEntity = $cac_Party->appendChild($xml->createElement("cac:PartyLegalEntity"));
        $cbc_RegistrationName = $cac_PartyLegalEntity->appendChild($xml->createElement("cbc:RegistrationName",$ci->item('conflux-razon_social')));

        /*********************************
        * INFORMACION DEL USUARIO RECEPTOR
        **********************************/
        $tip_docident = $config['tipos_doc'][$data['tipo_doc']];
        $num_docident = $data['cliente_doc'];
        
        $cac_AccountingCustomerParty = $Invoice->appendChild($xml->createElement("cac:AccountingCustomerParty"));
        /*Número de documento de identidad del adquirente o usuario*/
        $cac_AccountingCustomerParty->appendChild($xml->createElement("cbc:CustomerAssignedAccountID",$num_docident));

        /*Tipo de documento de identidad del adquirente o usuario*/
        $cac_AccountingCustomerParty->appendChild($xml->createElement("cbc:AdditionalAccountID",$tip_docident));

        $cac_Party = $cac_AccountingCustomerParty->appendChild($xml->createElement("cac:Party"));

        /*Dirección en el país del adquiriente o lugar de destino*/
        //$cac_PhysicalLocation = $cac_Party->appendChild($xml->createElement("cac:PhysicalLocation"));
        //$cbc_Description = $cac_PhysicalLocation->appendChild($xml->createElement("cbc:Description"));
        //$cbc_Description->appendChild($xml->createCDATASection($model['cliente']['domicilios'][0]['direccion']));//FILL DATA

        $cac_PartyLegalEntity = $cac_Party->appendChild($xml->createElement("cac:PartyLegalEntity"));
        
        $cliente = $data['cliente_nomb'];
        $cliente = strtoupper($cliente);
        $cbc_RegistrationName = $cac_PartyLegalEntity->appendChild($xml->createElement("cbc:RegistrationName",$cliente));
        //$cbc_RegistrationName->appendChild($xml->createCDATASection($cliente));

        $cac_SellerSupplierParty = $Invoice->appendChild($xml->createElement("cac:SellerSupplierParty"));
        $cac_Party = $cac_SellerSupplierParty->appendChild($xml->createElement("cac:Party"));
        $cac_PostalAddress = $cac_Party->appendChild($xml->createElement("cac:PostalAddress"));
        $cac_PostalAddress->appendChild($xml->createElement("cbc:AddressTypeCode"));


        if(isset($data['orden_compra'])){
            foreach($data['orden_compra'] as $orden_compra){
                $cac_OrderReference = $Invoice->appendChild($xml->createElement("cac:OrderReference"));
                $cac_OrderReference->appendChild($xml->createElement("cbc:ID", $orden_compra['numero']));
            }
        }

        if(isset($data['guia_remision'])){
            foreach($data['guia_remision'] as $guia_remision){
                $cac_DespatchDocumentReference = $Invoice->appendChild($xml->createElement("cac:DespatchDocumentReference"));
                $cac_DespatchDocumentReference->appendChild($xml->createElement("cbc:ID", $guia_remision['numero']));
                $cbc_DocumentTypeCode->appendChild($xml->createElement("cbc:DocumentTypeCode", $guia_remision['tipo']));
            }
        }
        
        /*****************************
        * TOTALES Y SUMATORIAS
        ******************************/
        
        /*Sumatoria IGV*/
        $cac_TaxTotal = $Invoice->appendChild($xml->createElement("cac:TaxTotal"));
        $cbc_TaxAmount = $cac_TaxTotal->appendChild($xml->createElement("cbc:TaxAmount",$data['total_igv']));//FILL DATA
        $cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
            ->appendChild($xml->createTextNode($data['moneda']));
        $cac_TaxSubtotal = $cac_TaxTotal->appendChild($xml->createElement("cac:TaxSubtotal"));
        $cbc_TaxAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxAmount",$data['total_igv']));//FILL DATA
        $cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
            ->appendChild($xml->createTextNode($data['moneda']));
        $cac_TaxCategory = $cac_TaxSubtotal->appendChild($xml->createElement("cac:TaxCategory"));
        $cac_TaxScheme = $cac_TaxCategory->appendChild($xml->createElement("cac:TaxScheme"));
        $cac_TaxScheme->appendChild($xml->createElement("cbc:ID", "1000"));
        $cac_TaxScheme->appendChild($xml->createElement("cbc:Name", "IGV"));
        $cac_TaxScheme->appendChild($xml->createElement("cbc:TaxTypeCode", "VAT"));

        /*Sumatoria ISC*/
        if(floatval($data['total_isc'])>0){
            $cac_TaxTotal = $Invoice->appendChild($xml->createElement("cac:TaxTotal"));
            $cbc_TaxAmount = $cac_TaxTotal->appendChild($xml->createElement("cbc:TaxAmount",$data['total_isc']));//FILL DATA
            $cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
                ->appendChild($xml->createTextNode($data['moneda']));
            $cac_TaxSubtotal = $cac_TaxTotal->appendChild($xml->createElement("cac:TaxSubtotal"));
            $cbc_TaxAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxAmount",$data['total_isc']));//FILL DATA
            $cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
                ->appendChild($xml->createTextNode($data['moneda']));
            $cac_TaxCategory = $cac_TaxSubtotal->appendChild($xml->createElement("cac:TaxCategory"));
            $cac_TaxScheme = $cac_TaxCategory->appendChild($xml->createElement("cac:TaxScheme"));
            $cac_TaxScheme->appendChild($xml->createElement("cbc:ID", "2000"));
            $cac_TaxScheme->appendChild($xml->createElement("cbc:Name", "ISC"));
            $cac_TaxScheme->appendChild($xml->createElement("cbc:TaxTypeCode", "EXC"));
        }

        $cac_LegalMonetaryTotal = $Invoice->appendChild($xml->createElement("cac:LegalMonetaryTotal"));

        $cbc_AllowanceTotalAmount = $cac_LegalMonetaryTotal->appendChild($xml->createElement("cbc:AllowanceTotalAmount",$data['descuento_global']));
        $cbc_AllowanceTotalAmount->appendChild($xml->createAttribute("currencyID"))
            ->appendChild($xml->createTextNode($data['moneda']));

        $cbc_ChargeTotalAmount = $cac_LegalMonetaryTotal->appendChild($xml->createElement("cbc:ChargeTotalAmount",$data['total_otros_cargos']));
        $cbc_ChargeTotalAmount->appendChild($xml->createAttribute("currencyID"))
            ->appendChild($xml->createTextNode($data['moneda']));

        $cbc_PayableAmount = $cac_LegalMonetaryTotal->appendChild($xml->createElement("cbc:PayableAmount",$data['total']));
        $cbc_PayableAmount->appendChild($xml->createAttribute("currencyID"))
            ->appendChild($xml->createTextNode($data['moneda']));

        if(count($data['items'])>0){
            $orden_item = 1;
            foreach($data['items'] as $row){
                $row['moneda'] = $data['moneda'];
                $this->addItemLineXml($xml, $Invoice, $orden_item, $row);
            }
        }
        //print_r($data);die();

        /************************
        * TOTALES Y SUMATORIAS
        ************************/
        $xml->formatOutput = true;
        
        $ruta_sin_firmar = "see-files/".$filename.".xml";
        $ruta_pdf = "see-files/pdf/".$filename.".pdf";
        $f->response->view('cj/ecom.print',array('data'=>$data,'ruta_pdf'=>$ruta_pdf));
        $xml->save(IndexPath.DS.$ruta_sin_firmar);
        return array(
        	'status'=>'success',
        	'success_message'=>'Se ha creado el XML y el formato PDF del modelo Invoice',
        	'success_data'=>array(
        		'ruta_xml_sin_firma'=>$ruta_sin_firmar,
	            'ruta_pdf'=>$ruta_pdf
        	)
        );
	}

	function addItemLineXml(&$xml, &$Invoice, &$orden_item, $data = array()){
        if(floatval($data['valor_unitario'])==0 && floatval($data['valor_gratuito'])==0) return false;
        $cac_InvoiceLine = $Invoice->appendChild($xml->createElement("cac:InvoiceLine"));
        /*Número de orden del Ítem*/
        $cac_InvoiceLine->appendChild($xml->createElement("cbc:ID",$orden_item));//FILL DATA

        /*Cantidad y unidad de medida por ítem*/
        $cbc_InvoicedQuantity = $cac_InvoiceLine->appendChild($xml->createElement("cbc:InvoicedQuantity", $data['cant']));//FILL DATA
        $cbc_InvoicedQuantity->appendChild($xml->createAttribute("unitCode"))
            ->appendChild($xml->createTextNode($data['cod_unidad']));//FILL DATA

        /*Monto total por linea por ítem*/
        $cbc_LineExtensionAmount = $cac_InvoiceLine->appendChild($xml->createElement("cbc:LineExtensionAmount", $data['valor_venta']));//FILL DATA
        $cbc_LineExtensionAmount->appendChild($xml->createAttribute("currencyID"))
            ->appendChild($xml->createTextNode($data['moneda']));//FILL DATA

        $cac_PricingReference = $cac_InvoiceLine->appendChild($xml->createElement("cac:PricingReference"));
        if($data['valor_gratuito']>0){
            /*Valor gratuito referencial*/
            $cac_AlternativeConditionPrice = $cac_PricingReference->appendChild($xml->createElement("cac:AlternativeConditionPrice"));
            $cbc_PriceAmount = $cac_AlternativeConditionPrice->appendChild($xml->createElement("cbc:PriceAmount",$data['valor_gratuito']));//FILL DATA
            $cbc_PriceAmount->appendChild($xml->createAttribute("currencyID"))
                ->appendChild($xml->createTextNode($data['moneda']));
            $cac_AlternativeConditionPrice->appendChild($xml->createElement("cbc:PriceTypeCode","02"));//02 Para operaciones gratuidtas
        }
        /*Precio de venta unitario por ítem y código (incluido el IGV, ISC y otros tributos). */
        $cac_AlternativeConditionPrice = $cac_PricingReference->appendChild($xml->createElement("cac:AlternativeConditionPrice"));
        $cbc_PriceAmount = $cac_AlternativeConditionPrice->appendChild($xml->createElement("cbc:PriceAmount",$data['precio_venta_unitario']));//FILL DATA
        $cbc_PriceAmount->appendChild($xml->createAttribute("currencyID"))
            ->appendChild($xml->createTextNode($data['moneda']));
        $cac_AlternativeConditionPrice->appendChild($xml->createElement("cbc:PriceTypeCode","01"));//02 Para operaciones gratuidtas

        /*Descuentos por item*/
        if(floatval($data['descuento'])>0){
            $cac_AllowanceCharge = $cac_InvoiceLine->appendChild($xml->createElement("cac:AllowanceCharge"));
            $cac_AllowanceCharge->appendChild($xml->createElement("cbc:ChargeIndicator","false"));
            $cbc_LineExtensionAmount = $cac_AllowanceCharge->appendChild($xml->createElement("cbc:Amount", $data['descuento']));//FILL DATA
            $cbc_LineExtensionAmount->appendChild($xml->createAttribute("currencyID"))
                ->appendChild($xml->createTextNode($data['moneda']));//FILL DATA
        }

        /*Impuestos por item (ISC)*/
        if(floatval($data['isc'])>0){
            $cac_TaxTotal = $cac_InvoiceLine->appendChild($xml->createElement("cac:TaxTotal"));
            $cbc_TaxAmount = $cac_TaxTotal->appendChild($xml->createElement("cbc:TaxAmount",$data['isc']));
            $cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
                ->appendChild($xml->createTextNode($data['moneda']));
            $cac_TaxSubtotal = $cac_TaxTotal->appendChild($xml->createElement("cac:TaxSubtotal"));
            $cbc_TaxAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxAmount",$data['isc']));
            $cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
                ->appendChild($xml->createTextNode($data['moneda']));
            $cac_TaxCategory = $cac_TaxSubtotal->appendChild($xml->createElement("cac:TaxCategory"));
            $cac_TaxCategory->appendChild($xml->createElement("cbc:TierRange","01"));
            $cac_TaxScheme = $cac_TaxCategory->appendChild($xml->createElement("cac:TaxScheme"));
            $cac_TaxScheme->appendChild($xml->createElement("cbc:ID", "2000"));
            $cac_TaxScheme->appendChild($xml->createElement("cbc:Name", "ISC"));
            $cac_TaxScheme->appendChild($xml->createElement("cbc:TaxTypeCode", "EXC"));
        }

        /*Impuestos por item (IGV)*/
        $cac_TaxTotal = $cac_InvoiceLine->appendChild($xml->createElement("cac:TaxTotal"));
        $cbc_TaxAmount = $cac_TaxTotal->appendChild($xml->createElement("cbc:TaxAmount",$data['igv']));//FILL DATA
        $cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
            ->appendChild($xml->createTextNode($data['moneda']));
        $cac_TaxSubtotal = $cac_TaxTotal->appendChild($xml->createElement("cac:TaxSubtotal"));
        $cbc_TaxableAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxableAmount",$data['igv']));//FILL DATA
        $cbc_TaxableAmount->appendChild($xml->createAttribute("currencyID"))
            ->appendChild($xml->createTextNode($data['moneda']));
        $cbc_TaxAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxAmount",$data['igv']));//FILL DATA
        $cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
            ->appendChild($xml->createTextNode($data['moneda']));
        $cac_TaxCategory = $cac_TaxSubtotal->appendChild($xml->createElement("cac:TaxCategory"));
        $cac_TaxCategory->appendChild($xml->createElement("cbc:TaxExemptionReasonCode",$data['tipo_igv']));
        
        $cac_TaxScheme = $cac_TaxCategory->appendChild($xml->createElement("cac:TaxScheme"));
        $cac_TaxScheme->appendChild($xml->createElement("cbc:ID", "1000"));
        $cac_TaxScheme->appendChild($xml->createElement("cbc:Name", "IGV"));
        $cac_TaxScheme->appendChild($xml->createElement("cbc:TaxTypeCode", "VAT"));

        /*Descripcion detallada por item*/
        $cac_Item = $cac_InvoiceLine->appendChild($xml->createElement("cac:Item"));
        $cbc_Description = $cac_Item->appendChild($xml->createElement("cbc:Description",$data['descr']));
        //$cbc_Description->appendChild($xml->createCDATASection($data['descripcion']));//FILL DATA
        $cac_SellersItemIdentification = $cac_Item->appendChild($xml->createElement("cac:SellersItemIdentification"));
        $cac_SellersItemIdentification->appendChild($xml->createElement("cbc:ID",$data['codigo']));//FILL DATA
        $cac_AdditionalItemIdentification = $cac_Item->appendChild($xml->createElement("cac:AdditionalItemIdentification"));
        $cac_AdditionalItemIdentification->appendChild($xml->createElement("cbc:ID",""));//FILL DATA

        /*Valor unitario por ítem (No incluye IGV, ISC y otros Tributos ni cargos globales)*/
        $cac_Price = $cac_InvoiceLine->appendChild($xml->createElement("cac:Price"));
        $cbc_PriceAmount = $cac_Price->appendChild($xml->createElement("cbc:PriceAmount",$data['valor_unitario']));
        $cbc_PriceAmount->appendChild($xml->createAttribute("currencyID"))
            ->appendChild($xml->createTextNode($data['moneda']));
        $orden_item++;
        return true;
    }
}