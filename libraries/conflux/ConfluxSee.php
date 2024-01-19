<?php
//define('APPPATH',IndexPath.DS);
require_once APPPATH.'/libraries/conflux/ConfluxException.php';
require_once APPPATH.'/libraries/conflux/ConfluxInvoice.php';
require_once APPPATH.'/libraries/conflux/ConfluxSoap.php';
require_once APPPATH.'/libraries/conflux/ConfluxConfig.php';
include APPPATH."/libraries/xmlseclibs/autoload.php";
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use RobRichards\XMLSecLibs\XMLSecEnc;
class ConfluxSee {
	protected $output;
	public $tipos_doc;
	public $tipos;
	public $monedas;
	public function __construct(){
		$this->tipos_doc = array(
            '0'=>'0',
            'DNI'=>'1',
            'CAE'=>'4',
            'RUC'=>'6',
            'PAS'=>'7',
            'CED'=>'A'
        );
        $this->tipos = array(
            "F"=>"01",
            "B"=>"03",
            "NC"=>"07",
            "ND"=>"08"
        );
        $this->monedas = array(
            "PEN"=>array("nomb"=>"SOLES","simb"=>"S/.","cod"=>"PEN"),
            "USD"=>array("nomb"=>"DOLARES AMERICANOS","simb"=>"US$","cod"=>"USD")
        );
	}
	public function config($item){
		$ci = new ConfluxConfig();
		return $ci->item($item);
	}
	public function init($data){
		
		switch($this->config("environment")){
			case "homologation":
				$this->init_homologation($data);
				break;
			case "testing":
				$this->init_testing($data);
				break;
			case "production":
				$this->init_production($data);
				break;
			default:
				$this->output=array(
					'status'=>'error',
					'error_message'=>'No se definio el entorno de ejecucion de Conflux SEE',
					'error_data'=>array()
				);
				break;
		}
	}
	protected function init_homologation($data){
		$ConfluxInvoice = new ConfluxInvoice();
		$data_validated = $ConfluxInvoice->validate($data);
		$this->output = $data_validated;
	}
	protected function init_testing($data){
		
	}
	protected function init_production($data){

	}
	public function generateFiles($data, $filename, $type='invoice'){
		switch ($type) {
			case 'invoice':
				if($data['tipo']=='F' || $data['tipo']=='B'){
					require_once APPPATH.'libraries/conflux/schema/SchemaInvoice.php';
					$SchemaInvoice = new SchemaInvoice();
					$response = $SchemaInvoice->generate($data, $filename, array(
						'tipos_doc'=>$this->tipos_doc,
						'tipos'=>$this->tipos,
						'monedas'=>$this->monedas
					));
				}elseif($data['tipo']=='NC'){
					require_once APPPATH.'libraries/conflux/schema/SchemaCreditNote.php';
					$SchemaCreditNote = new SchemaCreditNote();
					$response = $SchemaCreditNote->generate($data, $filename, array(
						'tipos_doc'=>$this->tipos_doc,
						'tipos'=>$this->tipos,
						'monedas'=>$this->monedas
					));
				}elseif($data['tipo']=='ND'){
					require_once APPPATH.'libraries/conflux/schema/SchemaCreditNote.php';
					$SchemaDebitNote = new SchemaDebitNote();
					$response = $SchemaDebitNote->generate($data, $filename, array(
						'tipos_doc'=>$this->tipos_doc,
						'tipos'=>$this->tipos,
						'monedas'=>$this->monedas
					));
				}
				return $response;
				break;
			case 'summary':
				return array(
					'status'=>'error',
					'error_message'=>'La generacion de archivos de tipo resumen no esta soportado en este momento',
					'error_data'=>array()
				);
				break;
			default:
				return array(
					'status'=>'error',
					'error_message'=>'El parametro type para la generacionde archivos no es valido',
					'error_data'=>array()
				);
				break;
		}
	}
	protected function saveFileLoalStorage($input, $destination, $type='domdocument_object'){
		switch ($type) {
			case 'domdocument_object':
				
				break;
			case 'file_base_64':

				break;
			default:
				
				break;
		}
	}
	public function signXML($ruta_xml){
		try {

			$ruta_xml_parts = explode('/', $ruta_xml);

			if(count($ruta_xml_parts)>0){
				$filename = $ruta_xml_parts[count($ruta_xml_parts)-1];
				$name_ext = explode('.', $filename);
				if(count($name_ext)==2){
					$name = $name_ext[0];
				}else{
					throw new ConfluxException("La ruta XML no tiene extension");
				}
			}else{
				throw new ConfluxException("La ruta XML tiene un formato incorrecto");
			}

			$ci = new ConfluxConfig();
			$doc = new DOMDocument();
			$doc->load($ruta_xml);
			$objDSig = new XMLSecurityDSig();
			$objDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
			$objDSig->addReference(
				$doc,
				XMLSecurityDSig::SHA1,
				array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'),
				array('force_uri' => true)
			);

			$objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array('type'=>'private'));
			$certificado = file_get_contents(IndexPath.DS.'dc/signkey.pfx');
			$certPassword = $ci->item('conflux-pfx_clave');
			openssl_pkcs12_read($certificado, $certs, $certPassword);

			/*echo $certs['pkey'];
			echo '<br>-------------------------------------------------</br>';
			echo $certs['cert'];*/
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

			$ruta_xml_firmado = "see-files/firma/".$name.".xml";
			$ruta_zip_firmado = "see-files/firma/".$name.".zip";
			$doc->save($ruta_xml_firmado);
			$ziping = $this->create_zip(array($ruta_xml_firmado), $ruta_zip_firmado, false);
			if(!$ziping){
				throw new ConfluxException("Ha ocurrido un error al zipear el archivo xml especificado");
			}
			$this->output = array(
				'status'=>'success',
				'success_message'=>'Se ha firmado y zipeado correctametne los archivos xml especificados',
				'success_data'=>array(
					'digest_value'=>$doc->getElementsByTagName("DigestValue")->item(0)->nodeValue,
					'signature_value'=>$doc->getElementsByTagName("SignatureValue")->item(0)->nodeValue,
					'ruta_xml_firmado'=>$ruta_xml_firmado,
					'ruta_zip_firmado'=>$ruta_zip_firmado
				)
			);
			return $this->output;
		} catch (ConfluxException $e) {
			$this->output = array(
				'status'=>'error',
				'error_message'=>$e->getMessage(),
                'error_data'=>$e->getData()
			);
			return $this->output;
		}
	}
	private function create_zip($files = array(),$destination = '',$overwrite = false) {
        //if(file_exists($destination) && !$overwrite) { return false; }
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
            $zip->open($destination,ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
            /*if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
            	
                //return false;
            }*/
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
	public function setOutput($data){
		$this->output=$data;
	}
	public function getOutput(){
		if($this->output==null){
			$this->output=array(
				'status'=>'error',
				'error_message'=>'Ninguna respuesta disponible por porte de Conflux SEE',
				'error_data'=>array()
			);
		}
		return $this->output;
	}
}
?>