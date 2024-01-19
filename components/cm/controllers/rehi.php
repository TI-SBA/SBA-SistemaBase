<?php
error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once 'google/vendor/autoload.php';
	use Google\Cloud\Storage\StorageClient;
	use Google\Cloud\Storage\StorageObject;

class Controller_cm_rehi extends Controller {
	
	function execute_save_digi() {
		global $f;
		$data = $f->request->data;
		$hist = $f->model("cm/hope")->params(array("_id" => new MongoId($f->request->data['_id'])))->get("one")->items;
		if(!isset($hist['url_imagen'])){
			$hist['url_imagen'] = '';
			$hist['url_imagen'] = $data['url_imagen'];
			
		}
		$model = $f->model("cm/hope")->params(array('_id' => new MongoId($f->request->data['_id']), 'data' => $hist))->save("update")->items;
		$f->response->json($model);
	}
	function execute_subir(){
		global $f;
		//echo IndexPath.DS;
		$target_path = "tmp/";
		if(isset($_FILES['file_upload'])){
			$target_path = $target_path . basename( $_FILES['file_upload']['name']); 
			if(move_uploaded_file($_FILES['file_upload']['tmp_name'], $target_path)) {
				$output = array();
				$client = new Google_Client();
				$client->setAuthConfig(IndexPath.DS.'/google/SBPA-8ff2e20bf066.json');
				$projectId = 'sbpa-153705';
				$bucketName = 'cementerio-storage';
				$client->addScope(Google_Service_Storage::DEVSTORAGE_FULL_CONTROL);
				$storage = new Google_Service_Storage($client);
				$fileTmpName = $target_path;
				$file_name = $_FILES['file_upload']['name'];
				$obj = new Google_Service_Storage_StorageObject();
				$obj->setName($file_name);
				$insert = $storage->objects->insert("cementerio-storage",$obj,array(
						'name' => $file_name,
						'data' => file_get_contents($fileTmpName),
						'uploadType' => 'media',
						'predefinedAcl' => 'publicRead'
					)
				);
				$output =$insert;
				/************DESCARGA*********/
				$descarga = $storage->objects->get($bucketName,$file_name);
				
				$descarga->mediaLink;

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
		$f->response->view('cm/oper.dig.edit');
	}
	function execute_view(){
		global $f;
		$f->response->view('cm/img.view');
	}
	function execute_search(){
		global $f;
		$params = array(
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		);
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		if(isset($f->request->data['tipo']))
			$params['tipo'] = $f->request->data['tipo'];
		if(isset($f->request->data['sector']))
			$params['sector'] = $f->request->data['sector'];
		if(isset($f->request->data['fila']))
			$params['fila'] = $f->request->data['fila'];
		if(isset($f->request->data['piso']))
			$params['piso'] = $f->request->data['piso'];
		if(isset($f->request->data['num']))
			$params['num'] = $f->request->data['num'];
		if(isset($f->request->data['filter'])){
			$params['filter'] = $f->request->data['filter'];
			foreach ($params['filter'] as $i=>$filter){
				if(gettype($filter['value'])=="array"){
					if(isset($filter['value']['$exists'])){
						if($filter['value']['$exists']=='true') $filter['value'] = array('$exists'=>1);
						else $filter['value'] = array('$exists'=>0);
					}
				}
				$params['filter'][$i]['value'] = $filter['value'];
			}
		}
		$model = $f->model("cm/espa")->params($params)->get("search");
		/*foreach($model->items as $i=>$item){
			$hist = $f->model('cm/hope')->params(array('espacio'=>$item['_id']))->get('all')->items;
			if(sizeof($hist)>0)
				$model->items[$i]['historia'] = true;
			else
				$model->items[$i]['historia'] = false;
		}*/
		$f->response->json( $model );
	}
	function execute_get(){
		global $f;
		$model = $f->model("cm/espa")->params(array("_id"=>new MongoId($f->request->_id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_arreglando(){
		global $f;
		$model = $f->model('cm/hope')->get('arreglar')->items;
		if($model != null){
			foreach($model as $i => $item){
				$url_imagen = $item['url_imagen'];
				$pos = strpos($url_imagen,'?');
				if($pos !== false){
					$url_imagen = strstr($url_imagen,'?',true);
				}
				$item['url_imagen'] = $url_imagen; // Actualizar el campo "url_imagen" en el registro
				$set = array(
					'url_imagen'=>$item['url_imagen']
				);
				$f->model('cm/hope')->params(array('_id'=>$item['_id'],'data'=>$set))->save('update');
				echo 'ACTUALIZADO '. $item['_id'];

				
			}
			
		}
		
	}
}
?>