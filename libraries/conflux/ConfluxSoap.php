<?php
require APPPATH.'/libraries/Sunaterrors.php';
class ConfluxSoap {
    public function connect($url_soap, $data, $method = 'sendBill'){
        $ci = new ConfluxConfig();
        $username = $ci->item('conflux-ruc').$ci->item('conflux-sol_usuario');
        $password = $ci->item('conflux-sol_clave');
        $strWSSENS = "http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd";
        $objSoapVarUser = new SoapVar($username, XSD_STRING, NULL, $strWSSENS, NULL, $strWSSENS);
        $objSoapVarPass = new SoapVar($password, XSD_STRING, NULL, $strWSSENS, NULL, $strWSSENS);
        $objWSSEAuth = new clsWSSEAuth($objSoapVarUser, $objSoapVarPass);
        $objSoapVarWSSEAuth = new SoapVar($objWSSEAuth, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'UsernameToken', $strWSSENS);
        $objWSSEToken = new clsWSSEToken($objSoapVarWSSEAuth);
        $objSoapVarWSSEToken = new SoapVar($objWSSEToken, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'UsernameToken', $strWSSENS);
        $objSoapVarHeaderVal=new SoapVar($objSoapVarWSSEToken, SOAP_ENC_OBJECT, NULL, $strWSSENS, 'Security', $strWSSENS);
        $objSoapVarWSSEHeader = new SoapHeader($strWSSENS, 'Security', $objSoapVarHeaderVal);//,true , 'http://abce.com'
         $options = array(
            //'proxy_host'=> "192.168.1.3",
            //'proxy_port'=> 8080,
            //"soap_version"=>SOAP_1_2,
            "trace" => 1,
            "exceptions" => 0
        );
        //$client = new SoapClient("https://www.sunat.gob.pe/ol-ti-itcpgem-sqa/billService?wsdl",$options);
        $client = new SoapClient($url_soap,$options);
        $client->__setSoapHeaders(array($objSoapVarWSSEHeader));
        switch ($method) {
            case 'sendBill':
                $response = $this->sendBill($client, $data);
                return $response;
                break;
            default:
                return array(
                    'status'=>'error',
                    'error_message'=>'No se ha podido establecer una conexion con WS Sunat',
                    'error_data'=>arraY()
                );
                break;
        }
    }
    public function sendBill($client, $data){
        try {
            $filename = explode("/", $data['ruta_zip_firmado']);
            $filename = $filename[count($filename)-1];
            $handle = fopen($data['ruta_zip_firmado'], "r");
            $contents = fread($handle, filesize($data['ruta_zip_firmado']));
            $params = array(
                'fileName'=>$filename,
                'contentFile'=>$contents
            );
            $result = $client->sendBill($params);
            /*echo "PRINT R RESULT SEND BILL";
            print_r($result);
            echo "========================================";*/
            if(isset($result->applicationResponse)){
                $fichero = "see-files/cdr/".$filename;
                $persona = $result->applicationResponse;
                //file_put_contents($fichero, $persona, FILE_APPEND | LOCK_EX);
                if($fh = fopen($fichero,'w')){
                    $stringData = $result->applicationResponse;
                    fwrite($fh, $stringData);
                    fclose($fh);
                    $cdr = new ZipArchive();
                    if ($cdr->open($fichero) === TRUE) {
                        $cdr->extractTo("see-files/cdr/");
                        //print_r($cdr);
                        $doc = new DOMDocument();
                        $doc->load("see-files/cdr/".$cdr->getNameIndex(0));
                        $ReferenceID = $doc->getElementsByTagName("ReferenceID")->item(0)->nodeValue;
                        $ResponseCode = $doc->getElementsByTagName("ResponseCode")->item(0)->nodeValue;
                        $Description = $doc->getElementsByTagName("Description")->item(0)->nodeValue;
                        $Note = ($doc->getElementsByTagName("Note")->length!=0)?$doc->getElementsByTagName("Note")->item(0)->nodeValue:'';
                        if(is_numeric($ResponseCode)){
                            if(floatval($ResponseCode)>0){
                                //print_r($Note);
                                throw new ConfluxException(Sunaterrors::getError($ResponseCode),0,null, array(
                                    'sunat_description'=>$Description,
                                    'sunat_responsecode'=>$ResponseCode,
                                    'sunat_note'=>$Note,
                                    'ruta_cdr_xml'=>"see-files/cdr/".$cdr->getNameIndex(0)));
                            }else{
                                return array(
                                    'status'=>'success',
                                    'success_message'=>$Description,
                                    'success_data'=>array(
                                        'ruta_cdr_xml'=>"see-files/cdr/".$cdr->getNameIndex(0),
                                        'sunat_note'=>$Note,
                                        'sunat_responsecode'=>$ResponseCode,
                                        'sunat_description'=>$Description
                                    )
                                );
                            }
                        }
                        $cdr->close();
                    } else {
                        throw new ConfluxException("El sistema recibio un archivo ZIP pero la operacion fue interrumpida al extraer su contenido");
                    }
                }
            }else{
                if(isset($result->faultcode)){
                    throw new ConfluxException(Sunaterrors::getError($result->faultcode),0,null, array('sunat_description'=>Sunaterrors::getError($result->faultcode),'sunat_faultcode'=>$result->faultcode));
                }else{
                    throw new ConfluxException('Error en conexion SOAP a SUNAT',0,null, array('sunat_description'=>'','sunat_faultcode'=>'', 'sunat_soap_error'=>'error'));
                }
            }
        } catch (ConfluxException $e) {
            //echo "errores soap conflux exception";
            //print_r($e->getData());
            return array(
                'status'=>'error',
                'error_message'=>$e->getMessage(),
                'error_data'=>$e->getData()
            );
        } catch(SoapFault $fault){
            return array(
                'status'=>'error',
                'error_message'=>'Ha ocurrido un error en la conexion SOAP con el servidor SUNAT',
                'error_data'=>array(
                    'sunat_faultcode'=>$fault->getMessage(),
                    'sunat_soap_error'=>'error'
                )
            );
            /*echo 'Request : <br/><xmp>',
            $client->__getLastRequest(),
            '</xmp><br/><br/> Error Message : <br/>',
            $fault->getMessage();*/
        }
    }

    public function _sendBill(){
        $data = array();
        if($metodo=='sendBill'||$metodo=='sendSummary'){
            if($comprobante!=null){
                //$model = $f->model('cj/comp')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;
                if(isset($comprobante['id_documento'])){
                    $this->generic->init(array('table'=>'documentos','table_alias'=>'doc','table_pk'=>'id_documento'));
                    $model = $this->generic->get_one(array(
                        "where"=>array('id_documento'=>$comprobante['id_documento'])
                    ));
                    $data['ruta'] = $model->ruta_zip_firmado;
                }
            }elseif($rutas!=null){
                $data['ruta'] = $rutas['ruta_zip_firmado'];
            }
            $filename = explode("/", $data['ruta']);
            $filename = $filename[count($filename)-1];
            $handle = fopen($data['ruta'], "r");
            $contents = fread($hand.le, filesize($data['ruta']));
            $params = array(
                'fileName'=>$filename,
                'contentFile'=>$contents
            );
        }else if($metodo=='getStatus'){
            $filename = $rutas.'.zip';
        }
        
        $options = array(
            //'proxy_host'=> "192.168.1.3",
            //'proxy_port'=> 8080,
            //"soap_version"=>SOAP_1_2,
            "trace" => 1,
            "exceptions" => 0
        );

        /*Autenticacion SOAP*/
        $username = $this->config->item('conflux-ruc').$this->config->item('conflux-sol_usuario');
        $password = $this->config->item('conflux-sol_clave');
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
        try{
            if($metodo=='sendBill'){
                $result = $client->sendBill($params);
                if(isset($result->applicationResponse)){
                    $fichero = IndexPath.DS."see-files/cdr/".$filename;
                    $persona = $result->applicationResponse;
                    //file_put_contents($fichero, $persona, FILE_APPEND | LOCK_EX);
                    if($fh = fopen($fichero,'w')){
                        $stringData = $result->applicationResponse;
                        fwrite($fh, $stringData);
                        fclose($fh);
                        $cdr = new ZipArchive();
                        if ($cdr->open($fichero) === TRUE) {
                            $cdr->extractTo(IndexPath.DS."see-files/cdr/");
                            //print_r($cdr);
                            $doc = new DOMDocument();
                            $doc->load(IndexPath.DS."see-files/cdr/".$cdr->getNameIndex(0));
                            $ReferenceID = $doc->getElementsByTagName("ReferenceID")->item(0)->nodeValue;
                            $ResponseCode = $doc->getElementsByTagName("ResponseCode")->item(0)->nodeValue;
                            $Description = $doc->getElementsByTagName("Description")->item(0)->nodeValue;
                            if(is_numeric($ResponseCode)){
                                if(floatval($ResponseCode)>0){
                                    $rpta['status']=false;
                                    $rpta['message']=Sunaterrors::getError($ResponseCode);
                                }else{
                                    $rpta['status']=TRUE;
                                    $rpta['message']=$Description;
                                }
                                $rpta['rpta']=array(
                                    'ReferenceID'=>$ReferenceID,
                                    'ResponseCode'=>$ResponseCode,
                                    'cdr_xml'=>"see-files/cdr/".$cdr->getNameIndex(0)
                                );
                            }
                            
                            if(floatval($rpta['rpta']['ResponseCode'])>0){
                                $rpta['message']=Sunaterrors::getError($rpta['rpta']['ResponseCode']);
                            }
                            $cdr->close();
                        } else {
                            $rpta['status'] = false;
                            $rpta['message'] = "El sistema recibio un archivo ZIP pero la operacion fue interrumpida al extraer su contenido";
                            $rpta['rpta'] = array(
                                'cdr_zip'=>"see-files/cdr/".$filename
                            );
                        }
                    }
                }else{
                    if(isset($result->faultcode)){
                        $rpta['status']=false;
                        $rpta['message']='SUNAT ha devuelto un error';
                        $rpta['rpta']=array(
                            'faultstring'=>Sunaterrors::getError($result->faultcode),
                            'faultcode'=>$result->faultcode
                        );
                    }
                }
            }elseif($metodo=='sendSummary'){
                $result = $client->sendSummary($params);
                if(isset($result->applicationResponse)){
                    $fichero = IndexPath.DS."see-files/cdr/".$filename;
                    $persona = $result->applicationResponse;
                    //file_put_contents($fichero, $persona, FILE_APPEND | LOCK_EX);
                    if($fh = fopen($fichero,'w')){
                        $stringData = $result->applicationResponse;
                        fwrite($fh, $stringData);
                        fclose($fh);
                        $rpta['status']=true;
                        $rpta['message']='Se ha enviado el archivo electronico y recibido una constancia de recepcion por parte de SUNAT';
                        $rpta['rpta']=array(
                            'cdr'=>"see-files/cdr/".$filename
                        );
                    }
                }else{
                    if(isset($result->faultcode)){
                        $rpta['status']=false;
                        $rpta['rpta']=array(
                            'faultcode'=>$result->faultcode
                        );
                    }
                }
            }elseif($metodo=='getStatus'){
                $result = $client->getStatus(array(
                    'ticket'=>$rutas
                ));
                if(isset($result->status->content)){
                    $fichero = IndexPath.DS."see-files/cdr/".$filename;
                    $persona = $result->status->content;
                    //file_put_contents($fichero, $persona, FILE_APPEND | LOCK_EX);
                    if($fh = fopen($fichero,'w')){
                        $stringData = $result->status->content;
                        fwrite($fh, $stringData);
                        fclose($fh);
                        $rpta['status']=true;
                        $rpta['message']='Se ha enviado el archivo electronico y recibido una respuesta por parte de SUNAT';
                        $rpta['rpta']=array(
                            'cdr'=>"see-files/cdr/".$filename
                        );
                    }
                }else{
                    $rpta['status']=false;
                    $rpta['rpta']=array(
                        'faultcode'=>$result->faultcode
                    );
                }
            }
            
            //echo 'PRINT_R: $RESULT send_sunat()';
            //print_r($result);
            return $rpta;
            
        }catch(SoapFault $fault){
            $rpta['status']=false;
            $rpta['message']='Ha ocurrido un error en la conexion SOAP con el servidor SUNAT';
            $rpta['rpta']=array(
                'soap'=>$fault->getMessage()
            );
            /*echo 'Request : <br/><xmp>',
            $client->__getLastRequest(),
            '</xmp><br/><br/> Error Message : <br/>',
            $fault->getMessage();*/
            return $rpta;
        }
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