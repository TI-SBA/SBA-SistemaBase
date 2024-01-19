<?php
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
		require_once 'google/vendor/autoload.php';
		use Google\Cloud\Storage\StorageClient;
		use Google\Cloud\Storage\StorageObject;


class Controller_dd_form extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("dd/form")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("dd/form")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_tmp() {
		global $f;
		$target_path = "tmp/";

		$target_path = $target_path . basename( $_FILES['file_upload']['name']); 
		if(move_uploaded_file($_FILES['file_upload']['tmp_name'], $target_path)) {
		    $output = array();
		    $output['rpta'] = "The file ".  basename( $_FILES['file_upload']['name'])." has been uploaded";
		} else{
			$output = array('error'=>'There was an error uploading the file, please try again!');
		}
		$f->response->json(json_encode($output));
	}
	function execute_get_reporte(){
		global $f;
		$data = $f->request->data;
		$params = array();
		$items = $f->model("dd/form")->params($params)->get("reporte")->items;
		$f->response->view("dd/form.report.php",array('form'=>$items));
		//print_r($items);
		//die();
	}
	function execute_subir(){
		global $f;
		//echo IndexPath.DS;
		$target_path = "tmp/";
		if(isset($_FILES['file_upload'])){
			$target_path = $target_path . basename( $_FILES['file_upload']['name']); 
			if(move_uploaded_file($_FILES['file_upload']['tmp_name'], $target_path)) {
				$output = array();
				//putenv('GOOGLE_APPLICATION_CREDENTIALS='.IndexPath.DS.'/google/SBPA-8ff2e20bf066.json');
				$client = new Google_Client();
				$client->setAuthConfig(IndexPath.DS.'/google/SBPA-8ff2e20bf066.json');
				$projectId = 'sbpa-153705';
				$bucketName = 'archivo-central-storage';
				$client->addScope(Google_Service_Storage::DEVSTORAGE_FULL_CONTROL);
				$storage = new Google_Service_Storage($client);
				$fileTmpName = $target_path;
				$file_name = $_FILES['file_upload']['name'];
				$obj = new Google_Service_Storage_StorageObject();
				$obj->setName($file_name);
				$insert = $storage->objects->insert("archivo-central-storage",$obj,array('name' => $file_name,					'data' => file_get_contents($fileTmpName),
						'uploadType' => 'media',
						'predefinedAcl' => 'publicRead'
					)
				);
				$output =$insert;
				/************DESCARGA*********/
				$descarga = $storage->objects->get($bucketName,$file_name);
				
				//$descarga->mediaLink;

				/****************************/
			}else{
				$output = array('error'=>'Se ha producido un error al subir el archivo. Intentalo de nuevo. 1!');
			}
		}else{
			$output = array('error'=>'Se ha producido un error al subir el archivo. Intentalo de nuevo. 2!');
		}
		$f->response->json($output);
	}
	function execute_upload(){
		global $f;
		$f->response->view('dd/form.upload');
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		if(isset($data['cuenta']))
			$data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDBMin;
			$data['estado'] = 'H';
			$model = $f->model("dd/form")->params(array('data'=>$data))->save("insert")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Tipo de Local',
				'descr'=>'Se creó el Tipo de Local <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("dd/form")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'IN',
				'bandeja'=>'Tipo de Local',
				'descr'=>'Se actualizó el Tipo de Local <b>'.$vari['nomb'].'</b>.'
			))->save('insert');
			$model = $f->model("dd/form")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update")->items;
		}
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$f->response->view("dd/form.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("dd/form.details");
	}
	function execute_delete(){
		global $f;
		$f->model('dd/form')->params(array('_id'=>new MongoId($f->request->data['_id'])))->delete('form');
		$f->response->print("true");
	}
}
?>