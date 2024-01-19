<?php
class Model_mg_entidad extends Model {
	private $entidad;
	public $items;
	
	public function __construct() {
		global $f;
		$this->entidad = $f->datastore->mg_entidades;
	}
	protected function get_lista(){
		global $f;
		$criteria = array();
		if(isset($this->params['texto'])){
			if($this->params["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $this->params["texto"];
				$criteria = $helper->paramsSearch($this->params["texto"], array('fullname','nomb','appat','apmat','docident.num'));
			}
		}
		$criteria['roles.titular'] = array('$ne'=>true);
		if(isset($this->params['roles'])){
			$criteria['roles.'.$this->params['roles']] = array( '$exists'=>true);
		}
		if(isset($this->params['organizacion'])){
			$criteria['roles.practicante.organizacion._id'] = $this->params["organizacion"];
		}
		if(isset($this->params['tipo_paciente'])){
			if($this->params['tipo_paciente']=='P'){
				$criteria['roles.paciente.fecini'] = array( '$exists'=>false);
			}elseif($this->params['tipo_paciente']=='C'){
				$criteria['roles.paciente.fecini'] = array( '$exists'=>true);
			}
		}
		if(isset($this->params["rol"])){
			$criteria[$this->params["rol"]['nomb']] = $this->params['rol']['value'];
		}
		//$criteria['roles.titular'] = array('$ne'=>true);
		if(isset($this->params["filter"])){
			foreach ($this->params['filter'] as $i=>$filter){
				$criteria[$filter['nomb']] = $filter['value'];
			}
		}//print_r($criteria);die();
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$sort = array('appat'=>1,'apmat'=>1,'nomb'=>1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->entidad->find($criteria,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($sort)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_lista_gest(){
		global $f;
		if(isset($f->request->data['filter']))
			$sort = array ( $f->request->data['filter'] => intval($f->request->data['order']));
		else
			$sort = array ( "appat" => 1,"apmat"=>1,"nomb"=>1);
		$cursor = $this->entidad->find( array('roles.trabajador'=>array('$exists'=>false),'roles.gestor'=>array('$exists'=>true)) )->sort($sort)->skip( $f->request->page_rows * ($f->request->page-1) )->limit( $f->request->page_rows );
		foreach ($cursor as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_lista_gest_trab(){
		global $f;
		if(isset($f->request->data['filter']))
			$sort = array ( $f->request->data['filter'] => intval($f->request->data['order']));
		else
			$sort = array ( "appat" => 1,"apmat"=>1,"nomb"=>1);
		$cursor = $this->entidad->find( array(
			'$and'=>array(
				array('roles.trabajador'=>array('$exists'=>true)),
				array('roles.gestor'=>array('$exists'=>true))
			)
		) )->sort($sort)->skip( $f->request->page_rows * ($f->request->page-1) )->limit( $f->request->page_rows );
		foreach ($cursor as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_search(){
		global $f;
		if($f->request->data["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $f->request->data["texto"];
			$criteria = $helper->paramsSearch($f->request->data["texto"], array('fullname','nomb','appat','apmat','docident.num'));
		}else $criteria = array();
		if(isset($this->params["rol"])){
			$criteria[$this->params["rol"]['nomb']] = $this->params['rol']['value'];
		}
		//$criteria['roles.titular'] = array('$ne'=>true);
		if(isset($this->params["filter"])){
			foreach ($this->params['filter'] as $i=>$filter){
				$criteria[$filter['nomb']] = $filter['value'];
			}
		}//print_r($criteria);die();
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$sort = array('appat'=>1,'apmat'=>1,'nomb'=>1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		if(isset($this->params["page_rows"]))
			$cursor = $this->entidad->find($criteria,$fields)->sort($sort)->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		else
			$cursor = $this->entidad->find($criteria,$fields)->sort($sort);
		foreach ($cursor as $obj) {
			/*if($f->request->data["texto"]!=''){
				$obj['docident_num'] = $obj['docident'][0]['num'];
				if($helper->filtrar($f->request->data["texto"], $obj, array('nomb','appat','apmat','docident_num'))){
					$this->items[] = $obj;
				}
			}else $this->items[] = $obj;*/
		    $this->items[] = $obj;
		}
		//$this->paging($this->params["page"],$this->params["page_rows"],count($this->items));
		if(isset($this->params["page_rows"]))
			$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_search_all(){
		global $f;
		if(isset($f->request->data["texto"])){
			if($f->request->data["texto"]!=''){
				$f->library('helpers');
				$helper=new helper();
				$parametro = $f->request->data["texto"];
				$criteria = $helper->paramsSearch($f->request->data["texto"], array('fullname','nomb','appat','apmat','docident.num'));
			}else $criteria = array();
		}else $criteria = array();
		if(isset($this->params["rol"])){
			$criteria[$this->params["rol"]['nomb']] = $this->params['rol']['value'];
		}
		if(isset($this->params["filter"])){
			foreach ($this->params['filter'] as $i=>$filter){
				$criteria[$filter['nomb']] = $filter['value'];
			}
		}
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$cursor = $this->entidad->find($criteria,$fields)->sort( array('tipo_enti'=>-1,'appat'=>1,'nomb'=>1) );
		foreach ($cursor as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_search_tra(){
		global $f;
		if($f->request->data["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $f->request->data["texto"];
			$criteria = $helper->paramsSearch($f->request->data["texto"], array('fullname','nomb','appat','apmat','docident.num'));
		}else $criteria = array();
		if(isset($this->params["rol"])){
			$criteria[$this->params["rol"]['nomb']] = $this->params['rol']['value'];
		}
		$criteria['roles.trabajador'] = array('$exists'=>true);
		$cursor = $this->entidad->find($criteria)->sort( array('tipo_enti'=>-1,'appat'=>1,'nomb'=>1) )->skip( $f->request->page_rows * ($f->request->page-1) )->limit( $f->request->page_rows );
		foreach ($cursor as $obj) {
			/*if($f->request->data["texto"]!=''){
				$obj['docident_num'] = $obj['docident'][0]['num'];
				if($helper->filtrar($f->request->data["texto"], $obj, array('nomb','appat','apmat','docident_num'))){
					$this->items[] = $obj;
				}
			}else $this->items[] = $obj;*/
		    $this->items[] = $obj;
		}
		//$this->paging($this->params["page"],$this->params["page_rows"],count($this->items));
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_search_gest_int(){
		global $f;
		if($f->request->data["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $f->request->data["texto"];
			$criteria = $helper->paramsSearch($f->request->data["texto"], array('fullname','nomb','appat','apmat','docident.num'));
		}else $criteria = array();
		if(isset($this->params["rol"])){
			$criteria[$this->params["rol"]['nomb']] = $this->params['rol']['value'];
		}
		$criteria['roles.trabajador'] = array('$exists'=>true);
		$cursor = $this->entidad->find($criteria)->sort( array('tipo_enti'=>-1,'appat'=>1,'nomb'=>1) )->skip( $f->request->page_rows * ($f->request->page-1) )->limit( $f->request->page_rows );
		foreach ($cursor as $obj) {
			/*if($f->request->data["texto"]!=''){
				$obj['docident_num'] = $obj['docident'][0]['num'];
				if($helper->filtrar($f->request->data["texto"], $obj, array('nomb','appat','apmat','docident_num'))){
					$this->items[] = $obj;
				}
			}else $this->items[] = $obj;*/
		    $this->items[] = $obj;
		}
		//$this->paging($this->params["page"],$this->params["page_rows"],count($this->items));
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_search_gest_ext(){
		global $f;
		if($f->request->data["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $f->request->data["texto"];
			$criteria = $helper->paramsSearch($f->request->data["texto"], array('fullname','nomb','appat','apmat','docident.num'));
		}else $criteria = array();
		if(isset($this->params["rol"])){
			$criteria[$this->params["rol"]['nomb']] = $this->params['rol']['value'];
		}
		$cursor = $this->entidad->find($criteria)->sort( array('tipo_enti'=>-1,'appat'=>1,'nomb'=>1) )->skip( $f->request->page_rows * ($f->request->page-1) )->limit( $f->request->page_rows );
		foreach ($cursor as $obj) {
			/*if($f->request->data["texto"]!=''){
				$obj['docident_num'] = $obj['docident'][0]['num'];
				if($helper->filtrar($f->request->data["texto"], $obj, array('nomb','appat','apmat','docident_num'))){
					$this->items[] = $obj;
				}
			}else $this->items[] = $obj;*/
		    $this->items[] = $obj;
		}
		//$this->paging($this->params["page"],$this->params["page_rows"],count($this->items));
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_one(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		if(isset($this->params['filter'])){
			$this->items = $this->entidad->findOne($this->params['filter'],$fields);
		}else{
			$this->items = $this->entidad->findOne( array('_id'=>new MongoId($this->params['_id'])),$fields);
		}
	}
	protected function get_enti(){
		global $f;
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$this->items = $this->entidad->findOne( array('_id'=>$this->params['_id']),$fields);
	}
	protected function get_titular(){
		global $f;
		$this->items = $this->entidad->findOne( array('roles.titular'=>true ));
	}
	protected function get_custom_data(){
		global $f;
		$cursor = $this->entidad->find($this->params['filter'],$this->params['fields'])->sort( $this->params['sort'] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
	}
	protected function get_cod_trabajador(){
		global $f;
		$this->items = $this->entidad->findOne( array('roles.trabajador.cod_tarjeta'=>$this->params['cod']));
	}
	protected function get_doc(){
		global $f;
		$criteria = array('docident.num'=>$this->params['doc']);
		if(isset($this->params['fields'])) $fields = $this->params['fields'];
		else $fields = array();
		$cursor = $this->entidad->find($criteria,$fields);
		foreach ($cursor as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_locales(){
		global $f;
		$enti = $this->entidad->findOne(array('roles.titular'=>true),array('domicilios'=>true));
		foreach ($enti['domicilios'] as $domi){
			$this->items[] = $domi;
		}
	}
	protected function save_insert(){
		global $f;
		$this->entidad->insert( $this->params['data'] );
		$this->items = $this->params['data'];
	}
	protected function save_update(){
		global $f;
		unset($this->params['data']['_id']);
		$this->entidad->update( array('_id'=>$this->params['_id']) , array('$set'=>$this->params['data']) );
		$this->items = $this->entidad->findOne(array('_id'=>$this->params['_id']));
	}
	protected function save_custom(){
		global $f;
		$this->entidad->update( array('_id'=>$this->params['_id']) , $this->params['data'] );
	}
	protected function save_rol(){
		global $f;
		$this->entidad->update(
			array( '_id' =>$this->params['_id'] ),
			array('$set'=>array($this->params['rol']=>true))
		);
	}
	protected function save_datos(){
		global $f;
		$array = (array)$f->request->data;
		if(isset($array['nomb'])){
			if(isset($array['appat'])){
				$array['fullname'] = trim($array['nomb']).' '.trim($array['appat']).' '.trim($array['apmat']);
			}else{
				$array['fullname'] = trim($array['nomb']);
			}
		}
		if(isset($array['dni'])){
			if($array['dni']!='--'){
				if(!isset($array['docident']))
					$array['docident'] = array();
				$array['docident'][] = array(
					'tipo'=>'DNI',
					'num'=>$array['dni']
				);
			}
		}
		if(isset($array['ruc'])){
			if($array['ruc']!='--'){
				if(!isset($array['docident']))
					$array['docident'] = array();
				$array['docident'][] = array(
					'tipo'=>'RUC',
					'num'=>$array['ruc']
				);
			}
		}
		if(isset($array['imagen'])){
			if(substr($array['imagen'], 0, 3)!='/Us')
				$array['imagen'] = new MongoId($array['imagen']);
		}
		if(isset($array['roles']['proveedor'])) $array['roles']['proveedor'] = true;
		if(isset($f->request->data['_id'])){
			foreach ($array as $index=>$obj){
				if($index!='_id') $set[$index] = $obj;
			}
			$array = array( '$set'=>$set );
			$this->entidad->update(array( '_id' => new MongoId($f->request->data['_id']) ),array('$set'=>$array));
			$array = $this->entidad->findOne( array('_id'=>new MongoId($f->request->data['_id'])));
		}else{
			$array['fecreg'] = new MongoDate();
			if(isset($array['roles'])){
				if($array['roles']['propietario']==true){
					$array['roles']['propietario'] = array(
						'ocupantes'=>array(),
						'espacios'=>array()
					);
				}
				if($array['roles']['ocupante']==true){
					$array['roles']['ocupante'] = array(
						'difunto'=>false,
						'propietario'=>true,
						'espacio'=>true
					);
				}
			}
			$this->entidad->insert($array);
		}
		$this->obj = $array;
	}
	protected function save_titular(){
		global $f;
		$array = (array)$f->request->data;
		
		$array['_id'] = new MongoId($array['_id']);
		if(isset($array['imagen'])) $array['imagen'] = new MongoId($array['imagen']);
		foreach ($array as $index=>$obj){
			if($index!='_id'){
				if($index=='domicilios'){
					foreach ($obj as $i=>$dom){
						if(!isset($dom['_id']) || $dom['_id']==null) $obj[$i]['_id'] = new MongoId();
						else $obj[$i]['_id'] = new MongoId($dom['_id']);
					}
				}
				$set[$index] = $obj;
			}
		}
		$obj = array( '$set'=>$set );
		$this->entidad->update(array( 'roles.titular' => true ),$obj);
		
		$this->obj = $obj;
	}
	/*
	protected function save_titular_legacy(){
		global $f;
		$array = (array)$f->request->data;
		
		$array['_id'] = new MongoId($array['_id']);
		if(isset($array['imagen'])) $array['imagen'] = new MongoId($array['imagen']);
		foreach ($array as $index=>$obj){
			if($index!='_id'){
				if($index=='domicilios'){
					foreach ($obj as $i=>$dom){
						if($dom['_id']==null) $obj[$i]['_id'] = new MongoId();
						else $obj[$i]['_id'] = new MongoId($dom['_id']);
					}
				}
				$set[$index] = $obj;
			}
		}
		$obj = array( '$set'=>$set );
		$this->entidad->update(array( 'roles.titular' => true ),$obj);
		
		$this->obj = $obj;
	}
	*/
	protected function delete_datos(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->_id),
		);
		$this->entidad->remove($this->items);
	}
	protected function save_propietario_espa(){
		global $f;
		$espacio = $this->params['espacio'];
		$this->entidad->update(
			array('_id'=>$this->params['_id']) ,
			array( '$push' => array( "roles.propietario.espacios" => $espacio ))
		);
	}
	protected function save_estado_difunto(){
		global $f;
		$set = array(
			"roles.ocupante.difunto" => true
		);
		if($this->params['fecnac']!='')
			$set['fecnac'] = new MongoDate($this->params['fecnac']);
		if($this->params['fecdef']!='')
			$set['fecdef'] = new MongoDate($this->params['fecdef']);
		$this->entidad->update(
			array('_id'=>$this->params['_id']) ,
			array( '$set' => $set)
		);
	}
	protected function get_all_ocu_pro(){
		global $f;
		$i = 0;
		$criterio = array(
			'_id'=>new MongoId($f->request->data['_id']['$id'])
		);
		$result = $this->entidad->find($criterio,array('roles.propietario.ocupantes'=>true));
		foreach ($result as $obj) {
			if(isset($obj['roles']['propietario']['ocupantes'])){
				foreach ($obj['roles']['propietario']['ocupantes'] as $ocupante) {
					$temp = $this->entidad->findOne(array('_id'=>$ocupante['_id']),
						array('_id'=>true, 'nomb'=>true, 'appat'=>true,'apmat'=>true,'roles.ocupante'=>true));
					if(!$temp['roles']['ocupante']['difunto']){
						$this->items[$i] = $temp;
						$i++;
					}
				}
			}
		}
	}
	protected function get_all_difu_pro(){
		global $f;
		$i = 0;
		$criterio = array(
			'_id'=>new MongoId($this->params['_id'])
		);
		$result = $this->entidad->find($criterio,array('roles.propietario.ocupantes'=>true));
		foreach ($result as $obj) {
			if(isset($obj['roles']['propietario']['ocupantes'])){
				foreach ($obj['roles']['propietario']['ocupantes'] as $ocupante) {
					$temp = $this->entidad->findOne(array('_id'=>$ocupante['_id']),
						array('_id'=>true, 'nomb'=>true, 'appat'=>true,'apmat'=>true,'roles.ocupante'=>true));
					if($temp['roles']['ocupante']['difunto']==true){
						$this->items[$i] = $temp;
						$i++;
					}
				}
			}
		}
	}
	protected function get_all_difu_espa(){
		global $f;
		$i = 0;
		$criterio = array(
			'roles.ocupante.espacio._id'=>new MongoId($f->request->data['_id']['$id']),
			'roles.ocupante.difunto' => true
		);
		$result = $this->entidad->find($criterio,
			array('_id'=>true, 'nomb'=>true, 'appat'=>true,'apmat'=>true,'roles.ocupante'=>true));
		foreach ($result as $obj) {
			$this->items[$i] = $obj;
			$i++;
		}
	}
	protected function get_search_empresas(){
		global $f;
		$i = 0;
		$parametro = $f->request->data["texto"];
		$criteria = array(
			'tipo_enti' => 'E',
			'$or' => array(
				array("nomb" => new MongoRegex('/'.$parametro.'/i')),
				array("docident.num" => new MongoRegex('/'.$parametro.'/i')),
				array("appat" => new MongoRegex('/'.$parametro.'/i')),
				array("apmat" => new MongoRegex('/'.$parametro.'/i')))
  		);
		$cursor = $this->entidad->find( $criteria )->skip( $f->request->page_rows * ($f->request->page-1) )->sort( array('tipo_enti'=>-1,'appat'=>1,'nomb'=>1) )->limit( $f->request->page_rows );
		foreach ($cursor as $obj) {
		    $this->items[$i] = $obj;
			$i++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all_trab(){
		global $f;
		$i = 0;
		if(isset($this->params['data'])){
			$filter = $this->params['data'];
		}else $filter = array();
		$filter['roles.titular'] = array('$ne'=>true);
		if(isset($this->params['roles'])){
			$filter['roles.'.$this->params['roles']] = array( '$exists'=>true);
		}
		if(isset($this->params['organizacion'])){
			$filter['roles.practicante.organizacion._id'] = $this->params["organizacion"];
		}
		if(isset($this->params['programa'])){
			$filter['roles.trabajador.programa._id'] = $this->params["programa"];
		}
		if(isset($this->params['estado'])){
			$filter['roles.trabajador.estado'] = $this->params["estado"];
			$filter['roles.trabajador.contrato'] = array('$exists'=>true);
		}
		$fields = array( );
		$sort = array ( "appat" => 1,"apmat"=>1,"nomb"=>1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$cursor = $this->entidad->find( $filter , $fields )->sort($sort);
		foreach($cursor as $obj){
		    $this->items[$i] = $obj;
		    $i++;
		}
		//$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_all_tipo(){
		global $f;
		$filter['roles.titular'] = array('$ne'=>true);
		if(isset($this->params['tipo'])){
			$filter['roles.trabajador.contrato.cod'] = $this->params["tipo"];
			$filter['roles.trabajador.estado'] = 'H';
		}
		$fields = array( );
		$sort = array ( "appat" => 1,"apmat"=>1,"nomb"=>1);
		$cursor = $this->entidad->find($filter, $fields)->sort($sort);
		foreach($cursor as $i=>$obj){
			$this->items[$i] = $obj;
			$i++;
		}
		//$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function save_anu_asignacion(){
		global $f;
		$this->entidad->update( array('_id'=>new MongoId($this->params['ocupante'])),array('$unset'=>array('roles.ocupante'=>1)));
	}
	protected function delete_data(){
		global $f;
		unset($this->params['data']['_id']);
		$this->entidad->update( array('_id'=>$this->params['_id']) , array('$unset'=>$this->params['data']) );
	}
}
?>