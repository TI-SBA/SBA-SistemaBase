<?php
define('APPPATH',IndexPath.DS);
error_reporting(E_ALL);
ini_set('display_errors', 1);
include APPPATH."/libraries/xmlseclibs/autoload.php";
require APPPATH.'/libraries/Number.php';
require APPPATH.'/libraries/conflux/ConfluxSee.php';
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;
use RobRichards\XMLSecLibs\XMLSecEnc;
class Controller_ct_fisc extends Controller {
    function execute_lista(){
        global $f;
        $params = array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows);
        if(isset($f->request->data['tipo']))
            $params['tipo'] = $f->request->data['tipo'];
        if(isset($f->request->data['estado'])){
            $params['estado'] = $f->request->data['estado'];
        }
        if(isset($f->request->data['texto'])){
            if($f->request->data['texto']!='')
                $params['texto'] = $f->request->data['texto'];
        }
        if(isset($f->request->data['caja'])){
            $params['caja'] = new MongoId($f->request->data['caja']);
        }
        $model = $f->model("cj/ecom")->params($params)->get("lista");
        $f->response->json( $model );
    }
    function execute_lista_badcalc(){
        global $f;
        $params = array(
            'filter' => array(
                "items.conceptos"=> array('$exists' => true)
            ),
            'fields' => array(
                'tipo' => 1, 
                'serie' => 1, 
                'numero' => 1, 
                'estado' => 1, 
                'fecemi' => 1, 
                //'items' => 1, 
                'total' => 1, 
                "total_isc"=> 1, 
		    	"total_igv"=> 1, 
   				"total_otros_tributos"=> 1, 
   				"total_otros_cargos" => 1, 
   				"total_ope_inafectas"=> 1,
   				"total_ope_gravadas" => 1, 
   				"total_desc" => 1, 
   				"total_ope_exoneradas" => 1, 
   				"total_ope_gratuitas" => 1, 
            )
        );
        $model = $f->model("cj/ecom")->params($params)->get("all")->items;
        $cuenta_total=count($model);
        $monto_erroneo=0;
        $monto_real_erroneo=0;
        $ecom_error=[];
        foreach ($model as $k => $ecom) {
        	$ecom['total_suma']= $ecom['total_isc'] + 
					$ecom['total_igv'] + 
					$ecom['total_otros_tributos'] +
					$ecom['total_otros_cargos'] + 
					$ecom['total_ope_inafectas'] + 
					$ecom['total_ope_gravadas'] +
					$ecom['total_desc'] +
					$ecom['total_ope_exoneradas'] + 
					$ecom['total_ope_gratuitas'];
			$a=$ecom['total'];
			$b=$ecom['total_suma'];
			$epsilon=0.00001;
        	if(abs($a-$b) < $epsilon){
        		unset($model[$k]);
        	}else{
        		$model[$k]['total_suma']=$ecom['total_suma'];
        		$model[$k]['fecemi']=date('Y-m-d H:i:s', $ecom['fecemi']->sec);;
        		$monto_erroneo+=abs($a-$b);
        		$monto_real_erroneo+=($ecom['estado']!='X')?abs($a-$b):0;
        		$ecom_error[$ecom['serie']][$ecom['tipo']][]=$model[$k];
        	}
        }
        $resumen = array(
        	'cuenta' => count($model), 
        	'porcentaje_erroneos' => count($model)/$cuenta_total*100,
        	'monto_erroneo' => $monto_erroneo,
        	'monto_real_erroneo' => $monto_real_erroneo,
        	'comprobantes_erroneos' => $ecom_error,  
        );
        header("Content-type:application/json");
        echo json_encode( $resumen );
        //var_dump($model);
    }
    function execute_get(){
        global $f;
        $model = $f->model('cj/ecom')->params(array('_id'=>new MongoId($f->request->data['_id'])))->get('one')->items;
        if(isset($f->request->data['forma'])){
            #ENVIAR CUENTAS BANCARIAS PARA MODIFICA PAGOS
            $model['ctban'] = $f->model("ts/ctban")->get("all")->items;
        }
        $f->response->json( $model );
    }
    public function execute_verificacion_estado(){
        /**
        *
        */
        global $f;
        $data = $f->request->data;

        # PARAMETROS
        $direccion_ip = "35.193.115.148";
        //$direccion_ip = "http://dc4b60f2.ngrok.io";
        $url_verificacion = "index.php/api/documents/invoice/format/json/";

        $ConfluxSee = new ConfluxSee();
        $response = array(
            'status'=>'error',
            'message'=>'',
            'data'=>array(),
        );

        if(isset($data['_id'])){
            $document = $f->model('cj/ecom')->params(array('_id'=>new MongoId($data['_id'])))->get('one')->items;

            if($document['tipo_comprobante']=='ELECTRONICO'){
                if(isset($document['conflux_see_id'])){
                    $curl_handle = curl_init();
                    curl_setopt($curl_handle, CURLOPT_URL, $direccion_ip."/".$url_verificacion."?_id=".$document['conflux_see_id']);
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                    //curl_setopt($curl_handle, CURLOPT_POST, 1);
                    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($curl_handle, CURLOPT_HTTPHEADER, array(
                        'X-CONFLUX-API-KEY: 1f3870be274f6c49b3e31a0c6728957f'
                    ));

                    //curl_setopt($curl_handle, CURLOPT_POSTFIELDS,"_id=".$document['conflux_see_id']);

                    $buffer = curl_exec($curl_handle);

                    if($buffer===false){
                        curl_close($curl_handle);
                        $response['status'] = 'error';
                        $response['message'] = 'El servidor no respondio a la solicitud enviada!';
                    }else{
                        curl_close($curl_handle);
                        $result = json_decode($buffer,true);
                        if($result === null && json_last_error() !== JSON_ERROR_NONE) {
                            $response['status'] = 'error';
                            $response['message'] = 'El servidor dio un error inesperado que no puede ser parseado!';
                            $response['data'] = $buffer;
                        }else{
                            if (isset($result['status']) && isset($result['rpta'])){

                                // $update_documento['estado']='X';
                                // $update_documento['fecanu']=new MongoDate();
                                // $update_documento['autor_anu']=$f->session->userDB;

                                if(isset($result['rpta']['sunat_note'])) $update_documento['sunat_note']=$result['rpta']['sunat_note'];
                                if(isset($result['rpta']['sunat_soap_error'])) $update_documento['sunat_soap_error']=$result['rpta']['sunat_soap_error'];
                                if(isset($result['rpta']['ruta_xml_firmado'])) $update_documento['ruta_xml_firmado']=$result['rpta']['ruta_xml_firmado'];
                                if(isset($result['rpta']['ruta_cdr_xml'])) $update_documento['ruta_cdr_xml']=$result['rpta']['ruta_cdr_xml'];
                                if(isset($result['rpta']['estado'])) $update_documento['estado']=$result['rpta']['estado'];
                                if(isset($result['rpta']['tipo'])) $update_documento['tipo']=$result['rpta']['tipo'];
                                if(isset($result['rpta']['sunat_faultcode'])) $update_documento['sunat_faultcode']=$result['rpta']['sunat_faultcode'];
                                if(isset($result['rpta']['sunat_description'])) $update_documento['sunat_description']=$result['rpta']['sunat_description'];
                                if(isset($result['rpta']['estado_resumen'])) $update_documento['estado_resumen']=$result['rpta']['estado_resumen'];
                                if(isset($result['rpta']['sunat_responsecode'])) $update_documento['sunat_responsecode']=$result['rpta']['sunat_responsecode'];

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
                            $response = $result;
                        }
                    }
                }
            }else{
                $response['status'] = 'error';
                $response['message'] = "El documento no es un comprobane de crédito electrónico";
            }
        }else{
            $response['status'] = 'error';
            $response['message'] = "El identificador del comprobante electronica a firmar y enviar es obligatorio";
        }
        $f->response->json($response);
    }
}
?>