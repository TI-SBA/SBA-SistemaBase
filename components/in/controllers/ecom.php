<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include IndexPath.DS."libraries/xmlseclibs/autoload.php";
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use RobRichards\XMLSecLibs\XMLSecEnc;
class Controller_in_ecom extends Controller {
	function execute_lista(){
		global $f;
		$params = array(
			"modulo"=>'IN'
		);
		if(isset($f->request->data['page']))
			if($f->request->data['page']!='')
				$params['page'] = $f->request->data['page'];
		if(isset($f->request->data['page_rows']))
			if($f->request->data['page_rows']!='')
				$params['page_rows'] = $f->request->data['page_rows'];
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['tipo']))
			if($f->request->data['tipo']!='')
				$params['tipo'] = $f->request->data['tipo'];
		if(isset($f->request->data['estado']))
			if($f->request->data['estado']!='')
				$params['estado'] = $f->request->data['estado'];
		if(isset($f->request->data['cliente']))
			if($f->request->data['cliente']!='')
				$params['cliente'] = new MongoId($f->request->data['cliente']);
		if(isset($f->request->data['alquileres']))
			$params['alquileres'] = true;
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("cj/comp")->params($params)->get("search") );
	}
	function execute_print(){
		global $f;
		$data = $f->request->data;
		$model = $f->model('cj/comp')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;
		$f->response->view('in/ecom.print.pdf.php',array('data'=>$model));
	}
	function execute_generate_xml(){
		global $f;
		$data = $f->request->data;
		include IndexPath.DS."components/in/controllers/impo.php";
		$test = new Controller_in_impo();
		$model = $test->generar_comprobante(array('F','FF11','3','S',1));//CASO 3
		echo json_encode($model);die();
		//print_r($model);die();
		//$model = $f->model('cj/comp')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;
		$xml = new DOMDocument("1.0", "ISO-8859-1");
		$file = null;
		switch($model['tipo']){
			case "F":
				$file = $this->generateInvoice($xml, $model);
				break;
			case "B":
				$file = $this->generateInvoice($xml, $model);
				break;
			case "NC":
				$file = $this->generateCreditNote($xml, $model);
				break;
			case "ND":
				$file = $this->generateDebitNote($xml, $model);
				break;
		}
		//echo $xml->saveXML();

		/************************
		* FIRMA DIGITAL
		************************/
		$ruta_sin_firmar = $file['ruta_sin_firmar'];
		$ruta_con_firma = $file['ruta_con_firma'];
		$ruta_comprimido = $file['ruta_comprimido'];
		if($file!=null){
			$doc = new DOMDocument();
			$doc->load($ruta_sin_firmar);
			$objDSig = new XMLSecurityDSig();
			$objDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
			$objDSig->addReference(
				$doc,
				XMLSecurityDSig::SHA1,
				array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'),
				array('force_uri' => true)
			);

			$objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type'=>'private'));
			$certificado = file_get_contents(IndexPath.DS.'signkey.pfx');
			$certPassword = 'beneficenciamayas2012';
			openssl_pkcs12_read($certificado, $certs, $certPassword);
			$objKey->loadKey($certs['pkey'], false, false);
			$nodo_firma = $doc->getElementsByTagName("ExtensionContent");
			$objDSig->sign($objKey);
			$objDSig->add509Cert($certs['cert']);
			$objDSig->appendSignature($nodo_firma->item(1));
			$SignatureSP = $doc->getElementsByTagName("Signature")->item(0);
			$SignatureSP->appendChild($doc->createAttribute("Id"))
				->appendChild($doc->createTextNode('SignSBPA'));
			$Reference = $doc->getElementsByTagName("Reference")->item(0);
			$Reference->appendChild($doc->createAttribute("URI"))
				->appendChild($doc->createTextNode(''));
			$doc->save($ruta_con_firma);
			$ziping = $this->create_zip(array($ruta_con_firma), $ruta_comprimido, false);
			if(!$ziping){
				echo "NO SE HA GENERADO EL ARCHIVO ZIP";
			}else{
				//$file['ruta_xml_sin_firma'] = $ruta_sin_firmar;
				//$file['ruta_zip_con_firma'] = $ruta_comprimido;
				//$f->model('cj/comp')->params(array('_id'=>new MongoId($data['_id']), "data"=>array('see_sbpa'=>$see_sbpa)))->save('update');
				echo "ARCHIVO ZIP GENERADO CORRECTAMENTE";

				$this->execute_send_sunat($file);
			}
		}else{
			echo 'EL TIPO DE DOCUMENTO DETECTADO NO ESTA HABILITADO PARA LA EMISION DE COMPROBANTES ELECTRONICOS';
		}
	}
	function generateInvoice(&$xml, $data = null){
		//create the xml document
		$tipos = array(
			"F"=>"01",
			"B"=>"03",
		);
		$monedas = array(
			"S"=>array("nomb"=>"SOLES","simb"=>"S/.","cod"=>"PEN"),
			"D"=>array("nomb"=>"DOLARES AMERICANOS","simb"=>"US$","cod"=>"USD")
		);
		//create the root element
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

		/*$Invoice->appendChild($xml->createAttribute("xmlns:xsi"))
			->appendChild($xml->createTextNode('http://www.w3.org/2001/XMLSchema-instance'));*/


		$ext_UBLExtensions = $Invoice->appendChild($xml->createElement("ext:UBLExtensions"));
		$ext_UBLExtension = $ext_UBLExtensions->appendChild($xml->createElement("ext:UBLExtension"));
		$ext_ExtensionContent = $ext_UBLExtension->appendChild($xml->createElement("ext:ExtensionContent"));
		$sac_AdditionalInformation = $ext_ExtensionContent->appendChild($xml->createElement("sac:AdditionalInformation"));

		$ext_UBLExtension = $ext_UBLExtensions->appendChild($xml->createElement("ext:UBLExtension"));
		$ext_ExtensionContent = $ext_UBLExtension->appendChild($xml->createElement("ext:ExtensionContent"));

		/*****************************
		* DETALLE DEL DOCUMENTO
		******************************/
		$total = $data['total'];
		$total_importe_total = $total;
		$decimal = round((($total-((int)$total))*100),0);
		$en_letras = Number::lit($total).' CON '.$decimal.'/100 '.$monedas[$data["moneda"]]["nomb"];


		/*Total descuentos*/
		$sac_AdditionalMonetaryTotal_2005 = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalMonetaryTotal"));
		$sac_AdditionalMonetaryTotal_2005_ID = $sac_AdditionalMonetaryTotal_2005->appendChild($xml->createElement("cbc:ID", "2005"));
		$sac_AdditionalMonetaryTotal_2005_PayableAmount = $sac_AdditionalMonetaryTotal_2005->appendChild($xml->createElement("cbc:PayableAmount", $data['total_desc']));
		$sac_AdditionalMonetaryTotal_2005_PayableAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));

		/*Total valor de venta - operaciones gravadas*/
		$sac_AdditionalMonetaryTotal_1001 = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalMonetaryTotal"));
		$sac_AdditionalMonetaryTotal_1001_ID = $sac_AdditionalMonetaryTotal_1001->appendChild($xml->createElement("cbc:ID", "1001"));
		$sac_AdditionalMonetaryTotal_1001_PayableAmount = $sac_AdditionalMonetaryTotal_1001->appendChild($xml->createElement("cbc:PayableAmount", $data['total_ope_gravadas']));
		$sac_AdditionalMonetaryTotal_1001_PayableAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));

		/*Total valor de venta - operaciones inafectas*/
		$sac_AdditionalMonetaryTotal_1002 = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalMonetaryTotal"));
		$sac_AdditionalMonetaryTotal_1002_ID = $sac_AdditionalMonetaryTotal_1002->appendChild($xml->createElement("cbc:ID", "1002"));
		$sac_AdditionalMonetaryTotal_1002_PayableAmount = $sac_AdditionalMonetaryTotal_1002->appendChild($xml->createElement("cbc:PayableAmount", $data['total_ope_inafectas']));
		$sac_AdditionalMonetaryTotal_1002_PayableAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));

		/*Total valor de venta - operaciones exoneradas*/
		$sac_AdditionalMonetaryTotal_1003 = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalMonetaryTotal"));
		$sac_AdditionalMonetaryTotal_1003_ID = $sac_AdditionalMonetaryTotal_1003->appendChild($xml->createElement("cbc:ID", "1003"));
		$sac_AdditionalMonetaryTotal_1003_PayableAmount = $sac_AdditionalMonetaryTotal_1003->appendChild($xml->createElement("cbc:PayableAmount", $data['total_ope_exoneradas']));
		$sac_AdditionalMonetaryTotal_1003_PayableAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));

		/*Total valor de venta - operaciones gratuitas*/
		/*$sac_AdditionalMonetaryTotal_1004 = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalMonetaryTotal"));
		$sac_AdditionalMonetaryTotal_1004_ID = $sac_AdditionalMonetaryTotal_1004->appendChild($xml->createElement("cbc:ID", "1004"));
		$sac_AdditionalMonetaryTotal_1004_PayableAmount = $sac_AdditionalMonetaryTotal_1004->appendChild($xml->createElement("cbc:PayableAmount", $total_op_gratuitas));
		$sac_AdditionalMonetaryTotal_1004_PayableAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));*/

		

		/*Leyendas*/
		/*$sac_AdditionalProperty = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalProperty"));
		$sac_AdditionalProperty_ID = $sac_AdditionalProperty->appendChild($xml->createElement("cbc:ID", "1000"));
		$sac_AdditionalProperty_Value = $sac_AdditionalProperty->appendChild($xml->createElement("cbc:Value", $en_letras));*/

		/*Leyendas*/
		$sac_SUNATTransaction = $sac_AdditionalInformation->appendChild($xml->createElement("sac:SUNATTransaction"));
		$sac_SUNATTransaction_ID = $sac_SUNATTransaction->appendChild($xml->createElement("cbc:ID", "01"));
		
		/*Versión del UBL*/
		$cbc_UBLVersionID = $Invoice->appendChild($xml->createElement("cbc:UBLVersionID", "2.0"));

		/*Versión de la estructura del documento*/
		$cbc_CustomizationID = $Invoice->appendChild($xml->createElement("cbc:CustomizationID", "1.0"));

		/*****************************
		* INFORMACION DEL COMPROBANTE
		******************************/
				
		$cbc_ID = $Invoice->appendChild($xml->createElement("cbc:ID", $data['serie']."-".$data['num']));

		/*Fecha de emisión*/
		$cbc_IssueDate = $Invoice->appendChild($xml->createElement("cbc:IssueDate", date("Y-m-d", $data['fecreg'])));

		/*Tipo de documento (Boleta)*/
		$cbc_InvoiceTypeCode = $Invoice->appendChild($xml->createElement("cbc:InvoiceTypeCode", $tipos[$data['tipo']]));//03 BOLETA | 01 FACTURA

		/*Numeración, conformada por serie y número correlativo*/
		$Invoice->appendChild($xml->createElement("cbc:DocumentCurrencyCode", "PEN"));// PEN: Nuevos soles | USD: Dolares estadounidanses

		/*****************************
		* DATOS DEL FIRMANTE
		******************************/
		$cac_Signature = $Invoice->appendChild($xml->createElement("cac:Signature"));
		$cac_Signature->appendChild($xml->createElement("cbc:ID", "20120958136"));//RUC DEL FIRMANTE
		$cac_Signature->appendChild($xml->createElement("cbc:Note", "Elaborado por Sistema de Emision Electronica Facturador SBPA (SEE-SBPA) 1.0.0"));//NOTA DEL FIRMANTE
		$cac_Signature->appendChild($xml->createElement("cbc:ValidatorID", "167847"));//VALIDATOR ID
		$cac_SignatoryParty = $cac_Signature->appendChild($xml->createElement("cac:SignatoryParty"));
		$cac_PartyIdentification = $cac_SignatoryParty->appendChild($xml->createElement("cac:PartyIdentification"));
		$cac_PartyIdentification->appendChild($xml->createElement("cbc:ID", "20120958136"));//RUC DEL FIRMANTE
		$cac_PartyName = $cac_SignatoryParty->appendChild($xml->createElement("cac:PartyName"));
		$cac_PartyName->appendChild($xml->createElement("cbc:Name","SOCIEDAD BENEFICENCIA PUBLICA DE AREQUIPA"));
		
		$cac_AgentParty = $cac_SignatoryParty->appendChild($xml->createElement("cac:AgentParty"));
		$cac_PartyIdentification = $cac_AgentParty->appendChild($xml->createElement("cac:PartyIdentification"));
		$cac_PartyIdentification->appendChild($xml->createElement("cbc:ID", "20120958136"));

		$cac_PartyName = $cac_AgentParty->appendChild($xml->createElement("cac:PartyName"));
		$cac_PartyName->appendChild($xml->createElement("cbc:Name","SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA"));

		$cac_PartyLegalEntity = $cac_AgentParty->appendChild($xml->createElement("cac:PartyLegalEntity"));
		$cac_PartyLegalEntity->appendChild($xml->createElement("cbc:RegistrationName","SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA"));
		$cac_DigitalSignatureAttachment = $cac_Signature->appendChild($xml->createElement("cac:DigitalSignatureAttachment"));
		$cac_ExternalReference = $cac_DigitalSignatureAttachment->appendChild($xml->createElement("cac:ExternalReference"));
		$cac_ExternalReference->appendChild($xml->createElement("cbc:URI","SIGN"));


		/*****************************
		* DATOS DEL EMISOR
		******************************/
		$cac_AccountingSupplierParty = $Invoice->appendChild($xml->createElement("cac:AccountingSupplierParty"));
		/*Número de RUC*/
		$cac_CustomerAssignedAccountID = $cac_AccountingSupplierParty->appendChild($xml->createElement("cbc:CustomerAssignedAccountID","20120958136"));

		/*Tipo de documento de identidad*/
		$cac_AdditionalAccountID = $cac_AccountingSupplierParty->appendChild($xml->createElement("cbc:AdditionalAccountID","6"));

		$cac_Party = $cac_AccountingSupplierParty->appendChild($xml->createElement("cac:Party"));

		/*Nombre Comercial*/
		$cac_PartyName = $cac_Party->appendChild($xml->createElement("cac:PartyName"));
		$cbc_Name = $cac_PartyName->appendChild($xml->createElement("cbc:Name","SOCIEDAD BENEFICENCIA PUBLICA DE AREQUIPA"));

		//$cbc_Name->appendChild($xml->createCDATASection('SBPA')); 

		
		/*Domicilio Fiscal*/
		$cac_PostalAddress = $cac_Party->appendChild($xml->createElement("cac:PostalAddress"));
		$cac_PostalAddress->appendChild($xml->createElement("cbc:ID","040101")); //CODIGO LUGAR
		$cac_PostalAddress->appendChild($xml->createElement("cbc:StreetName","CALLE PIEROLA 201"));
		$cac_Country = $cac_PostalAddress->appendChild($xml->createElement("cac:Country"));
		$cac_Country->appendChild($xml->createElement("cbc:IdentificationCode","PE"));

		/*Apellidos y nombres, denominación o razón social*/
		$cac_PartyLegalEntity = $cac_Party->appendChild($xml->createElement("cac:PartyLegalEntity"));
		$cbc_RegistrationName = $cac_PartyLegalEntity->appendChild($xml->createElement("cbc:RegistrationName","SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA"));

		/*********************************
		* INFORMACION DEL USUARIO RECEPTOR
		**********************************/
		$tip_docident = $data['tipo_doc'];
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
		
		/*****************************
		* TOTALES Y SUMATORIAS
		******************************/
		
		/*Sumatoria IGV*/
		$cac_TaxTotal = $Invoice->appendChild($xml->createElement("cac:TaxTotal"));
		$cbc_TaxAmount = $cac_TaxTotal->appendChild($xml->createElement("cbc:TaxAmount",$data['total_igv']));//FILL DATA
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxSubtotal = $cac_TaxTotal->appendChild($xml->createElement("cac:TaxSubtotal"));
		$cbc_TaxAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxAmount",$data['total_igv']));//FILL DATA
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxCategory = $cac_TaxSubtotal->appendChild($xml->createElement("cac:TaxCategory"));
		$cac_TaxScheme = $cac_TaxCategory->appendChild($xml->createElement("cac:TaxScheme"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:ID", "1000"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:Name", "IGV"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:TaxTypeCode", "VAT"));

		/*Sumatoria ISC*/
		/*$cac_TaxTotal = $Invoice->appendChild($xml->createElement("cac:TaxTotal"));
		$cbc_TaxAmount = $cac_TaxTotal->appendChild($xml->createElement("cbc:TaxAmount",$total_isc));//FILL DATA
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxSubtotal = $cac_TaxTotal->appendChild($xml->createElement("cac:TaxSubtotal"));
		$cbc_TaxAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxAmount",$total_isc));//FILL DATA
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxCategory = $cac_TaxSubtotal->appendChild($xml->createElement("cac:TaxCategory"));
		$cac_TaxScheme = $cac_TaxCategory->appendChild($xml->createElement("cac:TaxScheme"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:ID", "2000"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:Name", "ISC"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:TaxTypeCode", "EXC"));*/

		$cac_LegalMonetaryTotal = $Invoice->appendChild($xml->createElement("cac:LegalMonetaryTotal"));

		$cbc_AllowanceTotalAmount = $cac_LegalMonetaryTotal->appendChild($xml->createElement("cbc:AllowanceTotalAmount","0.00"));
		$cbc_AllowanceTotalAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));

		$cbc_ChargeTotalAmount = $cac_LegalMonetaryTotal->appendChild($xml->createElement("cbc:ChargeTotalAmount","0.00"));
		$cbc_ChargeTotalAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));

		$cbc_PayableAmount = $cac_LegalMonetaryTotal->appendChild($xml->createElement("cbc:PayableAmount",$data['total']));
		$cbc_PayableAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));

		if(count($data['items'])>0){
			$orden_item = 1;
			foreach($data['items'] as $row){
				$_row = array(
					"codigo"=>$row['codigo'],
					"unidad"=>$row['cod_unidad'],
					"cantidad"=>$row['cant'],
					"valor_unitario"=>$row['valor_unitario'],
					"igv"=>$row['igv'],
					"isc"=>$row['isc'],
					"precio_venta_unitario"=>$row['precio_unitario'],
					"valor_unitario_total"=>$row['valor_unitario']*$row['cant'],
					"importe_total"=>$row['importe_total'],
					"ope_inafectas"=>$row['ope_inafectas'],
					"ope_gravadas"=>$row['ope_gravadas'],
					"descuento"=>$row['desc'],
					"descripcion"=>$row['descr']
				);
				$this->addItemLineXml($xml, $Invoice, $orden_item, $_row);
			}
		}

		/************************
		* TOTALES Y SUMATORIAS
		************************/
		$xml->formatOutput = true;
		$ruta_sin_firmar = IndexPath.DS."see-files/20120958136-".$tipos[$data['tipo']]."-".$data['serie']."-".$data['num'].".xml";
		$ruta_con_firma = IndexPath.DS."see-files/firma/20120958136-".$tipos[$data['tipo']]."-".$data['serie']."-".$data['num'].".xml";
		$ruta_comprimido = IndexPath.DS."see-files/firma/20120958136-".$tipos[$data['tipo']]."-".$data['serie']."-".$data['num'].".zip";
		$xml->save($ruta_sin_firmar);
		return array(
			'ruta_sin_firmar'=>$ruta_sin_firmar,
			'ruta_con_firma'=>$ruta_con_firma,
			'ruta_comprimido'=>$ruta_comprimido,
			'ruta_zip_con_firma'=>$ruta_comprimido
		);
	}
	function addItemLineXml(&$xml, &$Invoice, &$orden_item, $data = null){
		if(floatval($data['importe_total'])==0) return false;
		$cac_InvoiceLine = $Invoice->appendChild($xml->createElement("cac:InvoiceLine"));
		/*Número de orden del Ítem*/
		$cac_InvoiceLine->appendChild($xml->createElement("cbc:ID",$orden_item));//FILL DATA

		/*Cantidad y unidad de medida por ítem*/
		$cbc_InvoicedQuantity = $cac_InvoiceLine->appendChild($xml->createElement("cbc:InvoicedQuantity", $data['cantidad']));//FILL DATA
		$cbc_InvoicedQuantity->appendChild($xml->createAttribute("unitCode"))
			->appendChild($xml->createTextNode($data['unidad']));//FILL DATA

		/*Monto total por linea por ítem*/
		$cbc_LineExtensionAmount = $cac_InvoiceLine->appendChild($xml->createElement("cbc:LineExtensionAmount", $data['importe_total']));//FILL DATA
		$cbc_LineExtensionAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode("PEN"));//FILL DATA

		/*Precio de venta unitario por ítem y código (incluido el IGV). */
		$cac_PricingReference = $cac_InvoiceLine->appendChild($xml->createElement("cac:PricingReference"));
		$cac_AlternativeConditionPrice = $cac_PricingReference->appendChild($xml->createElement("cac:AlternativeConditionPrice"));
		$cbc_PriceAmount = $cac_AlternativeConditionPrice->appendChild($xml->createElement("cbc:PriceAmount",$data['precio_venta_unitario']));//FILL DATA
		$cbc_PriceAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_AlternativeConditionPrice->appendChild($xml->createElement("cbc:PriceTypeCode","01"));//02 Para operaciones gratuidtas

		$cac_AllowanceCharge = $cac_InvoiceLine->appendChild($xml->createElement("cac:AllowanceCharge"));
		$cac_AllowanceCharge->appendChild($xml->createElement("cbc:ChargeIndicator","false"));
		$cbc_LineExtensionAmount = $cac_AllowanceCharge->appendChild($xml->createElement("cbc:Amount", "0.00"));//FILL DATA
		$cbc_LineExtensionAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode("PEN"));//FILL DATA

		/*Impuestos por item (ISC)*/
		/*$cac_TaxTotal = $cac_InvoiceLine->appendChild($xml->createElement("cac:TaxTotal"));
		$cbc_TaxAmount = $cac_TaxTotal->appendChild($xml->createElement("cbc:TaxAmount","77.58"));
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxSubtotal = $cac_TaxTotal->appendChild($xml->createElement("cac:TaxSubtotal"));
		$cbc_TaxAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxAmount","77.58"));
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxCategory = $cac_TaxSubtotal->appendChild($xml->createElement("cac:TaxCategory"));
		$cac_TaxCategory->appendChild($xml->createElement("cbc:TierRange","01"));
		$cac_TaxScheme = $cac_TaxCategory->appendChild($xml->createElement("cac:TaxScheme"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:ID", "2000"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:Name", "ISC"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:TaxTypeCode", "EXC"));*/

		/*Impuestos por item (IGV)*/
		$cac_TaxTotal = $cac_InvoiceLine->appendChild($xml->createElement("cac:TaxTotal"));
		$cbc_TaxAmount = $cac_TaxTotal->appendChild($xml->createElement("cbc:TaxAmount",$data['igv']));//FILL DATA
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxSubtotal = $cac_TaxTotal->appendChild($xml->createElement("cac:TaxSubtotal"));
		$cbc_TaxableAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxableAmount",$data['igv']));//FILL DATA
		$cbc_TaxableAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cbc_TaxAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxAmount",$data['igv']));//FILL DATA
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxCategory = $cac_TaxSubtotal->appendChild($xml->createElement("cac:TaxCategory"));
		$cac_TaxCategory->appendChild($xml->createElement("cbc:TaxExemptionReasonCode","10"));
		$cac_TaxScheme = $cac_TaxCategory->appendChild($xml->createElement("cac:TaxScheme"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:ID", "1000"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:Name", "IGV"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:TaxTypeCode", "VAT"));

		/*Descripcion detallada por item*/
		$cac_Item = $cac_InvoiceLine->appendChild($xml->createElement("cac:Item"));
		$cbc_Description = $cac_Item->appendChild($xml->createElement("cbc:Description",$data['descripcion']));
		//$cbc_Description->appendChild($xml->createCDATASection($data['descripcion']));//FILL DATA
		$cac_SellersItemIdentification = $cac_Item->appendChild($xml->createElement("cac:SellersItemIdentification"));
		$cac_SellersItemIdentification->appendChild($xml->createElement("cbc:ID",$data['codigo']));//FILL DATA
		$cac_AdditionalItemIdentification = $cac_Item->appendChild($xml->createElement("cac:AdditionalItemIdentification"));
		$cac_AdditionalItemIdentification->appendChild($xml->createElement("cbc:ID",""));//FILL DATA

		/*Valor unitario por ítem (No incluye IGV, ISC y otros Tributos ni cargos globales)*/
		$cac_Price = $cac_InvoiceLine->appendChild($xml->createElement("cac:Price"));
		$cbc_PriceAmount = $cac_Price->appendChild($xml->createElement("cbc:PriceAmount",$data['valor_unitario']));
		$cbc_PriceAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$orden_item++;
		return true;
	}

	function generateCreditNote(&$xml, $data = null){
		//create the xml document
		$tipos = array(
			"NC"=>"07"
		);
		$monedas = array(
			"S"=>array("nomb"=>"SOLES","simb"=>"S/.","cod"=>"PEN"),
			"D"=>array("nomb"=>"DOLARES AMERICANOS","simb"=>"US$","cod"=>"USD")
		);
		//create the root element
		$CreditNote = $xml->appendChild($xml->createElement("Invoice"));
		$CreditNote->appendChild($xml->createAttribute("xmlns"))
			->appendChild($xml->createTextNode('urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2'));

		$CreditNote->appendChild($xml->createAttribute("xmlns:cac"))
			->appendChild($xml->createTextNode('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2'));

		$CreditNote->appendChild($xml->createAttribute("xmlns:cbc"))
			->appendChild($xml->createTextNode('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2'));

		$CreditNote->appendChild($xml->createAttribute("xmlns:ccts"))
			->appendChild($xml->createTextNode('urn:un:unece:uncefact:documentation:2'));

		$CreditNote->appendChild($xml->createAttribute("xmlns:ds"))
			->appendChild($xml->createTextNode('http://www.w3.org/2000/09/xmldsig#'));

		$CreditNote->appendChild($xml->createAttribute("xmlns:ext"))
			->appendChild($xml->createTextNode('urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2'));

		$CreditNote->appendChild($xml->createAttribute("xmlns:qdt"))
			->appendChild($xml->createTextNode('urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2'));

		$CreditNote->appendChild($xml->createAttribute("xmlns:sac"))
			->appendChild($xml->createTextNode('urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1'));

		$CreditNote->appendChild($xml->createAttribute("xmlns:udt"))
			->appendChild($xml->createTextNode('urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2'));

		/*$CreditNote->appendChild($xml->createAttribute("xmlns:xsi"))
			->appendChild($xml->createTextNode('http://www.w3.org/2001/XMLSchema-instance'));*/


		$ext_UBLExtensions = $CreditNote->appendChild($xml->createElement("ext:UBLExtensions"));
		$ext_UBLExtension = $ext_UBLExtensions->appendChild($xml->createElement("ext:UBLExtension"));
		$ext_ExtensionContent = $ext_UBLExtension->appendChild($xml->createElement("ext:ExtensionContent"));
		$sac_AdditionalInformation = $ext_ExtensionContent->appendChild($xml->createElement("sac:AdditionalInformation"));

		$ext_UBLExtension = $ext_UBLExtensions->appendChild($xml->createElement("ext:UBLExtension"));
		$ext_ExtensionContent = $ext_UBLExtension->appendChild($xml->createElement("ext:ExtensionContent"));

		/*****************************
		* DETALLE DEL DOCUMENTO
		******************************/
		$total = $model['total'];
		$total_importe_total = $total;
		$decimal = round((($total-((int)$total))*100),0);
		$en_letras = Number::lit($total).' CON '.$decimal.'/100 '.$monedas[$model["moneda"]]["nomb"];

		/*Total valor de venta - operaciones gravadas*/
		$sac_AdditionalMonetaryTotal_1001 = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalMonetaryTotal"));
		$sac_AdditionalMonetaryTotal_1001_ID = $sac_AdditionalMonetaryTotal_1001->appendChild($xml->createElement("cbc:ID", "1001"));
		$sac_AdditionalMonetaryTotal_1001_PayableAmount = $sac_AdditionalMonetaryTotal_1001->appendChild($xml->createElement("cbc:PayableAmount", $total_op_gravada));
		$sac_AdditionalMonetaryTotal_1001_PayableAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));

		/*Total valor de venta - operaciones inafectas*/
		$sac_AdditionalMonetaryTotal_1002 = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalMonetaryTotal"));
		$sac_AdditionalMonetaryTotal_1002_ID = $sac_AdditionalMonetaryTotal_1002->appendChild($xml->createElement("cbc:ID", "1002"));
		$sac_AdditionalMonetaryTotal_1002_PayableAmount = $sac_AdditionalMonetaryTotal_1002->appendChild($xml->createElement("cbc:PayableAmount", $total_op_inafecta));
		$sac_AdditionalMonetaryTotal_1002_PayableAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));

		/*Total valor de venta - operaciones exoneradas*/
		$sac_AdditionalMonetaryTotal_1003 = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalMonetaryTotal"));
		$sac_AdditionalMonetaryTotal_1003_ID = $sac_AdditionalMonetaryTotal_1003->appendChild($xml->createElement("cbc:ID", "1003"));
		$sac_AdditionalMonetaryTotal_1003_PayableAmount = $sac_AdditionalMonetaryTotal_1003->appendChild($xml->createElement("cbc:PayableAmount", "0.00"));
		$sac_AdditionalMonetaryTotal_1003_PayableAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));

		/*Total valor de venta - operaciones gratuitas*/
		/*$sac_AdditionalMonetaryTotal_1004 = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalMonetaryTotal"));
		$sac_AdditionalMonetaryTotal_1004_ID = $sac_AdditionalMonetaryTotal_1004->appendChild($xml->createElement("cbc:ID", "1004"));
		$sac_AdditionalMonetaryTotal_1004_PayableAmount = $sac_AdditionalMonetaryTotal_1004->appendChild($xml->createElement("cbc:PayableAmount", $total_op_gratuitas));
		$sac_AdditionalMonetaryTotal_1004_PayableAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));*/

		

		/*Leyendas*/
		/*$sac_AdditionalProperty = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalProperty"));
		$sac_AdditionalProperty_ID = $sac_AdditionalProperty->appendChild($xml->createElement("cbc:ID", "1000"));
		$sac_AdditionalProperty_Value = $sac_AdditionalProperty->appendChild($xml->createElement("cbc:Value", $en_letras));*/

		/*Leyendas*/
		//$sac_SUNATTransaction = $sac_AdditionalInformation->appendChild($xml->createElement("sac:SUNATTransaction"));
		//$sac_SUNATTransaction_ID = $sac_SUNATTransaction->appendChild($xml->createElement("cbc:ID", "01"));
		
		/*Versión del UBL*/
		$cbc_UBLVersionID = $CreditNote->appendChild($xml->createElement("cbc:UBLVersionID", "2.0"));

		/*Versión de la estructura del documento*/
		$cbc_CustomizationID = $CreditNote->appendChild($xml->createElement("cbc:CustomizationID", "1.0"));

		/*****************************
		* INFORMACION DEL COMPROBANTE
		******************************/
		$cbc_ID = $CreditNote->appendChild($xml->createElement("cbc:ID", $data['serie']."-".$data['num']));

		/*Fecha de emisión*/
		$cbc_IssueDate = $CreditNote->appendChild($xml->createElement("cbc:IssueDate", date("Y-m-d", $data['fecreg'])));

		/*Tipo de documento (Boleta)*/
		$cbc_InvoiceTypeCode = $CreditNote->appendChild($xml->createElement("cbc:InvoiceTypeCode", $tipos[$data['tipo']]));//03 BOLETA | 01 FACTURA

		/*Numeración, conformada por serie y número correlativo*/
		$CreditNote->appendChild($xml->createElement("cbc:DocumentCurrencyCode", "PEN"));// PEN: Nuevos soles | USD: Dolares estadounidanses

		/*****************************
		* DATOS DEL FIRMANTE
		******************************/
		$cac_Signature = $CreditNote->appendChild($xml->createElement("cac:Signature"));
		$cac_Signature->appendChild($xml->createElement("cbc:ID", "20120958136"));//RUC DEL FIRMANTE
		$cac_Signature->appendChild($xml->createElement("cbc:Note", "Elaborado por Sistema de Emision Electronica Facturador SBPA (SEE-SBPA) 1.0.0"));//NOTA DEL FIRMANTE
		$cac_Signature->appendChild($xml->createElement("cbc:ValidatorID", "167847"));//VALIDATOR ID
		$cac_SignatoryParty = $cac_Signature->appendChild($xml->createElement("cac:SignatoryParty"));
		$cac_PartyIdentification = $cac_SignatoryParty->appendChild($xml->createElement("cac:PartyIdentification"));
		$cac_PartyIdentification->appendChild($xml->createElement("cbc:ID", "20120958136"));//RUC DEL FIRMANTE
		$cac_PartyName = $cac_SignatoryParty->appendChild($xml->createElement("cac:PartyName"));
		$cac_PartyName->appendChild($xml->createElement("cbc:Name","SOCIEDAD BENEFICENCIA PUBLICA DE AREQUIPA"));
		
		$cac_AgentParty = $cac_SignatoryParty->appendChild($xml->createElement("cac:AgentParty"));
		$cac_PartyIdentification = $cac_AgentParty->appendChild($xml->createElement("cac:PartyIdentification"));
		$cac_PartyIdentification->appendChild($xml->createElement("cbc:ID", "20120958136"));

		$cac_PartyName = $cac_AgentParty->appendChild($xml->createElement("cac:PartyName"));
		$cac_PartyName->appendChild($xml->createElement("cbc:Name","SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA"));

		$cac_PartyLegalEntity = $cac_AgentParty->appendChild($xml->createElement("cac:PartyLegalEntity"));
		$cac_PartyLegalEntity->appendChild($xml->createElement("cbc:RegistrationName","SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA"));
		$cac_DigitalSignatureAttachment = $cac_Signature->appendChild($xml->createElement("cac:DigitalSignatureAttachment"));
		$cac_ExternalReference = $cac_DigitalSignatureAttachment->appendChild($xml->createElement("cac:ExternalReference"));
		$cac_ExternalReference->appendChild($xml->createElement("cbc:URI","SIGN"));


		/*****************************
		* DATOS DEL EMISOR
		******************************/
		$cac_AccountingSupplierParty = $CreditNote->appendChild($xml->createElement("cac:AccountingSupplierParty"));
		/*Número de RUC*/
		$cac_CustomerAssignedAccountID = $cac_AccountingSupplierParty->appendChild($xml->createElement("cbc:CustomerAssignedAccountID","20120958136"));

		/*Tipo de documento de identidad*/
		$cac_AdditionalAccountID = $cac_AccountingSupplierParty->appendChild($xml->createElement("cbc:AdditionalAccountID","6"));

		$cac_Party = $cac_AccountingSupplierParty->appendChild($xml->createElement("cac:Party"));

		/*Nombre Comercial*/
		$cac_PartyName = $cac_Party->appendChild($xml->createElement("cac:PartyName"));
		$cbc_Name = $cac_PartyName->appendChild($xml->createElement("cbc:Name","SOCIEDAD BENEFICENCIA PUBLICA DE AREQUIPA"));

		//$cbc_Name->appendChild($xml->createCDATASection('SBPA')); 

		
		/*Domicilio Fiscal*/
		$cac_PostalAddress = $cac_Party->appendChild($xml->createElement("cac:PostalAddress"));
		$cac_PostalAddress->appendChild($xml->createElement("cbc:ID","040101")); //CODIGO LUGAR
		$cac_PostalAddress->appendChild($xml->createElement("cbc:StreetName","CALLE PIEROLA 201"));
		$cac_Country = $cac_PostalAddress->appendChild($xml->createElement("cac:Country"));
		$cac_Country->appendChild($xml->createElement("cbc:IdentificationCode","PE"));

		/*Apellidos y nombres, denominación o razón social*/
		$cac_PartyLegalEntity = $cac_Party->appendChild($xml->createElement("cac:PartyLegalEntity"));
		$cbc_RegistrationName = $cac_PartyLegalEntity->appendChild($xml->createElement("cbc:RegistrationName","SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA"));

		/*********************************
		* INFORMACION DEL USUARIO RECEPTOR
		**********************************/
		$tip_docident = $data['tipo_doc'];
		$num_docident = $data['cliente_doc'];
		
		$cac_AccountingCustomerParty = $CreditNote->appendChild($xml->createElement("cac:AccountingCustomerParty"));
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

		$cac_SellerSupplierParty = $CreditNote->appendChild($xml->createElement("cac:SellerSupplierParty"));
		$cac_Party = $cac_SellerSupplierParty->appendChild($xml->createElement("cac:Party"));
		$cac_PostalAddress = $cac_Party->appendChild($xml->createElement("cac:PostalAddress"));
		$cac_PostalAddress->appendChild($xml->createElement("cbc:AddressTypeCode"));
		
		/*****************************
		* TOTALES Y SUMATORIAS
		******************************/
		
		/*Sumatoria IGV*/
		$cac_TaxTotal = $CreditNote->appendChild($xml->createElement("cac:TaxTotal"));
		$cbc_TaxAmount = $cac_TaxTotal->appendChild($xml->createElement("cbc:TaxAmount",$data['total_igv']));//FILL DATA
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxSubtotal = $cac_TaxTotal->appendChild($xml->createElement("cac:TaxSubtotal"));
		$cbc_TaxAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxAmount",$data['total_igv']));//FILL DATA
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxCategory = $cac_TaxSubtotal->appendChild($xml->createElement("cac:TaxCategory"));
		$cac_TaxScheme = $cac_TaxCategory->appendChild($xml->createElement("cac:TaxScheme"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:ID", "1000"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:Name", "IGV"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:TaxTypeCode", "VAT"));

		/*Sumatoria ISC*/
		/*$cac_TaxTotal = $Invoice->appendChild($xml->createElement("cac:TaxTotal"));
		$cbc_TaxAmount = $cac_TaxTotal->appendChild($xml->createElement("cbc:TaxAmount",$total_isc));//FILL DATA
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxSubtotal = $cac_TaxTotal->appendChild($xml->createElement("cac:TaxSubtotal"));
		$cbc_TaxAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxAmount",$total_isc));//FILL DATA
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxCategory = $cac_TaxSubtotal->appendChild($xml->createElement("cac:TaxCategory"));
		$cac_TaxScheme = $cac_TaxCategory->appendChild($xml->createElement("cac:TaxScheme"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:ID", "2000"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:Name", "ISC"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:TaxTypeCode", "EXC"));*/

		$cac_LegalMonetaryTotal = $CreditNote->appendChild($xml->createElement("cac:LegalMonetaryTotal"));

		$cbc_AllowanceTotalAmount = $cac_LegalMonetaryTotal->appendChild($xml->createElement("cbc:AllowanceTotalAmount","0.00"));
		$cbc_AllowanceTotalAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));

		$cbc_ChargeTotalAmount = $cac_LegalMonetaryTotal->appendChild($xml->createElement("cbc:ChargeTotalAmount","0.00"));
		$cbc_ChargeTotalAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));

		$cbc_PayableAmount = $cac_LegalMonetaryTotal->appendChild($xml->createElement("cbc:PayableAmount",$data['total']));
		$cbc_PayableAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));

		if(count($data['items'])>0){
			$orden_item = 1;
			foreach($lineItems as $row){
				$_row = array(
					"codigo"=>$row['codigo'],
					"unidad"=>$row['NEW'],
					"cantidad"=>$row['cant'],
					"valor_unitario"=>$row['valor_unitario'],
					"igv"=>$row['igv'],
					"precio_venta_unitario"=>$row['precio_unitario'],
					"valor_unitario_total"=>$row['valor_unitario']*$row['cant'],
					"importe_total"=>$row['importe_total'],
					"descripcion"=>$row['descr']
				);
				$this->addItemLineCreditNoteXml($xml, $CreditNote, $orden_item, $row);
			}
		}

		/************************
		* TOTALES Y SUMATORIAS
		************************/
		$xml->formatOutput = true;
		$ruta_sin_firmar = IndexPath.DS."see-files/20120958136-".$tipos[$data['tipo']]."-".$data['serie']."-".$data['num'].".xml";
		$ruta_con_firma = IndexPath.DS."see-files/firma/20120958136-".$tipos[$data['tipo']]."-".$data['serie']."-".$data['num'].".xml";
		$ruta_comprimido = IndexPath.DS."see-files/firma/20120958136-".$tipos[$data['tipo']]."-".$data['serie']."-".$data['num'].".zip";
		$xml->save($ruta_sin_firmar);
		return array(
			'ruta_sin_firmar'=>$ruta_sin_firmar,
			'ruta_con_firma'=>$ruta_con_firma,
			'ruta_comprimido'=>$ruta_comprimido,
			'ruta_zip_con_firma'=>$ruta_comprimido
		);
	}

	function addItemLineCreditNoteXml(&$xml, &$Invoice, &$orden_item, $data = null){
		if(floatval($data['importe_total'])==0) return false;
		$cac_CreditNoteLine = $Invoice->appendChild($xml->createElement("cac:CreditNoteLine"));
		/*Número de orden del Ítem*/
		$cac_CreditNoteLine->appendChild($xml->createElement("cbc:ID",$orden_item));//FILL DATA

		/*Cantidad y unidad de medida por ítem*/
		$cbc_CreditedQuantity = $cac_CreditNoteLine->appendChild($xml->createElement("cbc:CreditedQuantity", $data['cantidad']));//FILL DATA
		$cbc_CreditedQuantity->appendChild($xml->createAttribute("unitCode"))
			->appendChild($xml->createTextNode($data['unidad']));//FILL DATA

		/*Monto total por linea por ítem*/
		$cbc_LineExtensionAmount = $cac_CreditNoteLine->appendChild($xml->createElement("cbc:LineExtensionAmount", $data['importe_total']));//FILL DATA
		$cbc_LineExtensionAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode("PEN"));//FILL DATA

		/*Precio de venta unitario por ítem y código (incluido el IGV). */
		$cac_PricingReference = $cac_CreditNoteLine->appendChild($xml->createElement("cac:PricingReference"));
		$cac_AlternativeConditionPrice = $cac_PricingReference->appendChild($xml->createElement("cac:AlternativeConditionPrice"));
		$cbc_PriceAmount = $cac_AlternativeConditionPrice->appendChild($xml->createElement("cbc:PriceAmount",$data['precio_venta_unitario']));//FILL DATA
		$cbc_PriceAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_AlternativeConditionPrice->appendChild($xml->createElement("cbc:PriceTypeCode","01"));//02 Para operaciones gratuidtas
		$cac_AllowanceCharge = $cac_CreditNoteLine->appendChild($xml->createElement("cac:AllowanceCharge"));
		$cac_AllowanceCharge->appendChild($xml->createElement("cbc:ChargeIndicator","false"));
		$cbc_LineExtensionAmount = $cac_AllowanceCharge->appendChild($xml->createElement("cbc:Amount", "0.00"));//FILL DATA
		$cbc_LineExtensionAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode("PEN"));//FILL DATA

		/*Impuestos por item (ISC)*/
		/*$cac_TaxTotal = $cac_CreditNoteLine->appendChild($xml->createElement("cac:TaxTotal"));
		$cbc_TaxAmount = $cac_TaxTotal->appendChild($xml->createElement("cbc:TaxAmount","77.58"));
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxSubtotal = $cac_TaxTotal->appendChild($xml->createElement("cac:TaxSubtotal"));
		$cbc_TaxAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxAmount","77.58"));
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxCategory = $cac_TaxSubtotal->appendChild($xml->createElement("cac:TaxCategory"));
		$cac_TaxCategory->appendChild($xml->createElement("cbc:TierRange","01"));
		$cac_TaxScheme = $cac_TaxCategory->appendChild($xml->createElement("cac:TaxScheme"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:ID", "2000"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:Name", "ISC"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:TaxTypeCode", "EXC"));*/

		/*Impuestos por item (IGV)*/
		$cac_TaxTotal = $cac_CreditNoteLine->appendChild($xml->createElement("cac:TaxTotal"));
		$cbc_TaxAmount = $cac_TaxTotal->appendChild($xml->createElement("cbc:TaxAmount",$data['igv']));//FILL DATA
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxSubtotal = $cac_TaxTotal->appendChild($xml->createElement("cac:TaxSubtotal"));
		$cbc_TaxableAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxableAmount",$data['igv']));//FILL DATA
		$cbc_TaxableAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cbc_TaxAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxAmount",$data['igv']));//FILL DATA
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxCategory = $cac_TaxSubtotal->appendChild($xml->createElement("cac:TaxCategory"));
		$cac_TaxCategory->appendChild($xml->createElement("cbc:TaxExemptionReasonCode","10"));
		$cac_TaxScheme = $cac_TaxCategory->appendChild($xml->createElement("cac:TaxScheme"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:ID", "1000"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:Name", "IGV"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:TaxTypeCode", "VAT"));

		/*Descripcion detallada por item*/
		$cac_Item = $cac_CreditNoteLine->appendChild($xml->createElement("cac:Item"));
		$cbc_Description = $cac_Item->appendChild($xml->createElement("cbc:Description",$data['descripcion']));
		//$cbc_Description->appendChild($xml->createCDATASection($data['descripcion']));//FILL DATA
		$cac_SellersItemIdentification = $cac_Item->appendChild($xml->createElement("cac:SellersItemIdentification"));
		$cac_SellersItemIdentification->appendChild($xml->createElement("cbc:ID",$data['codigo']));//FILL DATA
		$cac_AdditionalItemIdentification = $cac_Item->appendChild($xml->createElement("cac:AdditionalItemIdentification"));
		$cac_AdditionalItemIdentification->appendChild($xml->createElement("cbc:ID",""));//FILL DATA

		/*Valor unitario por ítem (No incluye IGV, ISC y otros Tributos ni cargos globales)*/
		$cac_Price = $cac_CreditNoteLine->appendChild($xml->createElement("cac:Price"));
		$cbc_PriceAmount = $cac_Price->appendChild($xml->createElement("cbc:PriceAmount",$data['valor_unitario']));
		$cbc_PriceAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$orden_item++;
		return true;
	}

	function generateDebitNote(&$xml, $data = null){
		//create the xml document
		$tipos = array(
			"ND"=>"08"
		);
		$monedas = array(
			"S"=>array("nomb"=>"SOLES","simb"=>"S/.","cod"=>"PEN"),
			"D"=>array("nomb"=>"DOLARES AMERICANOS","simb"=>"US$","cod"=>"USD")
		);
		//create the root element
		$DebitNote = $xml->appendChild($xml->createElement("DebitNote"));
		$DebitNote->appendChild($xml->createAttribute("xmlns"))
			->appendChild($xml->createTextNode('urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2'));

		$DebitNote->appendChild($xml->createAttribute("xmlns:cac"))
			->appendChild($xml->createTextNode('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2'));

		$DebitNote->appendChild($xml->createAttribute("xmlns:cbc"))
			->appendChild($xml->createTextNode('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2'));

		$DebitNote->appendChild($xml->createAttribute("xmlns:ccts"))
			->appendChild($xml->createTextNode('urn:un:unece:uncefact:documentation:2'));

		$DebitNote->appendChild($xml->createAttribute("xmlns:ds"))
			->appendChild($xml->createTextNode('http://www.w3.org/2000/09/xmldsig#'));

		$DebitNote->appendChild($xml->createAttribute("xmlns:ext"))
			->appendChild($xml->createTextNode('urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2'));

		$DebitNote->appendChild($xml->createAttribute("xmlns:qdt"))
			->appendChild($xml->createTextNode('urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2'));

		$DebitNote->appendChild($xml->createAttribute("xmlns:sac"))
			->appendChild($xml->createTextNode('urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1'));

		$DebitNote->appendChild($xml->createAttribute("xmlns:udt"))
			->appendChild($xml->createTextNode('urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2'));

		/*$DebitNote->appendChild($xml->createAttribute("xmlns:xsi"))
			->appendChild($xml->createTextNode('http://www.w3.org/2001/XMLSchema-instance'));*/


		$ext_UBLExtensions = $DebitNote->appendChild($xml->createElement("ext:UBLExtensions"));
		$ext_UBLExtension = $ext_UBLExtensions->appendChild($xml->createElement("ext:UBLExtension"));
		$ext_ExtensionContent = $ext_UBLExtension->appendChild($xml->createElement("ext:ExtensionContent"));
		$sac_AdditionalInformation = $ext_ExtensionContent->appendChild($xml->createElement("sac:AdditionalInformation"));

		$ext_UBLExtension = $ext_UBLExtensions->appendChild($xml->createElement("ext:UBLExtension"));
		$ext_ExtensionContent = $ext_UBLExtension->appendChild($xml->createElement("ext:ExtensionContent"));

		/*****************************
		* DETALLE DEL DOCUMENTO
		******************************/
		$total = $model['total'];
		$total_importe_total = $total;
		$decimal = round((($total-((int)$total))*100),0);
		$en_letras = Number::lit($total).' CON '.$decimal.'/100 '.$monedas[$model["moneda"]]["nomb"];

		/*Total valor de venta - operaciones gravadas*/
		$sac_AdditionalMonetaryTotal_1001 = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalMonetaryTotal"));
		$sac_AdditionalMonetaryTotal_1001_ID = $sac_AdditionalMonetaryTotal_1001->appendChild($xml->createElement("cbc:ID", "1001"));
		$sac_AdditionalMonetaryTotal_1001_PayableAmount = $sac_AdditionalMonetaryTotal_1001->appendChild($xml->createElement("cbc:PayableAmount", $data['total_ope_gravadas']));
		$sac_AdditionalMonetaryTotal_1001_PayableAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));

		/*Total valor de venta - operaciones inafectas*/
		$sac_AdditionalMonetaryTotal_1002 = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalMonetaryTotal"));
		$sac_AdditionalMonetaryTotal_1002_ID = $sac_AdditionalMonetaryTotal_1002->appendChild($xml->createElement("cbc:ID", "1002"));
		$sac_AdditionalMonetaryTotal_1002_PayableAmount = $sac_AdditionalMonetaryTotal_1002->appendChild($xml->createElement("cbc:PayableAmount", $data['total_ope_inafectas']));
		$sac_AdditionalMonetaryTotal_1002_PayableAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));

		/*Total valor de venta - operaciones exoneradas*/
		$sac_AdditionalMonetaryTotal_1003 = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalMonetaryTotal"));
		$sac_AdditionalMonetaryTotal_1003_ID = $sac_AdditionalMonetaryTotal_1003->appendChild($xml->createElement("cbc:ID", "1003"));
		$sac_AdditionalMonetaryTotal_1003_PayableAmount = $sac_AdditionalMonetaryTotal_1003->appendChild($xml->createElement("cbc:PayableAmount", $data['total_ope_exoneradas']));
		$sac_AdditionalMonetaryTotal_1003_PayableAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));

		/*Leyendas*/
		/*$sac_AdditionalProperty = $sac_AdditionalInformation->appendChild($xml->createElement("sac:AdditionalProperty"));
		$sac_AdditionalProperty_ID = $sac_AdditionalProperty->appendChild($xml->createElement("cbc:ID", "1000"));
		$sac_AdditionalProperty_Value = $sac_AdditionalProperty->appendChild($xml->createElement("cbc:Value", $en_letras));*/

		/*Leyendas*/
		//$sac_SUNATTransaction = $sac_AdditionalInformation->appendChild($xml->createElement("sac:SUNATTransaction"));
		//$sac_SUNATTransaction_ID = $sac_SUNATTransaction->appendChild($xml->createElement("cbc:ID", "01"));
		
		/*Versión del UBL*/
		$cbc_UBLVersionID = $DebitNote->appendChild($xml->createElement("cbc:UBLVersionID", "2.0"));

		/*Versión de la estructura del documento*/
		$cbc_CustomizationID = $DebitNote->appendChild($xml->createElement("cbc:CustomizationID", "1.0"));

		/*****************************
		* INFORMACION DEL COMPROBANTE
		******************************/
		$cbc_ID = $DebitNote->appendChild($xml->createElement("cbc:ID", $data['serie']."-".$data['num']));

		/*Fecha de emisión*/
		$cbc_IssueDate = $DebitNote->appendChild($xml->createElement("cbc:IssueDate", date("Y-m-d", $data['fecreg'])));

		/*Tipo de documento (Boleta)*/
		$cbc_InvoiceTypeCode = $DebitNote->appendChild($xml->createElement("cbc:InvoiceTypeCode", $tipos[$data['tipo']]));//03 BOLETA | 01 FACTURA

		/*Numeración, conformada por serie y número correlativo*/
		$DebitNote->appendChild($xml->createElement("cbc:DocumentCurrencyCode", "PEN"));// PEN: Nuevos soles | USD: Dolares estadounidanses

		/*****************************
		* DATOS DEL FIRMANTE
		******************************/
		$cac_Signature = $DebitNote->appendChild($xml->createElement("cac:Signature"));
		$cac_Signature->appendChild($xml->createElement("cbc:ID", "20120958136"));//RUC DEL FIRMANTE
		$cac_Signature->appendChild($xml->createElement("cbc:Note", "Elaborado por Sistema de Emision Electronica Facturador SBPA (SEE-SBPA) 1.0.0"));//NOTA DEL FIRMANTE
		$cac_Signature->appendChild($xml->createElement("cbc:ValidatorID", "167847"));//VALIDATOR ID
		$cac_SignatoryParty = $cac_Signature->appendChild($xml->createElement("cac:SignatoryParty"));
		$cac_PartyIdentification = $cac_SignatoryParty->appendChild($xml->createElement("cac:PartyIdentification"));
		$cac_PartyIdentification->appendChild($xml->createElement("cbc:ID", "20120958136"));//RUC DEL FIRMANTE
		$cac_PartyName = $cac_SignatoryParty->appendChild($xml->createElement("cac:PartyName"));
		$cac_PartyName->appendChild($xml->createElement("cbc:Name","SOCIEDAD BENEFICENCIA PUBLICA DE AREQUIPA"));
		
		$cac_AgentParty = $cac_SignatoryParty->appendChild($xml->createElement("cac:AgentParty"));
		$cac_PartyIdentification = $cac_AgentParty->appendChild($xml->createElement("cac:PartyIdentification"));
		$cac_PartyIdentification->appendChild($xml->createElement("cbc:ID", "20120958136"));

		$cac_PartyName = $cac_AgentParty->appendChild($xml->createElement("cac:PartyName"));
		$cac_PartyName->appendChild($xml->createElement("cbc:Name","SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA"));

		$cac_PartyLegalEntity = $cac_AgentParty->appendChild($xml->createElement("cac:PartyLegalEntity"));
		$cac_PartyLegalEntity->appendChild($xml->createElement("cbc:RegistrationName","SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA"));
		$cac_DigitalSignatureAttachment = $cac_Signature->appendChild($xml->createElement("cac:DigitalSignatureAttachment"));
		$cac_ExternalReference = $cac_DigitalSignatureAttachment->appendChild($xml->createElement("cac:ExternalReference"));
		$cac_ExternalReference->appendChild($xml->createElement("cbc:URI","SIGN"));


		/*****************************
		* DATOS DEL EMISOR
		******************************/
		$cac_AccountingSupplierParty = $DebitNote->appendChild($xml->createElement("cac:AccountingSupplierParty"));
		/*Número de RUC*/
		$cac_CustomerAssignedAccountID = $cac_AccountingSupplierParty->appendChild($xml->createElement("cbc:CustomerAssignedAccountID","20120958136"));

		/*Tipo de documento de identidad*/
		$cac_AdditionalAccountID = $cac_AccountingSupplierParty->appendChild($xml->createElement("cbc:AdditionalAccountID","6"));

		$cac_Party = $cac_AccountingSupplierParty->appendChild($xml->createElement("cac:Party"));

		/*Nombre Comercial*/
		$cac_PartyName = $cac_Party->appendChild($xml->createElement("cac:PartyName"));
		$cbc_Name = $cac_PartyName->appendChild($xml->createElement("cbc:Name","SOCIEDAD BENEFICENCIA PUBLICA DE AREQUIPA"));

		//$cbc_Name->appendChild($xml->createCDATASection('SBPA')); 

		
		/*Domicilio Fiscal*/
		$cac_PostalAddress = $cac_Party->appendChild($xml->createElement("cac:PostalAddress"));
		$cac_PostalAddress->appendChild($xml->createElement("cbc:ID","040101")); //CODIGO LUGAR
		$cac_PostalAddress->appendChild($xml->createElement("cbc:StreetName","CALLE PIEROLA 201"));
		$cac_Country = $cac_PostalAddress->appendChild($xml->createElement("cac:Country"));
		$cac_Country->appendChild($xml->createElement("cbc:IdentificationCode","PE"));

		/*Apellidos y nombres, denominación o razón social*/
		$cac_PartyLegalEntity = $cac_Party->appendChild($xml->createElement("cac:PartyLegalEntity"));
		$cbc_RegistrationName = $cac_PartyLegalEntity->appendChild($xml->createElement("cbc:RegistrationName","SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA"));

		/*********************************
		* INFORMACION DEL USUARIO RECEPTOR
		**********************************/
		$tip_docident = $data['tipo_doc'];
		$num_docident = $data['cliente_doc'];
		
		$cac_AccountingCustomerParty = $DebitNote->appendChild($xml->createElement("cac:AccountingCustomerParty"));
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

		$cac_SellerSupplierParty = $DebitNote->appendChild($xml->createElement("cac:SellerSupplierParty"));
		$cac_Party = $cac_SellerSupplierParty->appendChild($xml->createElement("cac:Party"));
		$cac_PostalAddress = $cac_Party->appendChild($xml->createElement("cac:PostalAddress"));
		$cac_PostalAddress->appendChild($xml->createElement("cbc:AddressTypeCode"));
		
		/*****************************
		* TOTALES Y SUMATORIAS
		******************************/
		
		/*Sumatoria IGV*/
		$cac_TaxTotal = $DebitNote->appendChild($xml->createElement("cac:TaxTotal"));
		$cbc_TaxAmount = $cac_TaxTotal->appendChild($xml->createElement("cbc:TaxAmount",$data['total_igv']));//FILL DATA
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxSubtotal = $cac_TaxTotal->appendChild($xml->createElement("cac:TaxSubtotal"));
		$cbc_TaxAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxAmount",$data['total_igv']));//FILL DATA
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxCategory = $cac_TaxSubtotal->appendChild($xml->createElement("cac:TaxCategory"));
		$cac_TaxScheme = $cac_TaxCategory->appendChild($xml->createElement("cac:TaxScheme"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:ID", "1000"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:Name", "IGV"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:TaxTypeCode", "VAT"));

		/*Sumatoria ISC*/
		/*$cac_TaxTotal = $Invoice->appendChild($xml->createElement("cac:TaxTotal"));
		$cbc_TaxAmount = $cac_TaxTotal->appendChild($xml->createElement("cbc:TaxAmount",$total_isc));//FILL DATA
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxSubtotal = $cac_TaxTotal->appendChild($xml->createElement("cac:TaxSubtotal"));
		$cbc_TaxAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxAmount",$total_isc));//FILL DATA
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxCategory = $cac_TaxSubtotal->appendChild($xml->createElement("cac:TaxCategory"));
		$cac_TaxScheme = $cac_TaxCategory->appendChild($xml->createElement("cac:TaxScheme"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:ID", "2000"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:Name", "ISC"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:TaxTypeCode", "EXC"));*/

		$cac_LegalMonetaryTotal = $DebitNote->appendChild($xml->createElement("cac:LegalMonetaryTotal"));

		$cbc_AllowanceTotalAmount = $cac_LegalMonetaryTotal->appendChild($xml->createElement("cbc:AllowanceTotalAmount","0.00"));
		$cbc_AllowanceTotalAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));

		$cbc_ChargeTotalAmount = $cac_LegalMonetaryTotal->appendChild($xml->createElement("cbc:ChargeTotalAmount","0.00"));
		$cbc_ChargeTotalAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));

		$cbc_PayableAmount = $cac_LegalMonetaryTotal->appendChild($xml->createElement("cbc:PayableAmount",$data['total']));
		$cbc_PayableAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));

		if(count($lineItems)>0){
			$orden_item = 1;
			foreach($lineItems as $row){
				$_row = array(
					"codigo"=>$row['codigo'],
					"unidad"=>$row['cod_unidad'],
					"cantidad"=>$row['cant'],
					"valor_unitario"=>$row['valor_unitario'],
					"igv"=>$row['igv'],
					"precio_venta_unitario"=>$row['precio_unitario'],
					"valor_unitario_total"=>$row['valor_unitario']*$row['cant'],
					"importe_total"=>$row['importe_total'],
					"descripcion"=>$row['descr']
				);
				$this->addItemLineDebitNoteXml($xml, $DebitNote, $orden_item, $row);
			}
		}

		/************************
		* TOTALES Y SUMATORIAS
		************************/
		$xml->formatOutput = true;
		$ruta_sin_firmar = IndexPath.DS."see-files/20120958136-".$tipos[$data['tipo']]."-".$data['serie']."-".$data['num'].".xml";
		$ruta_con_firma = IndexPath.DS."see-files/firma/20120958136-".$tipos[$data['tipo']]."-".$data['serie']."-".$data['num'].".xml";
		$ruta_comprimido = IndexPath.DS."see-files/firma/20120958136-".$tipos[$data['tipo']]."-".$data['serie']."-".$data['num'].".zip";
		$xml->save($ruta_sin_firmar);
		return array(
			'ruta_sin_firmar'=>$ruta_sin_firmar,
			'ruta_con_firma'=>$ruta_con_firma,
			'ruta_comprimido'=>$ruta_comprimido,
			'ruta_zip_con_firma'=>$ruta_comprimido
		);
	}

	function addItemLineDebitNoteXml(&$xml, &$Invoice, &$orden_item, $data = null){
		if(floatval($data['importe_total'])==0) return false;
		$cac_DebitNoteLine = $Invoice->appendChild($xml->createElement("cac:DebitNoteLine"));
		/*Número de orden del Ítem*/
		$cac_DebitNoteLine->appendChild($xml->createElement("cbc:ID",$orden_item));//FILL DATA

		/*Cantidad y unidad de medida por ítem*/
		$cbc_CreditedQuantity = $cac_DebitNoteLine->appendChild($xml->createElement("cbc:CreditedQuantity", $data['cantidad']));//FILL DATA
		$cbc_CreditedQuantity->appendChild($xml->createAttribute("unitCode"))
			->appendChild($xml->createTextNode($data['unidad']));//FILL DATA

		/*Monto total por linea por ítem*/
		$cbc_LineExtensionAmount = $cac_DebitNoteLine->appendChild($xml->createElement("cbc:LineExtensionAmount", $data['importe_total']));//FILL DATA
		$cbc_LineExtensionAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode("PEN"));//FILL DATA

		/*Precio de venta unitario por ítem y código (incluido el IGV). */
		$cac_PricingReference = $cac_DebitNoteLine->appendChild($xml->createElement("cac:PricingReference"));
		$cac_AlternativeConditionPrice = $cac_PricingReference->appendChild($xml->createElement("cac:AlternativeConditionPrice"));
		$cbc_PriceAmount = $cac_AlternativeConditionPrice->appendChild($xml->createElement("cbc:PriceAmount",$data['precio_venta_unitario']));//FILL DATA
		$cbc_PriceAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_AlternativeConditionPrice->appendChild($xml->createElement("cbc:PriceTypeCode","01"));//02 Para operaciones gratuidtas
		$cac_AllowanceCharge = $cac_DebitNoteLine->appendChild($xml->createElement("cac:AllowanceCharge"));
		$cac_AllowanceCharge->appendChild($xml->createElement("cbc:ChargeIndicator","false"));
		$cbc_LineExtensionAmount = $cac_AllowanceCharge->appendChild($xml->createElement("cbc:Amount", "0.00"));//FILL DATA
		$cbc_LineExtensionAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode("PEN"));//FILL DATA

		/*Impuestos por item (ISC)*/
		/*$cac_TaxTotal = $cac_DebitNoteLine->appendChild($xml->createElement("cac:TaxTotal"));
		$cbc_TaxAmount = $cac_TaxTotal->appendChild($xml->createElement("cbc:TaxAmount","77.58"));
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxSubtotal = $cac_TaxTotal->appendChild($xml->createElement("cac:TaxSubtotal"));
		$cbc_TaxAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxAmount","77.58"));
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxCategory = $cac_TaxSubtotal->appendChild($xml->createElement("cac:TaxCategory"));
		$cac_TaxCategory->appendChild($xml->createElement("cbc:TierRange","01"));
		$cac_TaxScheme = $cac_TaxCategory->appendChild($xml->createElement("cac:TaxScheme"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:ID", "2000"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:Name", "ISC"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:TaxTypeCode", "EXC"));*/

		/*Impuestos por item (IGV)*/
		$cac_TaxTotal = $cac_DebitNoteLine->appendChild($xml->createElement("cac:TaxTotal"));
		$cbc_TaxAmount = $cac_TaxTotal->appendChild($xml->createElement("cbc:TaxAmount",$data['igv']));//FILL DATA
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxSubtotal = $cac_TaxTotal->appendChild($xml->createElement("cac:TaxSubtotal"));
		$cbc_TaxableAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxableAmount",$data['igv']));//FILL DATA
		$cbc_TaxableAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cbc_TaxAmount = $cac_TaxSubtotal->appendChild($xml->createElement("cbc:TaxAmount",$data['igv']));//FILL DATA
		$cbc_TaxAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$cac_TaxCategory = $cac_TaxSubtotal->appendChild($xml->createElement("cac:TaxCategory"));
		$cac_TaxCategory->appendChild($xml->createElement("cbc:TaxExemptionReasonCode","10"));
		$cac_TaxScheme = $cac_TaxCategory->appendChild($xml->createElement("cac:TaxScheme"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:ID", "1000"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:Name", "IGV"));
		$cac_TaxScheme->appendChild($xml->createElement("cbc:TaxTypeCode", "VAT"));

		/*Descripcion detallada por item*/
		$cac_Item = $cac_DebitNoteLine->appendChild($xml->createElement("cac:Item"));
		$cbc_Description = $cac_Item->appendChild($xml->createElement("cbc:Description",$data['descripcion']));
		//$cbc_Description->appendChild($xml->createCDATASection($data['descripcion']));//FILL DATA
		$cac_SellersItemIdentification = $cac_Item->appendChild($xml->createElement("cac:SellersItemIdentification"));
		$cac_SellersItemIdentification->appendChild($xml->createElement("cbc:ID",$data['codigo']));//FILL DATA
		$cac_AdditionalItemIdentification = $cac_Item->appendChild($xml->createElement("cac:AdditionalItemIdentification"));
		$cac_AdditionalItemIdentification->appendChild($xml->createElement("cbc:ID",""));//FILL DATA

		/*Valor unitario por ítem (No incluye IGV, ISC y otros Tributos ni cargos globales)*/
		$cac_Price = $cac_DebitNoteLine->appendChild($xml->createElement("cac:Price"));
		$cbc_PriceAmount = $cac_Price->appendChild($xml->createElement("cbc:PriceAmount",$data['valor_unitario']));
		$cbc_PriceAmount->appendChild($xml->createAttribute("currencyID"))
			->appendChild($xml->createTextNode('PEN'));
		$orden_item++;
		return true;
	}

	function execute_send_sunat($test=null){
		global $f;
		$data = $f->request->data;
		if($test==null){
			$model = $f->model('cj/comp')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;	
			$data['ruta'] = $model['see_sbpa']['ruta_zip_con_firma'];
		}else{
			$data['ruta'] = $test['ruta_zip_con_firma'];
		}
		
		
		$filename = explode("/", $data['ruta']);
		$filename = $filename[count($filename)-1];
		$options = array(
			//'proxy_host'=> "192.168.1.3",
			//'proxy_port'=> 8080,
			//"soap_version"=>SOAP_1_2,
			"trace" => 1,
			"exceptions" => 0
		);

		/*Auth*/
		$username = '20120958136SIST2016';
		$password = 'sbp@2016';
		$strWSSENS = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd";
		$objSoapVarUser = new SoapVar($username, XSD_STRING, NULL, $strWSSENS, NULL, $strWSSENS);
		$objSoapVarPass = new SoapVar($password, XSD_STRING, NULL, $strWSSENS, NULL, $strWSSENS);
		$objWSSEAuth = new clsWSSEAuth($objSoapVarUser, $objSoapVarPass);
		$objSoapVarWSSEAuth = new SoapVar($objWSSEAuth, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'UsernameToken', $strWSSENS);
		$objWSSEToken = new clsWSSEToken($objSoapVarWSSEAuth);
		$objSoapVarWSSEToken = new SoapVar($objWSSEToken, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'UsernameToken', $strWSSENS);
		$objSoapVarHeaderVal=new SoapVar($objSoapVarWSSEToken, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'Security', $strWSSENS);
		$objSoapVarWSSEHeader = new SoapHeader($strWSSENS, 'Security', $objSoapVarHeaderVal);//,true , 'http://abce.com'
		$client = new SoapClient("https://www.sunat.gob.pe/ol-ti-itcpgem-sqa/billService?wsdl",$options);
		$client->__setSoapHeaders(array($objSoapVarWSSEHeader));
		$filename = $filename;//IndexPath.DS.'20120958136-01-F001-1.zip'
		$handle = fopen($data['ruta'], "r");
		$contents = fread($handle, filesize($data['ruta']));
		$params = array(
			'fileName'=>$filename,
			'contentFile'=>$contents
		);
		try{
			$result = $client->sendBill($params);
			print_r($result);
			if(isset($result->applicationResponse)){
				$fichero = IndexPath.DS."see-files/cdr/".$filename;
				$persona = $result->applicationResponse;
				file_put_contents($fichero, $persona, FILE_APPEND | LOCK_EX);
				
				echo "CONSTANCIA ZIP GUARDADA CORRECTAMENTE";
			}
		}catch(SoapFault $fault){
			echo 'Request : <br/><xmp>',
			$client->__getLastRequest(),
			'</xmp><br/><br/> Error Message : <br/>',
			$fault->getMessage();
		}
	}
	private function create_zip($files = array(),$destination = '',$overwrite = false) {
		if(file_exists($destination) && !$overwrite) { return false; }
		$valid_files = array();
		if(is_array($files)) {
			foreach($files as $file) {
				if(file_exists($file)) {
					$valid_files[] = $file;
				}
			}
		}
		if(count($valid_files)) {
			$zip = new ZipArchive();
			if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			foreach($valid_files as $file) {
				$new_filename = substr($file,strrpos($file,'/') + 1);
				$zip->addFile($file,$new_filename);
			}
			$zip->close();
			return file_exists($destination);
		}
		else
		{
			return false;
		}
	}
	function libxml_display_error($error)
	{
		$return = "<br/>\n";
		switch ($error->level) {
			case LIBXML_ERR_WARNING:
				$return .= "<b>Warning $error->code</b>: ";
				break;
			case LIBXML_ERR_ERROR:
				$return .= "<b>Error $error->code</b>: ";
				break;
			case LIBXML_ERR_FATAL:
				$return .= "<b>Fatal Error $error->code</b>: ";
				break;
		}
		$return .= trim($error->message);
		if ($error->file) {
		$return .=    " in <b>$error->file</b>";
		}
		$return .= " on line <b>$error->line</b>\n";
		return $return;
	}

	function libxml_display_errors() {
		$errors = $this->libxml_get_errors();
		foreach ($errors as $error) {
			print libxml_display_error($error);
		}
		$this->libxml_clear_errors();
	}
}
class clsWSSEAuth {
	private $Username;
	private $Password;
	function __construct($username, $password) {
		$this->Username=$username;
		$this->Password=$password;
	}
}
 
class clsWSSEToken {
	private $UsernameToken;
	function __construct ($innerVal){
		$this->UsernameToken = $innerVal;
	}
}
?>