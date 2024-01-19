<?php
class Model_cm_rehi extends Model {
	private $espa;
	public $items;
	
	public function __construct() {
		global $f;
		$this->espa = $f->datastore->cm_espacios;
	}
	protected function get_one2(){
		global $f;
		$this->items = $this->espa->findOne(array('_id'=>$this->params["_id"]));
	}
	protected function get_lista(){
		global $f;
		$filter = array();
		$fields = array();
		$order = array('fecreg'=>-1);
		if($this->params["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["texto"];
			$filter = $helper->paramsSearch($this->params["texto"], array('nomb','num','sector'));
		};
		if(isset($this->params['tipo'])){
			switch($this->params['tipo']){
				case "M":
					$filter['mausoleo'] = array('$exists'=>true);
					break;
				case "N":
					$filter['nicho'] = array('$exists'=>true);
					break;
				case "T":
					$filter['tumba'] = array('$exists'=>true);
					break;
			}
		}
		if(isset($this->params['sector'])){
			$filter['sector'] = $this->params['sector'];
		}
		$filter['osario'] = array('$exists'=>false);
		$data = $this->espa->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_lista_get_mapa(){
		global $f;
		$filter = array(
			'imagen'=>new MongoId($this->params['_id'])
		);
		$i = 0;
		$cursor = $this->espa->find( $filter );
		foreach ($cursor as $obj) {
		    $this->items[$i] = $obj;
		    $i++;
		}
		$cursor = $f->datastore->cm_pabellones->find( $filter );
		foreach ($cursor as $obj) {
		    $this->items[$i] = $obj;
		    $this->items[$i]['pabellon'] = true;
		    $i++;
		}
	}
	protected function get_lista_mapa(){
		global $f;
		$this->items = array(
			'total'=>array(),
			'cuadra'=>array()
		);
		$files = $f->datastore->selectCollection("fs.files");
		$filter = array(
			'cm_total'=>array('$exists'=>true)
		);
		$i = 0;
		$cursor = $files->find( $filter );
		foreach ($cursor as $obj) {
		    $this->items['total'] = $obj;
		    $i++;
		}
		$filter = array(
			'cm_nomb'=>array('$exists'=>true)
		);
		$i = 0;
		$cursor = $files->find( $filter );
		foreach ($cursor as $obj) {
		    $this->items['cuadra'][$i] = $obj;
		    $i++;
		}
	}
	protected function get_verify(){
		global $f;
		$this->items = $this->espa->findOne(array(
			'nicho.pabellon._id'=>$this->params["pabellon"],
			'nicho.num'=>$this->params["num"],
			'nicho.tipo'=>$this->params["tipo"],
			'nicho.piso'=>$this->params["piso"],
			'nicho.fila'=>$this->params["fila"]
		));
	}
	/*protected function save_datos(){
		global $f;
		$array = $f->request->data['data'];
		if(isset($f->request->data['_id'])){
			/*$array['fecreg'] = new MongoDate();
			$this->espa->update(
				array('_id'=>$imagen) ,$array);*
		}else{
			$array['ubicacion']['imagen'] = new MongoId($array['ubicacion']['imagen']);
			$array['fecreg'] = new MongoDate();
			if(isset($array['nicho'])){
				$array['nicho']['pabellon']['_id'] = new MongoId($array['nicho']['pabellon']['_id']);
			}
			$this->espa->insert($array);
		}
	}*/
	protected function save_insert(){
		global $f;
		$this->espa->insert( $this->params['data'] );
		$this->items = $this->params['data'];
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->espa->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
		$this->items = $this->espa->findOne(array('_id'=>$this->params["_id"]));
	}
	protected function save_remove(){
		global $f;
		$criteria = array(
    		'_id' => new MongoId($f->request->_id)
		);
  		$collection->remove($criteria);
	}
	protected function save_propietario(){
		global $f;
		$propietario = $this->params['propietario'];
		$this->espa->update(
			array('_id'=>new MongoId($this->params['_id'])) ,
			array( '$set' => array('propietario'=>$propietario,'estado'=>'C'))
		);
	}
	protected function save_prop(){
		global $f;
		$propietario = $this->params['propietario'];
		$this->espa->update(
			array('_id'=>$this->params['_id']),
			array( '$set' => array('propietario'=>$propietario,'estado'=>'C'))
		);
	}
	protected function save_update_ocup(){
		global $f;
		$ocupante = $this->params['data']['ocupante'];
		$ocupante['_id'] = new MongoId($ocupante['_id']);
		$this->espa->update(
			array('_id'=>new MongoId($this->params['data']['_id'])) ,
			array( '$push' => array('ocupantes'=>$ocupante))
		);
	}
	protected function save_update_ocup_conId(){
		global $f;
		$ocupante = $this->params['data']['ocupante'];
		$this->espa->update(
			array('_id'=>new MongoId($this->params['data']['_id'])) ,
			array( '$push' => array('ocupantes'=>$ocupante))
		);
	}
	protected function save_upd_prop(){
		global $f;
		$prop = $this->params['propietario'];
		$prop['_id'] = new MongoId($prop['_id']);
		$this->espa->update(
			array('_id'=>new MongoId($this->params['_id'])) ,
			array( '$set' => array('propietario'=>$prop))
		);
	}
	protected function save_anul_conce(){
		global $f;
		$this->espa->update(
			array('_id'=>new MongoId($this->params['_id'])),
			array( '$unset' => array('propietario'=>true),'$set'=>array('estado'=>'D'))
		);
	}
	protected function get_espa_prop(){
		global $f;
		$filter = array(
			'propietario._id'=>new MongoId($this->params['_id'])
		);
		$cursor = $this->espa->find( $filter );
		foreach ($cursor as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_all_difu_pro(){
		global $f;
		$criterio = array(
			'roles.ocupante.propietario._id'=>new MongoId($this->params['_id'])
		);
		$result = $f->datastore->entidades->find($criterio,array('roles'=>false));
		foreach ($result as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_one(){
		global $f;
		$this->items = $this->espa->findOne(array('_id'=>new MongoId($f->request->data['_id'])));
	}
	protected function delete_ocupante(){
		global $f;
		$this->espa->update( array('_id'=>new MongoId($this->params['_id'])) , array( '$pull' => array( 'ocupantes' => array('_id'=>new MongoId($this->params['ocupante'])) )  ) );
	}
	protected function get_all_mausoleos(){
		global $f;
		$filter = array(
			'propietario._id'=>new MongoId($f->request->data['_id']['$id']),
			'mausoleo'=>array('$exists'=>true)
		);
		$cursor = $this->espa->find( $filter );
		foreach ($cursor as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_all_nichos(){
		global $f;
		$filter = array(
			'nicho.pabellon._id'=>$this->params['pabellon']
		);
		$cursor = $this->espa->find( $filter );
		foreach ($cursor as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_osario(){
		global $f;
		$filter = array(
			'osario'=>array('$exists'=>true)
		);
		$this->items = $this->espa->findOne( $filter );
	}
	protected function save_custom(){
		global $f;
		$this->espa->update(array( '_id' => $this->params['_id'] ),$this->params['data']);
	}
	

	protected function get_search(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto']))
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$criteria = $helper->paramsSearch($this->params["texto"], array(
					'nomb',
					'cod',
					'propietario.nomb',
					'propietario.appat',
					'propietario.apmat',
					'propietario.fullname',
					'propietario.docident.num',
					'ocupantes.nomb',
					'ocupantes.appat',
					'ocupantes.apmat',
					'ocupantes.fullname',
					'ocupantes.docident.num',
				));
			}
		if(isset($this->params['tipo'])){
			if($this->params['tipo']!=''){
				switch($this->params['tipo']){
					case "M":
						$criteria['mausoleo'] = array('$exists'=>true);
						break;
					case "N":
						$criteria['nicho'] = array('$exists'=>true);
						break;
					case "T":
						$criteria['tumba'] = array('$exists'=>true);
						break;
				}
			}
		}
		$criteria['osario'] = array('$exists'=>false);
		if(isset($this->params['sector'])){
			if($this->params['sector']!=''){
				$criteria['sector'] = $this->params['sector'];
			}
		}
		if(isset($this->params['estado'])){
			if($this->params['estado']!=''){
				$criteria['estado'] = $this->params['estado'];
			}
		}
		if(isset($this->params['num'])){
			if($this->params['num']!=''){
				$criteria['nicho.num'] = $this->params['num'];
			}
		}
		if(isset($this->params['fila'])){
			if($this->params['fila']!=''){
				$criteria['nicho.fila'] = $this->params['fila'];
			}
		}
		if(isset($this->params['piso'])){
			if($this->params['piso']!=''){
				$criteria['nicho.piso'] = $this->params['piso'];
			}
		}
		if(isset($this->params["filter"])){
			foreach ($this->params['filter'] as $i=>$filter){
				$criteria[$filter['nomb']] = $filter['value'];
				if(substr($filter['nomb'],-3)=='_id'){
					$criteria[$filter['nomb']] = new MongoId($filter['value']);
				}
			}
		}
		$fields = array();
		$sort = array('nomb'=>1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		if(isset($this->params["page"])){
			$cursor = $this->espa->find($criteria,$fields)->sort($sort)->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
			foreach ($cursor as $obj) {
				$this->items[] = $obj;
			}
			$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
		}else{
			$cursor = $this->espa->find($criteria,$fields)->sort($sort);
			foreach ($cursor as $obj) {
				$this->items[] = $obj;
			}
		}
	}

}
?>