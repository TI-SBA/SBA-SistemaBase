<?php
class Model_ts_reca extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->ts_recibos_cajas;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$criteria = array(
					'$or'=>array(
						array('autor.appat'=>new MongoRegex('/'.$parametro.'/i'))
					)
				);
			}
		}
		if(isset($this->params['programa']))
			$criteria['programa'] = $this->params['programa'];
		if(isset($this->params['_id']))
		{
			$filter= array(($this->params["_id"]));
		}
		else
			$filter= array();
		$sort = array('_id'=>-1);
		
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->db->find($criteria,$filter)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
			
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_num(){
		global $f;
		if (isset($this->params['filter'])) {
            $filter = $this->params['filter'];
        }
		$data = $this->db->find(array($filter))->sort(array('num'=>-1))->limit(1);
		foreach ($data as $obj) {
		    $this->items = $obj;
		}
	}
	protected function get_lista_(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto'])){
			if($this->params['texto']!=''){
				$f->library('helpers');
				$helper = new helper();
				$parametro = $this->params['texto'];
				$criteria = $helper->paramsSearch($this->params["texto"], array('num','autor.nomb'));
			}
		}
		if(isset($this->params['oficina'])){
			$criteria['cargo.oficina._id'] = $this->params['oficina'];
		}
		$sort = array('_id'=>-1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
			$data = $this->db->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach($data as $obj){
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());	
	}
	protected function get_reporte(){
		global $f;
		$filter = array();
		if(isset($this->params)) $filter = $this->params;
		$data = $this->db->find($filter, array('num'=>1,'fecreg'=>1,'estado'=>1,'monto'=>1,'concepto'=>1,'trabajador'=>1,'tipo'=>1,'autor'=>1,'cargo.oficina.nomb'=>1))->sort(array('num'=>1,'fecreg'=>1,'estado'=>1,'monto'=>1,'concepto'=>1,'trabajador'=>1,'tipo'=>1,'autor'=>1,'cargo.oficina.nomb'=>1));
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_all()
    {
        global $f;
        if (isset($this->params['filter'])) {
            $filter = $this->params['filter'];
        } else {
            $filter = array();
        }
        if (isset($this->params['fields'])) {
            $fields = $this->params['fields'];
        } else {
            $fields = array();
        }
        if (isset($this->params['sort'])) {
            $sort = $this->params['sort'];
        } else {
            $sort = array('_id'=>-1);
        }
        //if(isset($this->params['fecreg'])) $fecreg = $this->params['fecreg'];
        //else $fecreg = array();

        //print_r(date('Y-M-d h:i:s',$filter['fecreg']['$lte']->sec));die();
        $data = $this->db->find($filter, $fields)->sort($sort)->limit(1);
        foreach ($data as $ob) {
            $this->items[] = $ob;
        }
    }
	protected function save_insert(){
		global $f;
		$this->db->insert( $this->params['data'] );
		$this->items = $this->params['data'];
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->db->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}
	protected function save_custom(){
		global $f;
		$this->db->update(array( '_id' => $this->params['_id'] ),$this->params['data']);
	}
	protected function delete_reca(){
		global $f;
		$this->db->remove(array('_id'=>$this->params['_id']));
	}
}
?>