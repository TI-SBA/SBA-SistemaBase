<?php
class Model_mg_tiden extends Model {
	private $tiden;
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$m = new Mongo('localhost');
		$this->db = $m->beneficencia;
		$this->tiden = $this->db->td_tipos_identidad;
	}
	protected function get_lista(){
		global $f;
  		$i = 0;
  		if(isset($f->request->data['filter']))
			$criteria = array ( $f->request->data['filter'] => intval($f->request->data['order']));
		else
			$criteria = array ( "_id" => 1);
		$data = $this->tiden->find()->skip( $f->request->page_rows * ($f->request->page-1) )->sort($criteria)->limit( $f->request->page_rows );
		foreach ($data as $ob) {
		    $this->items[$i] = $ob;
		    $i++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function save_datos(){
		global $f;
		$array = (array)$f->request->data;
		if(isset($f->request->data['_id'])){
			$array['_id'] = new MongoId($f->request->data['_id']);
			$this->tiden->update(array( '_id' => new MongoId($f->request->data['_id']) ),$array);
		}else
			$this->tiden->insert($f->request->data);
	}
	protected function delete_datos(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->tiden->remove($this->items);
	}
	protected function get_search(){
		global $f;
		$i = 0;
		$parametro=$f->request->nomb;
		$criteria = array(
    		'nomb' => new MongoRegex('/^'.$parametro.'/i')
  		);
		$data = $this->tiden->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->limit( $f->request->page_rows );
		foreach ($data as $obj) {
		    $this->items[$i] = $obj;
			$i++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
}
?>