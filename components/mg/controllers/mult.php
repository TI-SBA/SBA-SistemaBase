<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'google/vendor/autoload.php';
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Storage\StorageObject;
class Controller_mg_mult extends Controller{
	function execute_index(){
		global $f;
		$f->response->view("mg/mult.main");
	}
	function execute_lista(){
		global $f;
		$data = $f->request->data;
		$f->library('ftp');
		$ftp = new ftp();
		$id_ftp = $ftp->ConectarFTP();
		$empty = false;
		if(isset($data['dir'])){
			if (@ftp_chdir($id_ftp,$data['dir'])) {
				//echo "Current directory is now: " . ftp_pwd($conn_id) . "\n";
			} else {
				if (@ftp_chdir($id_ftp,$data['ruta'])) {
					//echo "Current directory is now: " . ftp_pwd($conn_id) . "\n";
				} else { 
					$empty = true;
				}
			}
		}else{
			$data['dir'] = '/';
		}
		if($empty==false){
			$rpta = array(
				'ruta'=>ftp_pwd($id_ftp),
				'items'=>array()
			);
			$lista=ftp_nlist($id_ftp,'');
			$lista=array_reverse($lista);
			while ($item=array_pop($lista)){
				$tamano=number_format(((ftp_size($id_ftp,$item))/1024),2)." Kb";
				if($tamano=="-0.00 Kb"){
					$tipo = 'F';
					$item=$item;
					$tamano="&nbsp;";
					$fecha="&nbsp;";
				}else{
					$tipo = 'A';
					$fecha=date("Y-m-d h:i:s", ftp_mdtm($id_ftp,$item));
				}
				$rpta['items'][] = array(
					'tipo'=>$tipo,
					'item'=>$item,
					'tamano'=>$tamano,
					'fecha'=>$fecha
				);
			}
		}else{
			$rpta = array(
				'ruta'=>$data['ruta'],
				'items'=>array()
			);
		}
		$f->response->json($rpta);
	}
	function execute_save_folder(){
		global $f;
		$descr = $f->request->data['descr'];
		$f->library('ftp');
		$ftp = new ftp();
		$id_ftp = $ftp->ConectarFTP();
		if(isset($f->request->data['dir'])){
			ftp_chdir($id_ftp,$f->request->data['dir']);
		}
		if (ftp_mkdir($id_ftp, $descr)) {
			$f->response->print("true");
		} else {
			$f->response->print("false");
		}
	}
	function execute_delete_folder(){
		global $f;
		$f->library('ftp');
		$ftp = new ftp();
		$id_ftp = $ftp->ConectarFTP();
		$rpta = $ftp->ftpDeleteDirectory($id_ftp,$f->request->data['dir']);
		$f->response->print($rpta);
	}
	
	
	
	
	
	
	
	
	function execute_upload(){
		global $f;
		set_time_limit(0);
		$f->library('ftp');
		$ftp = new ftp();
		$rpta = $ftp->uploadFile('tmp/'.$f->request->data["file"],$f->request->data['dir'],$f->request->data["file"]);
		
		unlink(IndexPath.'/tmp/'.$f->request->data['file']);
		$f->response->print($rpta);
	}
	function execute_upload_module(){
		global $f;

		putenv('GOOGLE_APPLICATION_CREDENTIALS='.__DIR__.'/SBPA-8ff2e20bf066.json');

		$client = new Google_client();
		$client->useApplicationDefaultCredentials();
		$projectId = 'sbpa-153705';
		$bucketName = 'archivo-central-storage';
		$client->addScope(Google_Service_Storage::DEVSTORAGE_FULL_CONTROL);
		$Storage = new Google_Service_Storage($client);
		//RUTA DE ARCHIVO EN LOCAL
		//$fileTmpName = "";
		$fileTmpName = $f->request->data['nomb'];
		//NOMBRE DE ARCHIVO SUBIDO
		//$file_name = "";
		$file_name = $f->request->data['file'];
		$obj = new Google_Service_Storage_StorageObject();
		$obj->setName($file_name);
		$insert = $storage->objects->insert(
			"archivo-central-storage",
			$obj,
			array('name'=> $file_name,'data'=> file_get_contents($fileTmpName),
				'uploadFile' => 'media')
			);


		//////////////////////////////////////
/*
		set_time_limit(0);
		$f->library('ftp');
		$ftp = new ftp();
		
		$id_ftp = $ftp->ConectarFTP();
		//ARCHIVO
		$ext = substr($f->request->data['file'], strrpos($f->request->data['file'], '.'));
		//RUTA
		ftp_chdir($id_ftp, $f->request->data['dir']);
		$ftp->ftpDeleteDirectory($id_ftp,$f->request->data["nomb"].$ext);
		//CREA LA CARPETA TEMPORAL + ARCHIVO + LA RUTA + ARCHIVO.
		$rpta = $ftp->uploadFile('tmp/'.$f->request->data["file"],$f->request->data['dir'],$f->request->data["file"]);
		
		unlink(IndexPath.'/tmp/'.$f->request->data['file']);
		
		
		ftp_chdir($id_ftp, $f->request->data['dir']);
		ftp_rename($id_ftp, $f->request->data["file"], $f->request->data["nomb"].$ext);
		$f->response->json(array(
			'ext'=>$ext
		));
	*/
	}
	
	function execute_upload_module_pdf(){
		global $f;
		set_time_limit(0);
		$f->response->view('mg/mult.pdf',array('data'=>$f->request->data['files']));
		
		$f->library('ftp');
		$ftp = new ftp();
		$rpta = $ftp->uploadFile('tmp/file.pdf',$f->request->data['dir'],'file.pdf');
		
		unlink(IndexPath.'/tmp/file.pdf');
		foreach ($f->request->data['files'] as $img) {
			unlink(IndexPath.'/tmp/'.$img);
		}
		
		$id_ftp = $ftp->ConectarFTP();
		// open the folder that have the file
		ftp_chdir($id_ftp, $f->request->data['dir']);
		// rename the file
		ftp_rename($id_ftp, 'file.pdf', $f->request->data["nomb"].'.pdf');
		
		$f->response->json(array(
			'ext'=>'pdf'
		));
	}
	
	
	
	
	
	
	
	
	function execute_update_folder(){
		global $f;
		$data = $f->request->data;
		$data["_id"] = new MongoId($data["_id"]);
		$f->model("mg/dire")->params($data)->save("data");
		$f->response->print("true");
	}
	
	
	
	
	
	
	function execute_copy(){
		global $f;
		/*$f->library('ftp');
		$ftp = new ftp();
		$ftp->ftpCopy();*/
		$word = new COM("Word.Application") or die ("Could not initialise Object.");
  // set it to 1 to see the MS Word window (the actual opening of the document)
  $word->Visible = 0;
  // recommend to set to 0, disables alerts like "Do you want MS Word to be the default .. etc"
  $word->DisplayAlerts = 0;
  // open the word 2007-2013 document 
  $word->Documents->Open('D:\www\beneficencia\informe.docx');
  // save it as word 2003
  $word->ActiveDocument->SaveAs('D:\www\beneficencia\newdocument.doc');
  // convert word 2007-2013 to PDF
  $word->ActiveDocument->ExportAsFixedFormat('D:\www\beneficencia\yourdocument.pdf', 17, false, 0, 0, 0, 0, 7, true, true, 2, true, true, false);
  // quit the Word process
  $word->Quit(false);
  // clean up
  unset($word);
	}
	
	
	
	
	
	
	
	
	function execute_prev(){
		global $f;
		$f->response->view("mg/mult.prev");
	}
	function execute_new(){
		global $f;
		$f->response->view("mg/mult.new");
	}
	function execute_new_file(){
		global $f;
		$f->response->view("mg/mult.new.file");
	}
	function execute_new_folder(){
		global $f;
		$f->response->view("mg/mult.folder");
	}
	function execute_print_img(){
		global $f;
		$f->response->print('<img src="'.$f->request->data['img'].'" style="width:100%" />');
	}
	function execute_listar_bucket(){
		global $f;
		$client = new Google_Client();
		$client->setAuthConfig(IndexPath.DS.'/google/SBPA-8ff2e20bf066.json');
		$projectId = 'sbpa-153705';
		$bucketName = 'archivo-central-storage';
		$client->addScope(Google_Service_Storage::DEVSTORAGE_FULL_CONTROL);
		$storage = new Google_Service_Storage($client);
		$listObjects = $storage->objects->listObjects('archivo-central-storage', array());
		//$listObjects = $storage->objects->get('archivo-central-storage', 'cementerio/2012/01', array());
		$items = $listObjects->getItems();
		foreach ($items as $item) {
			echo $item["name"].'<br />';
		}
	}
	function execute_subir(){
		global $f;
		$target_path = "tmp/";
		$ruta = "";
		if(isset($f->request->data['folder'])){
			if($f->request->data['folder']!=''&&$f->request->data['folder']!='undefined'){
				$ruta = $f->request->data['folder'];
			}
		}
		$file_name_pre = $_FILES['file_upload']['name'];
		if(isset($_FILES['file_upload'])){
			$target_path = $target_path . basename( $_FILES['file_upload']['name']); 
			if(move_uploaded_file($_FILES['file_upload']['tmp_name'], $target_path)) {
				$ext = pathinfo($target_path, PATHINFO_EXTENSION);
				$mime = mime_content_type($target_path);
				if(isset($f->request->data['nomb'])){
					if($f->request->data['nomb']!=''&&$f->request->data['nomb']!='undefined'){
						$file_name_pre = $f->request->data['nomb'].'.'.$ext;
					}
				}
				$output = array();
				//putenv('GOOGLE_APPLICATION_CREDENTIALS='.IndexPath.DS.'/google/SBPA-8ff2e20bf066.json');
				$client = new Google_Client();
				$client->setAuthConfig(IndexPath.DS.'/google/SBPA-8ff2e20bf066.json');
				$projectId = 'sbpa-153705';
				$bucketName = 'archivo-central-storage';
				$client->addScope(Google_Service_Storage::DEVSTORAGE_FULL_CONTROL);
				$storage = new Google_Service_Storage($client);
				$fileTmpName = $target_path;
				$file_name = $ruta.$file_name_pre;
				$obj = new Google_Service_Storage_StorageObject();
				$obj->setName($file_name);
				$insert = $storage->objects->insert(
					"archivo-central-storage",
					$obj,
					array(
						'name' => $file_name,
						'data' => file_get_contents($fileTmpName),
						'uploadType' => 'media',
						'mimeType'      => $mime,
						'predefinedAcl' => 'publicRead'
					)
				);
				$output =$insert;
			}else{
				$output = array('error'=>'Se ha producido un error al subir el archivo. Intentalo de nuevo. 1!');
			}
		}else{
			$output = array('error'=>'Se ha producido un error al subir el archivo. Intentalo de nuevo. 2!');
		}
		$f->response->json($output);
	}
}
?>