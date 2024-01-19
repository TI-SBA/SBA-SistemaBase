<?php
class Model_ts_comp extends Model {
	private $comp;
	public $items;
	
	public function __construct() {
		global $f;
		$this->col = $f->datastore->ts_comprobantes;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->col->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_cod(){
		global $f;
		$cursor = $this->col->find(array(),array('cod'=>true))->sort(array('cod'=>-1))->limit(1);
		foreach ($cursor as $ob) {
		    $this->items = $ob['cod'];
		}
	}
	protected function get_lista(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','trabajador.fullname'));
			}
		}
		$sort = array('_id'=>-1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->col->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_all(){
		global $f;
		$filter = array(
			"estado"=>$this->params['estado'],
			"cuenta_banco._id"=>$this->params['cuenta_banco'],
			"tipo_pago"=>$this->params['tipo_pago'],
			'fecreg'=>array(
				'$gte'=>$this->params['desde'],
				'$lt'=>$this->params['hasta']
			)
		);
		$data = $this->col->find($filter);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_insert(){
		global $f;
		$this->col->insert( $this->params['data'] );
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->col->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
	}
	protected function delete_fuen(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->col->remove($this->items);
	}
}
?>