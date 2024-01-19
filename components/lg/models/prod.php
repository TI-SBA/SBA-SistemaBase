<?php
class Model_lg_prod extends Model {
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore->lg_productos;
	}
	protected function get_one(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$this->items = $this->db->findOne(array('_id'=>$this->params['_id']),$fields);
	}
	protected function get_old_id(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$this->items = $this->db->findOne(array('old_id'=>$this->params['old_id']),$fields);
	}

	protected function get_nomb(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$this->items = $this->db->findOne(array('nomb'=>$this->params['nomb']),$fields);
	}
	protected function get_generico(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$this->items = $this->db->findOne(array('generico'=>$this->params['generico']),$fields);
	}
	protected function get_oldid(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$this->items = $this->db->findOne(array('oldid'=>$this->params['oldid']),$fields);
	}
	protected function get_oldcod(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$this->items = $this->db->findOne(array('oldcod'=>$this->params['oldcod'],'unidad._id'=>$this->params['unidad']),$fields);
	}
	protected function get_lista(){
		global $f;
		if(isset($this->params['filter'])) $criteria = $this->params['filter'];
		else $criteria = array();
		if(isset($this->params["texto"])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				//$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','trabajador.fullname'));
				$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','trabajador.fullname','generico'));
			}
		}
		if(isset($this->params['cuenta']))
			$criteria['cuenta._id'] = $this->params['cuenta'];
		if(isset($this->params['clasif']))
			$criteria['clasif._id'] = $this->params['clasif'];
		if(isset($this->params['tipo']))
			if($this->params['tipo']!='')
				$criteria['tipo_producto'] = $this->params['tipo'];
		if(isset($this->params['modulo']))
			$criteria['modulo'] = $this->params['modulo'];
		if(isset($this->params['almacen'])) $criteria['stock.almacen._id'] = $this->params['almacen'];
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$sort = array('cod'=>1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->db->find($criteria,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_lista_stock(){
		global $f;
		if(isset($this->params['filter'])) $filter = $this->params['filter'];
		else $filter = array();
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$order = array('cod'=>-1);
		/*$filter = array('$and'=>array(
			array('stock'=>array('$exists'=>true)),
			array('cant'=>array('$gte'=>0))
		));*/
		$data = $this->db->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_search(){
		global $f;
		if($this->params["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["texto"];
			$criteria = $helper->paramsSearch($this->params["texto"], array('nomb','cod','clasif.cod'));
			//$criteria['$or'] = array('cod'=>$this->params["texto"],'nomb'=>$this->params["texto"]);
		//	$criteria['cod'] = array('$or'=>array($criteria['cod'],$this->params["texto"]));
			/*$criteria['nomb'] = $this->params["texto"];*/
		}else $criteria = array();
		/*if(isset($this->params['stock'])){
			$criteria['stock'] = array('$exists'=>true);
			$criteria['cant'] = array('$gte'=>0);
		}*/
		if($this->params['tipo']!='') $criteria['tipo_producto'] = $this->params['tipo'];
		if(isset($this->params['almacen'])) $criteria['stock.almacen._id'] = $this->params['almacen'];
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		//$criteria = array('$or'=>$criteria);
		$cursor = $this->db->find($criteria,$fields)->sort( array('cod'=>1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	//NO AUDITADO
	protected function get_all(){
		global $f;
		$filter = array();
		if(isset($this->params["stock"])){
			/*$filter["stock"] = array(
				'$and'=>array('$exists'=>true,'$gte'=>0)
			);*/
			$filter = array(
				'$and'=>array(
					array('cant'=>array('$exists'=>true)),
					array('cant'=>array('$gte'=>0))
				)
			);
			
		}
		$data = $this->db->find($filter);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_filter_all(){
		global $f;
		$filter = array();
		if(isset($this->params["filter"])){
			$filter =$this->params["filter"];
		}
		$data = $this->db->find($filter);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_cod(){
		global $f;
		$cursor = $this->db->find(array(),array('cod'=>true))->sort(array('cod'=>-1))->limit(1);
		foreach ($cursor as $ob) {
		    $this->items = $ob['cod'];
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
		$this->db->update( $this->params['filter'] , $this->params['data'] );
	}
/*	protected function save_stock_add(){
		global $f;
		print_r($this->params);
		$prod = $this->db->findOne(array('_id'=>$this->params['prod']));
		$almc = $f->datastore->lg_almacenes->findOne(array('_id'=>$this->params['almacen']['_id']));
		$almc = $f->model('lg/alma')->params(array('_id'=>$this->params['almacen']))->get('one')->items;
		$cant = 0;
		$total = 0;
		$index = -1;
		if(isset($prod['stock'])){
			foreach($prod['stock'] as $i=>$stock){
				if($stock['almacen']['_id']==$this->params['almacen']['_id']){
					$index = $i;
					if(isset($stock['actual'])) $cant = (float)$stock['actual'];
					if(isset($stock['valor_total'])) $total = (float)$stock['valor_total'];
				}
			}
		}
		//
		// si $index = -1, generar el nuevo stock
		//
		if($index==-1){
			$almc = $f->model('lg/alma')->params(array('_id'=>$this->params['almacen']['_id']))->get('one')->items;
			$this->db->update( array('_id'=>$this->params['prod']) , array('$inc'=>array(
				'cant'=>doubleval($this->params['cant']),
				'valor_total'=>doubleval($this->params['total'])
			),'$set'=>array(
				'precio_promedio'=>($prod['valor_total']+doubleval($this->params['total']))/($prod['cant']+doubleval($this->params['cant']))
			),'$push'=>array(
				'stock'=>array(
					'inicializado'=>true,
					'almacen'=>array(
						'_id'=>$almc['_id'],
						'nomb'=>$almc['nomb'],
						'local'=>$almc['local']
					),
					'actual'=>doubleval($this->params['cant']),
					'valor_total'=>doubleval($this->params['total'])
				)
			)) );
		}else{
			$this->db->update( array('_id'=>$this->params['prod']) , array('$inc'=>array(
				'stock.'.$index.'.actual'=>doubleval($this->params['cant']),
				'stock.'.$index.'.valor_total'=>doubleval($this->params['total']),
				'cant'=>doubleval($this->params['cant']),
				'valor_total'=>doubleval($this->params['total'])
			),'$set'=>array(
				'precio_promedio'=>($prod['valor_total']+doubleval($this->params['total']))/($prod['cant']+doubleval($this->params['cant']))
			)) , array("upsert"=>true) );
		}
		$this->items = $this->db->findOne(array('_id'=>$this->params['prod']));
	}
*/

	protected function save_stock_add(){
		global $f;
		$prod = $this->db->findOne(array('_id'=>$this->params['prod']));
		//$almc = $f->datastore->lg_almacenes->findOne(array('_id'=>$this->params['almacen']['_id']));
		//$almc = $f->model('lg/alma')->params(array('_id'=>$this->params['almacen']))->get('one')->items;
		$cant = 0;
		$total = 0;
		$index = -1;
		if(isset($prod['stock'])){
			foreach($prod['stock'] as $i=>$stock){
				//var_dump($prod['stock'][$i]['almacen']['_id']);
				//var_dump($stock);
				//var_dump($this->params['almacen']['_id']);
				if($stock['almacen']['_id']==$this->params['almacen']['_id']){
					$index = $i;
					if(isset($stock['actual'])) $cant = (float)$stock['actual'];
					if(isset($stock['valor_total'])) $total = (float)$stock['valor_total'];
				}
			}
		}
		/*
		 * si $index = -1, generar el nuevo stock
		 */
		if($index==-1){
			$almc = $f->model('lg/alma')->params(array('_id'=>$this->params['almacen']['_id']))->get('one')->items;
			$actual=$this->db->update( array('_id'=>$this->params['prod']) , array('$inc'=>array(
				'cant'=>doubleval($this->params['cant']),
				'valor_total'=>doubleval($this->params['total'])
			),'$set'=>array(
				'precio_promedio'=>($prod['valor_total']+doubleval($this->params['total']))/($prod['cant']+doubleval($this->params['cant']))
			),'$push'=>array(
				'stock'=>array(
					'inicializado'=>true,
					'almacen'=>array(
						'_id'=>$almc['_id'],
						'nomb'=>$almc['nomb'],
						'local'=>$almc['local']
					),
					'actual'=>doubleval($this->params['cant']),
					'valor_total'=>doubleval($this->params['total'])
				)
			)) );

		
		}else{
			$this->db->update( array('_id'=>$this->params['prod']) , array('$inc'=>array(
				'stock.'.$index.'.actual'=>doubleval($this->params['cant']),
				'stock.'.$index.'.valor_total'=>doubleval($this->params['total']),
				'cant'=>doubleval($this->params['cant']),
				'valor_total'=>doubleval($this->params['total'])
			),'$set'=>array(
				'precio_promedio'=>($prod['valor_total']+doubleval($this->params['total']))/($prod['cant']+doubleval($this->params['cant']))
			)) , array("upsert"=>true) );

		}
		$this->items = $this->db->findOne(array('_id'=>$this->params['prod']));
	}

	protected function save_stock_def(){
		global $f;
		$prod = $this->params['prod'];
		$index = 0;
		foreach($prod['stock'] as $i=>$stock){
			if($stock['almacen']['_id']==$this->params['almacen']['_id']){
				$index = $i;
			}
		}
		$this->db->update( array('_id'=>$this->params['prod']['_id']) , array('$set'=>array(
			'stock.'.$index.'.minimo'=>(int)$this->params['minimo'],
			'stock.'.$index.'.maximo'=>(int)$this->params['maximo']
		)) );
	}
	protected function save_stock_reg(){
		global $f;
		$prod = $this->params['prod'];
		$index = 0;
		$this->db->update( array('_id'=>$this->params['prod']['_id']) , array('$inc'=>array(
			'cant'=>doubleval($this->params['actual']),
			'valor_total'=>doubleval($this->params['valor_total'])
		),'$set'=>array(
			'precio_promedio'=>(doubleval($prod['valor_total'])+doubleval($this->params['valor_total']))/(doubleval($prod['cant'])+doubleval($this->params['actual']))
		),'$push'=>array(
			'stock'=>array(
				'inicializado'=>true,
				'almacen'=>$this->params['almacen'],
				'actual'=>doubleval($this->params['actual']),
				'valor_total'=>doubleval($this->params['valor_total'])
			)
		)) );
	}
	protected function save_stock_dec(){
		global $f;
		$this->db->update( array(
			'_id'=>$this->params['_id'],
			'stock'=>array('$elemMatch'=>array(
				'almacen._id'=>$this->params['almacen']
			))
		) , array('$inc'=>array(
			'stock.$.actual'=>-abs($this->params['cant']),
			'stock.$.valor_total'=>-abs($this->params['total']),
			'cant'=>-abs($this->params['cant']),
			'valor_total'=>-abs($this->params['total'])
		) ));
	}
	protected function delete_datos(){
		global $f;
		$this->db->remove(array('_id'=>$this->params['_id']));
	}
}
?>