<?php
class Model_ho_hosp extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->ho_hospitalizaciones;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
	}

	protected function get_one_entidad(){
		global $f;
		$this->items = $this->db->findOne(array('paciente._id'=>$this->params['_id']));
	}

	protected function get_lista(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$parametro_n = $this->params["texto"];
				$criteria = $helper->paramsSearch(floatval($this->params["texto"]), array('hist_cli'));
				$criteria = array('hist_cli'=>$this->params["texto"]);
			}
		}
		if(isset($this->params['modulo']))
			$criteria['modulo'] = $this->params['modulo'];
		if(isset($this->params['estado']))
			$criteria['estado'] = $this->params['estado'];
		if(isset($this->params['pend'])){
			/*$criteria['$or'] = array(
				array('recibo'=>array('$exists'=>false)),
				array('recibo.fecfin'=>array('$lt'=>new MongoDate()))
			);*/
          	//$criteria['recibo'] = array('$exists'=>false);
            //$criteria['recibo.fecfin'] = array('$lt'=>new MongoDate());
			//$criteria['recibo.fecfin'] = array('$lt'=>new MongoDate());
		}
		if(isset($this->params['alta'])){
			$criteria['recibo.fecfin'] = new MongoDate(strtotime(date('Y-m-d')));
		}
		$sort = array('fecreg'=>-1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->db->find($criteria)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	
	protected function get_recidia(){
		global $f;
		$filter = array();
		if(isset($this->params)) $filter = $this->params;
		$data = $this->db->find($filter, array('paciente'=>1,'categoria'=>1,'num'=>1,'modalidad'=>1,'recibo'=>1,'recibos'=>1,'importe'=>1,'fecpag'=>1));
		foreach ($data as $obj) {
			$this->items[] = $obj;
		}
	}
	protected function get_hospi_dia(){
		global $f;
		$filter = array();
		if(isset($this->params)) $filter = $this->params;
		$data = $this->db->find($filter, array('estado'=>1,'hist_cli'=>1,'paciente'=>1,'pabe'=>1,'tipo_hosp'=>1,'categoria'=>1,'fecpag'=>1));
		foreach ($data as $obj) {
			$this->items[] = $obj;
		}	
	}
		protected function get_hospi(){
		global $f;
		$filter = array();
		if(isset($this->params)) $filter = $this->params;
		$data = $this->db->find($filter, array('paciente'=>1,'categoria'=>1,'ning'=>1));
		foreach ($data as $obj) {
			$this->items[] = $obj;
		}
	}
	
	protected function get_all(){
		global $f;
		$fields = array();
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		$filter = array();
		if(isset($this->params['ini'])){
			$filter = array(
				'$or'=>array(
					array('fecini'=>array(
						'$gte'=>new MongoDate(strtotime($this->params['ini'])),
						'$lte'=>new MongoDate(strtotime($this->params['fin']))
					)),
					array('fecfin'=>array(
						'$gte'=>new MongoDate(strtotime($this->params['ini'])),
						'$lte'=>new MongoDate(strtotime($this->params['fin']))
					))
				)
			);
		}
		if(isset($this->params['altas'])){
			if($this->params['altas']==true)
				$filter['fecalt'] = array('$exists'=>true);
			else
				$filter['fecalt'] = array('$exists'=>false);
		}
		if(isset($this->params['tipo_hosp'])){
			$filter['tipo_hosp'] = $this->params['tipo_hosp'];
		}
		$data = $this->db->find($filter,$fields);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}

	protected function get_all_by_histcli(){
		global $f;
		$fields = array();
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		$filter = array();
		if(isset($this->params['histcli']))
		{
			$filter['hist_cli']=$this->params['histcli'];
		}
		$data = $this->db->find($filter,$fields);
		$index=0;
		foreach ($data as $obj) {
				$this->items[] = $obj;
				$idx=0;
				if(isset($obj["recibos"])){
				foreach($obj["recibos"] as $recibos){
					$rcb=$f->model("cj/comp")->params(array("filter"=>array("_id"=>new MongoId($recibos["_id"])),"fields"=>array("total"=>true,"moneda"=>true)))->get("one_custom")->items;
					$this->items[$index]["recibos"][$idx]["total"]=$rcb["total"];
					$rcb=array();
					$idx++;
					}
				}	
				$index++;
		}
	}
	protected function get_hist_cli_string(){
		$data = $this->db->find(array('hist_cli'=>array('$type'=>2)));
		foreach ($data as $obj) {
			$this->items[] = $obj;
		}	
	}

	protected function save_insert(){
		global $f;
		$this->db->insert( $this->params['data'] );
		$this->items = $this->params['data'];
	}
	protected function save_update(){
		global $f;
		$this->params['data']['_id'];
		$this->db->update( array('_id'=>$this->params['data']['_id']) , array('$set'=>$this->params['data']) );
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']));
		/*print_r($this->params);
		print_r('________________');
		print_r($test);
		die();*/
	}
	protected function get_custom_data(){
		global $f;
		$cursor = $this->db->find($this->params['filter'],$this->params['fields'])->sort( $this->params['sort'] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
	}
	protected function save_custom(){
		global $f;
		$this->db->update(array( '_id' => $this->params['_id'] ),$this->params['data']);
	}
}
?>