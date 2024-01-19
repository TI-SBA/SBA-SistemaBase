<?php
class Model_ci_archivos extends Model {
	/**
	 * @var Archivos
	 */
	public $archivos;
	
	public function __construct() {
		global $f;
		$this->gridFS = $f->datastore->getGridFS();
	}
	
	protected function get_one() {
		global $f;
		$files = $f->datastore->selectCollection("fs.files");
		$this->items = $files->findOne( array('_id'=>$this->params['_id']) );
	}
	protected function get_oper() {
		global $f;
		$files = $f->datastore->selectCollection("fs.files");
		$this->items = $files->findOne( array('operacion'=>$this->params['oper']) );
	}
	protected function get_archivo() {
		global $f;
		$archivo = new File($this->params['id']);
		$this->archivos = $archivo;
	}
	
	protected function get_data() {
		global $f;
		$this->archivos = $this->gridFS->findOne( array('_id'=>new MongoId($this->params['id'])) );
	}
	
	protected function get_metadata() {
		global $f;
		/*$archivo = new Archivo();
		$archivo->id = $this->params['id'];
		$archivo->load_metadata();
		$this->archivos = $archivo;*/
		$files = $f->datastore->selectCollection("fs.files");
		$this->archivos = $files->findOne( array('_id'=>new MongoId($this->params['id'])) );
	}
	
	protected function save_archivo(){
		global $f;
		$filename = $this->params['path'];
		$file = new File($this->params['path']);
		$metadata = array(
			"filename" => $this->params['file'],
			"bytes" => $file->size,
			"ext" => $file->ext,
			"mime" => $file->mime
		);
		$metadata = array("metadata" => $metadata,"filename" => $this->params['file']);
		if($file->ext=='jpg' || $file->ext=='png' || $file->ext=='bmp' || $file->ext=='gif' || $file->ext=='JPG' || $file->ext=='PNG' || $file->ext=='BMP' || $file->ext=='GIF'){
			$size = getimagesize($this->params['path']);
			$metadata['metadata']['width'] = $size[0];
			$metadata['metadata']['height'] = $size[1];
			$metadata['metadata']['mime'] = $size['mime'];
		}
		$storedfile = $this->gridFS->storeFile($filename,$metadata);
        $this->archivos = $storedfile;
        $file->delete();
	}
	protected function save_extra(){
		global $f;
		$array = array( '$set'=>$this->params['data'] );
		$files = $f->datastore->selectCollection("fs.files");
		$files->update(array( '_id' => new MongoId($this->params['_id']) ),$array);
	}
	protected function get_lista_ini() {
		global $f;
		$tmp = $f->datastore->mg_directorios->findOne(array('descr'=>'/'));
		$this->params['dir'] = $tmp['_id'];
		$files = $f->datastore->selectCollection("fs.files");
		$data = $files->find( array('metadata.dir'=>$this->params['dir']) );
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_lista() {
		global $f;
		$files = $f->datastore->selectCollection("fs.files");
		$data = $files->find( array('metadata.dir'=>$this->params['dir']) );
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_search(){
		global $f;
		$files = $f->datastore->selectCollection("fs.files");
		if($f->request->data["bus"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $f->request->data["bus"];
			$criteria = $helper->paramsSearch($f->request->data["bus"], array('metadata.nomb','metadata.filename','ext'));
		}else $criteria = array();
		$cursor = $files->find($criteria)->sort( array('metadata.nomb'=>1) );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
	}
	protected function delete_item(){
		global $f;
		$this->gridFS->remove(array('_id'=>$this->params['_id']));
	}
	protected function save_nomb(){
		global $f;
		if(!isset($this->params['dir'])){
			$tmp = $f->datastore->mg_directorios->findOne(array('descr'=>'/'));
			$this->params['dir'] = $tmp['_id'];
		}
		$files = $f->datastore->selectCollection("fs.files");
		if(!isset($this->params['nomb'])){
			$tmp = $files->findOne(array('_id'=>$this->params['_id']));
			$this->params['nomb'] = $tmp['filename'];
		}
		$array = array( '$set'=>array(
			'metadata.nomb'=>$this->params['nomb'],
			'metadata.dir'=>$this->params['dir']
		) );
		$files->update(array( '_id' =>$this->params['_id'] ),$array);
	}
}
?>