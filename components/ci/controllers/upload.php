<?php
class Controller_ci_upload extends Controller {
	function execute_index(){
		global $f;
		$f->response->view("ci/ci.upload");
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
	function execute_in_playas() {
		global $f;
		$target_path = "tmp/";
		if(isset($_FILES['file_upload'])){
			$target_path = $target_path . basename( $_FILES['file_upload']['name']); 
			if(move_uploaded_file($_FILES['file_upload']['tmp_name'], $target_path)) {
			    $output = array();
			} else{
				$output = array('error'=>'There was an error uploading the file, please try again!');
			}
		}else
			$output = array('error'=>'There was an error uploading the file, please try again!');
		$f->response->json(json_encode($output));
	}
	function execute_pe_asistencia() {
		global $f;
		$target_path = "tmp/";
		if(isset($_FILES['file_upload'])){
			$target_path = $target_path . basename( $_FILES['file_upload']['name']); 
			if(move_uploaded_file($_FILES['file_upload']['tmp_name'], $target_path)) {
			    $output = array();
			} else{
				$output = array('error'=>'There was an error uploading the file, please try again!');
			}
		}else
			$output = array('error'=>'There was an error uploading the file, please try again!');
		$f->response->json(json_encode($output));
	}
	//-------------------------GOOGLE-------------------------------\\
	function execute_dd_google() {
		global $f;
		$target_path = "tmp/";
		if(isset($_FILES['file_upload'])){
			$target_path = $target_path . basename( $_FILES['file_upload']['name']); 
			if(move_uploaded_file($_FILES['file_upload']['tmp_name'], $target_path)) {
			    $output = array();
			} else{
				$output = array('error'=>'Se ha producido un error al subir el archivo. Intentalo de nuevo 1.!');
			}
		}else
			$output = array('error'=>'Se ha producido un error al subir el archivo. Intentalo de nuevo 2.!');
		$f->response->json(json_encode($output));
	}
	//--------------------------------------------------------------\\

	function execute_pe_rol_turnos() {
		global $f;
		$target_path = "tmp/";
		if(isset($_FILES['file_upload2'])){
			$target_path = $target_path . basename( $_FILES['file_upload2']['name']); 
			if(move_uploaded_file($_FILES['file_upload2']['tmp_name'], $target_path)) {
			    $output = array();
			} else{
				$output = array('error'=>'There was an error uploading the file, please try again!');
			}
		}else
			$output = array('error'=>'There was an error uploading the file, please try again!');
		$f->response->json(json_encode($output));
	}
	function execute_img() {
		global $f;
		$target_path = "tmp/";
		$target_path = $target_path . basename( $_FILES['file_upload']['name']); 
		if(move_uploaded_file($_FILES['file_upload']['tmp_name'], $target_path)) {
		    $output = array();
		} else{
			$output = array('error'=>'There was an error uploading the file, please try again!');
		}
		$gridFS = $f->datastore->getGridFS();
		$id = $gridFS->storeFile(IndexPath.'/'.$target_path);
		$gridfsFile = $gridFS->get($id);
		
		echo json_encode($id);
		unlink(IndexPath.'/'.$target_path);
		//echo json_encode($gridfsFile->file);
	}
}
?>