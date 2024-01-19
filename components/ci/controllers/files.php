<?php
error_reporting(E_ALL);
	ini_set('display_errors', 1);
	require_once 'google/vendor/autoload.php';
	use Google\Cloud\Storage\StorageClient;
	use Google\Cloud\Storage\StorageObject;

class Controller_ci_files extends Controller {
	function execute_index(){
		global $f;
		$f->response->view("ci/ftp");
	}
	function execute_upload() {
		global $f;
		$f->library("upload");
				
		// list of valid extensions, ex. array("jpeg", "xml", "bmp")
		$allowedExtensions = array();
		// max file size in bytes
		$sizeLimit = 10 * 1024 * 1024;
		
		$uploader = new Upload($allowedExtensions, $sizeLimit);
		$result = $uploader->handleUpload(IndexPath.'/tmp/',$f->request->root.'tmp/');
		print_r($result);
		
		/*$archivo = $f->model('ci/archivos')->params(array('path'=>$result['path'],'file'=>$result['file'],'ext'=>$result['ext']))->save('archivo')->archivos;
		$result['id'] = $archivo;*/
		$f->response->json($result);
	}
	function execute_upload_digi() {
		global $f;
		$f->library("upload");
		// list of valid extensions, ex. array("jpeg", "xml", "bmp")
		$allowedExtensions = array();
		// max file size in bytes
		$sizeLimit = 10 * 1024 * 1024;
		$uploader = new Upload($allowedExtensions, $sizeLimit);
		//$target_path = IndexPath.'/tmp/';
		
		$result = $uploader->handleUpload(IndexPath.'/tmp/',$f->request->root.'tmp/');
		$target_path = "tmp/";
		
		//if(isset($_FILES['file_upload'])){
			//$target_path = $target_path . basename( $_FILES['file_upload']['name']); 
			//if(move_uploaded_file($_FILES['file_upload']['tmp_name'], $target_path)) {
				$output = array();
				$client = new Google_Client();
				$client->setAuthConfig(IndexPath.DS.'/google/SBPA-8ff2e20bf066.json');
				$projectId = 'sbpa-153705';
				$bucketName = 'cementerio-storage';
				$client->addScope(Google_Service_Storage::DEVSTORAGE_FULL_CONTROL);
				$storage = new Google_Service_Storage($client);
				$fileTmpName = $result['url'];
				$file_name = $result['file'];
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
				$descarga = $storage->objects->get($bucketName,$file_name);
				
				$link=$descarga->mediaLink;

				
			/*}else{
				$output = array('error'=>'Se ha producido un error al subir el archivo. Intentalo de nuevo. 1!');
			}*/
		//}
		
		
				
		$f->response->json_encode($output);
	}
	function execute_get(){
		global $f;
		$archivo = $f->model('ci/archivos')->params(array('id'=>$f->request->id))->get('data')->archivos;
		$mime = "";
		if(isset($archivo->file['metadata'])) $mime = $archivo->file['metadata']['mime'];
		$f->response->file( $mime , $archivo->getBytes() );
	}
	
	function execute_metadata() {
		global $f;
		$archivo = $f->model('ci/archivos')->params(array('id'=>$f->request->id))->get('metadata')->archivos;
		$f->response->json( $archivo['metadata'] );
	}
	
	function execute_download(){
		global $f;
		$data = $f->model('ci/archivos')->params(array('_id'=>new MongoId($f->request->data['id'])))->get('one')->items;
		$archivo = $f->model('ci/archivos')->params(array('id'=>$f->request->id))->get('data')->archivos;
		$f->response->download( $data['filename'] , $data['metadata']['mime'], $archivo->getBytes() );
	}
}
?>