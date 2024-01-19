<?php
class Model_cm_oper extends Model {
	private $oper;
	private $db;
	public $items;
	public $dias;

	public function __construct() {
		global $f;
		$this->db = $f->datastore;
		$this->oper = $this->db->cm_operaciones;
		$this->dias = 5;
	}
	protected function get_lista(){
		global $f;
		if($this->params["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = trim($this->params["texto"]);
			$criteria = $helper->paramsSearch($this->params["texto"], array(
				'espacio.nomb',
				'ocupante.fullname',
				'propietario.fullname',
				'recibo.cliente.fullname'
			));
		}
		else
		{
				$criteria = array();
		}

		if(isset($this->params['progra']))
		{
			if($this->params['progra']=='1')
			{
				$criteria['programacion'] = array('$exists' => true);
			}
			else if($this->params['progra']=='0')
				{
					$criteria['programacion'] = array('$exists' => false);
				}
		}

		if(isset($this->params['fecbus'])){
			$findate=$this->params['fecbus'];
			$start=new MongoDate(strtotime($findate." 00:00:00"));
			$end=new MongoDate(strtotime($findate." 23:59:59"));
				switch ($this->params['progra'])
				{
					case '0':
						$criteria['fecreg'] = array('$gte'=>$start,'$lte'=>$end);
						break;
					case '1':
						$criteria['programacion.fecprog'] = array('$gte'=>$start,'$lte'=>$end);
						break;
					case '2':
						$criteria['programacion.fecprog'] = array('$gte'=>$start,'$lte'=>$end);
						break;
				}

		}

		if(isset($this->params['oper'])){
			switch($this->params['oper']){
				case 'CN':
					$criteria['concesion'] = array('$exists' => true);break;
				case 'IN':
					$criteria['inhumacion'] = array('$exists' => true);break;
				case 'TR':
					$criteria['traslado'] = array('$exists' => true);break;
				case 'AD':
					$criteria['adjuntacion'] = array('$exists' => true);break;
				case 'CO':
					$criteria['construccion'] = array('$exists' => true);break;
				case 'CL':
					$criteria['colocacion'] = array('$exists' => true);break;
			}
		}

		$sort = array('_id'=>-1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->oper->find($criteria)->skip($this->params['page_rows']*($this->params['page']-1))->sort($sort)->limit($this->params['page_rows']);
	 	$this->items=array();
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_listaprog_all(){
		global $f;
		if($this->params["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["texto"];
			$criteria = $helper->paramsSearch($this->params["texto"], array('espacio.nomb'));
		}else $criteria = array();
  		/*$criteria = array ( 'programacion' => array('$exists' => true),
  							'ejecucion' => array('$exists' => false)
  		);*/
  		$criteria['programacion'] = array('$exists' => true);
		$criteria['ejecucion'] = array('$exists' => true);
		$sort = array('fecreg'=>-1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->oper->find($criteria)->skip($this->params['page_rows']*($this->params['page']-1) )->sort($sort)->limit($this->params['page_rows']);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_listaprog(){
		global $f;
  		/*$criteria = array ( 'programacion' => array('$exists' => true),
  							'ejecucion' => array('$exists' => false),
  							'programacion.fecprog' =>array(
  								'$gte'=>new MongoDate(strtotime($f->request->data['fecbus'])),
  								'$lt'=>new MongoDate(strtotime($f->request->data['fecbus'].' + 1 days'))
  		));*/
		if($this->params["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["texto"];
			$criteria = $helper->paramsSearch($this->params["texto"], array('espacio.nomb'));
		}else $criteria = array();
  		$criteria['anulacion'] = array('$exists' => false);
  		$criteria['programacion'] = array('$exists' => true);
		$criteria['ejecucion'] = array('$exists' => false);
		if(!isset($this->params['all'])){
			$criteria['programacion.fecprog'] = array(
				'$gte'=>new MongoDate(strtotime($this->params['fecbus'])),
				'$lt'=>new MongoDate(strtotime($this->params['fecbus'].' + 1 days'))
	  		);
		}
		if(isset($this->params['oper'])){
			switch($this->params['oper']){
				case 'IN':
					$criteria['inhumacion'] = array('$exists' => true);break;
				case 'TR':
					$criteria['traslado'] = array('$exists' => true);break;
				case 'AD':
					$criteria['adjuntacion'] = array('$exists' => true);break;
				case 'CO':
					$criteria['construccion'] = array('$exists' => true);break;
			}
		}
		$sort = array('fecreg'=>-1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		//print_r($criteria);die();
		$data = $this->oper->find($criteria)->skip($this->params['page_rows']*($this->params['page']-1))->sort($sort)->limit($this->params['page_rows']);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_listaconc(){
		global $f;
		if(isset($f->request->data['bus'])){
			$f->library('helpers');
			$helper=new helper();
			$criteria = $helper->paramsSearch($f->request->data['bus'], array('propietario.nomb','propietario.appat','propietario.apmat','espacio.nomb'));
			$criteria['concesion'] = array('$exists' => true);
		}else
  			$criteria = array ( 'concesion' => array('$exists' => true));
		$data = $this->oper->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->sort(array('fecreg'=>-1))->limit( $f->request->page_rows );
		foreach ($data as $ob) {
			$this->items[] = $ob;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_operacion(){
		global $f;
  		$id=new MongoId($this->params['_id']);
  		if($this->params['oper']!='') $criteria[$this->params['oper']] = array('$exists' => true);
  		$criteria[$this->params['campo']] = $id;
		$data = $this->oper->find($criteria)->sort(array('_id'=>-1));
		foreach($data as $ob){
		    $this->items[] = $ob;
		}
	}
	protected function get_custom(){
		global $f;
		$fields = array();
		if(isset($this->params['fields']))
			$fields = $this->params['fields'];
		$sort = array('_id'=>-1);
		if(isset($this->params['sort']))
			$sort = $this->params['sort'];
		$data = $this->oper->find($this->params['filter'],$fields)->sort($sort);
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_listaconcrec(){
		global $f;
  		if(isset($f->request->data['filter'])){
  			$filtro = $f->request->data['filter'];
  			if($filtro == 'propietario') $filtro = 'propietario.nomb';
			$ordenacion = array ( $filtro => intval($f->request->data['order']));
  		}
		else
			$ordenacion = array ( '_id' => -1);
		if(isset($f->request->data['bus'])){
			$f->library('helpers');
			$helper=new helper();
			$criteria = $helper->paramsSearch($f->request->data['bus'], array('propietario.nomb','propietario.appat','propietario.apmat','espacio.nomb'));
			$criteria['concesion.estado'] = 'A';
		}
		else
			$criteria = array ('concesion.estado' => 'A');
		$data = $this->oper->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->sort($ordenacion)->limit( $f->request->page_rows );
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_listaconcpor(){
		global $f;
  		$fecha = new MongoDate(time() + ((int)$this->dias * (24 * 3600)));
  		if(isset($f->request->data['filter'])){
  			$filtro = $f->request->data['filter'];
  			if($filtro == 'propietario') $filtro = 'propietario.nomb';
			$ordenacion = array ( $filtro => intval($f->request->data['order']));
  		}
		else
			$ordenacion = array ( '_id' => -1);
		if(!isset($f->request->data['bus']))
			$criteria = array (
				'concesion.estado' => 'A',
				'concesion.fecven' => array('$lt' => $fecha,'$gt'=>new MongoDate()),
				'concesion.condicion' => 'T'
			);
		else{
			$f->library('helpers');
			$helper=new helper();
			$criteria = $helper->paramsSearch($f->request->data['bus'], array('propietario.nomb','propietario.appat','propietario.apmat','espacio.nomb'));
			$criteria['concesion.estado'] = 'A';
			$criteria['concesion.fecven'] = array('$lt' => $fecha,'$gt'=>new MongoDate());
			$criteria['concesion.condicion'] = 'T';
		}
		$data = $this->oper->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->sort($ordenacion)->limit( $f->request->page_rows );
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_listaconcven(){
		global $f;
  		$fecha = new MongoDate();
  		if(isset($f->request->data['filter'])){
  			$filtro = $f->request->data['filter'];
  			if($filtro == 'propietario') $filtro = 'propietario.nomb';
			$ordenacion = array ( $filtro => intval($f->request->data['order']));
  		}
		else
			$ordenacion = array ( '_id' => -1);
		if(!isset($f->request->data['bus']))
			$criteria = array (
				'concesion.estado' => 'A',
				'concesion.fecven' => array('$lt' => $fecha),
				'concesion.condicion' => 'T'
			);
		else{
			$f->library('helpers');
			$helper=new helper();
			$criteria = $helper->paramsSearch($f->request->data['bus'], array('propietario.nomb','propietario.appat','propietario.apmat','espacio.nomb'));
			$criteria['concesion.estado'] = 'A';
			$criteria['concesion.fecven'] = array('$lt' => $fecha);
			$criteria['concesion.condicion'] = 'T';
		}
		$data = $this->oper->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->sort($ordenacion)->limit( $f->request->page_rows );
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_search_all(){
		global $f;
		if($this->params["texto"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["texto"];
			$criteria = $helper->paramsSearch($this->params["texto"], array(
				'espacio.nomb','propietario.n','propietario.doc','ocupante.n','ocupante.doc','observ'
			));
		}else $criteria = array();
		$fields = array();
		$cursor = $this->oper->find($criteria,$fields)->sort( array('_id'=>-1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function save_update(){
		global $f;
		$this->oper->update(array( '_id' => $this->params['_id'] ),$this->params['data']);
	}
	protected function save_datos_conc(){
		global $f;
		$array = $f->request->data;
		unset($array['cuenta_cobrar']);
		$array['estado'] = 'A';
		$array['fecreg'] = new MongoDate();
		$array['propietario']['_id'] = new MongoId($array['propietario']['_id']);
		$array['espacio']['_id'] = new MongoId($array['espacio']['_id']);
		$array['trabajador'] = $f->session->userDB;
		if(!isset($array['concesion']['condicion'])){
			$array['concesion']['condicion'] = 'P';
		}
		if($array['concesion']['condicion']=="T"){
			$array['concesion']['fecven'] = new MongoDate(strtotime($array['concesion']['fecven']));
		}
		$array['concesion']['estado'] = 'A';
		if(isset($f->request->data['_id'])){
			$array['_id'] = new MongoId($f->request->data['_id']);
			$this->oper->update(array( '_id' => new MongoId($f->request->data['_id']) ),$array);
		}else{
			$this->oper->insert($array);
		}
		$this->items = $this->oper->findOne($array);
	}
	protected function save_datos_inhu(){
		global $f;
		$array = $f->request->data['data'];
		unset($array['cuenta_cobrar']);
		$array['estado'] = 'A';
		$array['fecreg'] = new MongoDate();
		$array['propietario']['_id'] = new MongoId($array['propietario']['_id']);
		$array['espacio']['_id'] = new MongoId($array['espacio']['_id']);
		$array['ocupante']['_id'] = new MongoId($array['ocupante']['_id']);
		$array['trabajador'] = $f->session->userDB;
		$array['inhumacion']['fecdef'] = new MongoDate(strtotime($array['inhumacion']['fecdef']));
		if(isset($array['inhumacion']['funeraria']))
			$array['inhumacion']['funeraria']['_id'] = new MongoId($array['inhumacion']['funeraria']['_id']);
		if(isset($array['inhumacion']['municipalidad']))
			$array['inhumacion']['municipalidad']['_id'] = new MongoId($array['inhumacion']['municipalidad']['_id']);
		if(isset($array['programacion']))
			$array['programacion']['fecprog'] = new MongoDate(strtotime($array['programacion']['fecprog']));
		if(isset($f->request->data['_id'])){
			/*$array['_id'] = new MongoId($f->request->data['_id']);
			$this->oper->update(array( '_id' => new MongoId($f->request->data['_id']) ),$array);*/
		}else{
			$this->oper->insert($array);
		}
		$this->items = $this->oper->findOne($array);
	}
	protected function save_datos_tras(){
		global $f;
		$array = $f->request->data;
		unset($array['cuenta_cobrar']);
		$array['estado'] = 'A';
		$array['fecreg'] = new MongoDate();
		$array['propietario']['_id'] = new MongoId($array['propietario']['_id']);
		$array['espacio']['_id'] = new MongoId($array['espacio']['_id']);
		$array['ocupante']['_id'] = new MongoId($array['ocupante']['_id']);
		$array['trabajador'] = $f->session->userDB;
		$array['programacion']['fecprog'] = new MongoDate(strtotime($array['programacion']['fecprog']));
		if(isset($array['traslado']['espacio_destino'])){
				$array['traslado']['espacio_destino']['_id'] = new MongoId($array['traslado']['espacio_destino']['_id']);
		}
		else{
			$array['traslado']['cementerio']['_id'] = new MongoId($array['traslado']['cementerio']['_id']);
		}
		if(isset($f->request->data['_id'])){
			/*$array['_id'] = new MongoId($f->request->data['_id']);
			$this->oper->update(array( '_id' => new MongoId($f->request->data['_id']) ),$array);*/
		}else{
			$this->oper->insert($array);
		}
		$this->items = $this->oper->findOne($array);
	}
	protected function save_datos_colo(){
		global $f;
		$i = 0;
		$array = $f->request->data;
		unset($array['cuenta_cobrar']);
		$array['estado'] = 'A';
		$array['fecreg'] = new MongoDate();
		$array['propietario']['_id'] = new MongoId($array['propietario']['_id']);
		if(isset($array['ocupante']))
			$array['ocupante']['_id'] = new MongoId($array['ocupante']['_id']);
		$array['espacio']['_id'] = new MongoId($array['espacio']['_id']);
		//$array['ocupante']['_id'] = new MongoId($array['ocupante']['_id']);
		$array['trabajador'] = $f->session->userDB;
		$array['programacion']['fecprog'] = new MongoDate(strtotime($array['programacion']['fecprog']));
		foreach ($array['colocacion']['accesorios'] as $obj){
			$array['colocacion']['accesorios'][$i]['_id'] = new MongoId($obj['_id']['$id']);
			$i++;
		}
		if(isset($f->request->data['_id'])){
			/*$array['_id'] = new MongoId($f->request->data['_id']);
			$this->oper->update(array( '_id' => new MongoId($f->request->data['_id']) ),$array);*/
		}else{
			$this->oper->insert($array);
		}
		$this->items = $this->oper->findOne($array);
	}
	protected function save_new_asig(){
		global $f;
		$array = $this->params['data'];
		unset($array['cuenta_cobrar']);
		$array['asignacion'] = true;
		$array['estado'] = 'A';
		$array['fecreg'] = new MongoDate();
		$array['ocupante']['_id'] = new MongoId($array['ocupante']['_id']);
		$array['propietario']['_id'] = new MongoId($array['propietario']['_id']);
		$array['espacio']['_id'] = new MongoId($array['espacio']['_id']);
		$array['trabajador'] = $f->session->userDB;
		$this->oper->insert($array);
		$this->items = $this->oper->findOne($array);
	}
	protected function save_new_adju(){
		global $f;
		$array = $this->params['data'];
		$array['estado'] = 'A';
		$array['ocupante']['_id'] = new MongoId($array['ocupante']['_id']);
		$array['propietario']['_id'] = new MongoId($array['propietario']['_id']);
		$array['espacio']['_id'] = new MongoId($array['espacio']['_id']);
		$this->oper->insert($array);
	}
	protected function save_new_tras(){
		global $f;
		$array = $this->params['data'];
		unset($array['cuenta_cobrar']);
		$array['ocupante']['_id'] = new MongoId($array['ocupante']['_id']);
		$array['propietario']['_id'] = new MongoId($array['propietario']['_id']);
		$array['espacio']['_id'] = new MongoId($array['espacio']['_id']);
		$this->oper->insert($array);
		$this->items = $this->oper->findOne($array);
	}
	protected function save_traspaso(){
		global $f;
		$array = $this->params['data'];
		unset($array['cuenta_cobrar']);
		$array['estado'] = 'A';
		if(isset($array['ocupante'])) $array['ocupante']['_id'] = new MongoId($array['ocupante']['_id']);
		$array['propietario']['_id'] = new MongoId($array['propietario']['_id']);
		$array['espacio']['_id'] = new MongoId($array['espacio']['_id']);
		$array['traspaso']['nuevo_propietario']['_id'] = new MongoId($array['traspaso']['nuevo_propietario']['_id']);
		$this->oper->insert($array);
		$this->items = $this->oper->findOne($array);
	}
	protected function save_anul_conce(){
		global $f;
		$this->oper->update( array(
			'espacio._id'=>new MongoId($this->params['espacio']),
			'estado'=>'A'
		) , array('$set'=>array(
			'estado'=>'F',
			'anulacion'=>array(
				'fecanl'=>new MongoDate(),
				'observ'=>$this->params['observ']
			)
		)) );
	}
	protected function save_operacion(){
		global $f;
		$array['estado'] = 'A';
		$this->params['data']['estado'] = 'A';
		$this->oper->insert( $this->params['data'] );
		$this->items = $this->params['data'];
	}
	protected function save_ejecutar(){
		global $f;
		$array = $this->params['data'];
		$ejecu = array("ejecucion"=>array(
			"fecini"=>new MongoDate(strtotime($array['fecini'])),
			"fecfin"=>new MongoDate(strtotime($array['fecfin'])),
			"observ"=>$array['observ'],
			"trabajador"=>$f->session->userDB
		));
		$this->oper->update(array( '_id' => new MongoId($array['_id']) ),array('$set'=>$ejecu));
	}
	protected function delete_datos(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->oper->remove($this->items);
	}
	protected function get_search(){
		global $f;
		$parametro=$f->request->nomb;
		$criteria = array(
    		'nomb' => new MongoRegex('/^'.$parametro.'/i')
  		);
		$data = $this->oper->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->limit( $f->request->page_rows );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_all(){
		global $f;
		$data = $this->oper->find();
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function save_datos_cons(){
		global $f;
		$array = $f->request->data;
		unset($array['cuenta_cobrar']);
		$array['estado'] = 'A';
		$array['fecreg'] = new MongoDate();
		$array['espacio']['_id'] = new MongoId($array['espacio']['_id']);
		$array['propietario']['_id'] = new MongoId($array['propietario']['_id']);
		$array['trabajador'] = $f->session->userDB;
		$tmp = strtotime($array['construccion']['fecven']);
		$array['construccion']['fecven'] = new MongoDate($tmp);
		$array['programacion']['fecprog'] = new MongoDate($tmp);
		$tmp = strtotime($array['construccion']['fecdec']);
		$array['construccion']['fecdec'] = new MongoDate($tmp);
		$this->oper->insert($array);
		//$this->items = $this->oper->findOne($array,array('_id'=>true,'nomb'=>true));
		$this->items = $array;
	}
	protected function save_datos_amp(){
		global $f;
		$array = $f->request->data;
		unset($array['cuenta_cobrar']);
		$array['estado'] = 'A';
		$array['fecreg'] = new MongoDate();
		$array['espacio']['_id'] = new MongoId($array['espacio']['_id']);
		$array['propietario']['_id'] = new MongoId($array['propietario']['_id']);
		$array['trabajador'] = $f->session->userDB;
		$tmp = strtotime($array['ampliacion']['fecven']);
		$array['ampliacion']['fecven'] = new MongoDate($tmp);
		$array['programacion']['fecprog'] = new MongoDate($tmp);
		$tmp = strtotime($array['ampliacion']['fecdec']);
		$array['ampliacion']['fecdec'] = new MongoDate($tmp);
		$this->oper->insert($array);
		//$this->items = $this->oper->findOne($array,array('_id'=>true,'nomb'=>true));
		$this->items = $array;
	}
	protected function save_exe_cons(){
		global $f;
		$trabajador = $f->session->userDB;
		$this->oper->update( array('_id'=>new MongoId($this->params['_id'])) , array(
			'$set'=>array(
				'ejecucion'=>array(
					'fecini'=>new MongoDate(strtotime($this->params['fecini'])),
					'fecfin'=>new MongoDate(),
					'trabajador'=>$trabajador,
					'recibido'=>$this->params['recibido'],
					'observ'=>$this->params['observ']
				),
				'construccion.finalizacion'=>array(
					'capacidad'=>$this->params['capacidad'],
					'tipo'=>$this->params['tipo'],
					'largo'=>$this->params['largo'],
					'ancho'=>$this->params['ancho'],
					'altura1'=>$this->params['altura1'],
					'altura2'=>$this->params['altura2'],
				)
			)
		) );
		$this->db->cm_espacios->update( array('_id'=>new MongoId($this->params['espacio'])) , array(
			'$set'=>array(
				'mausoleo.tipo'=>$this->params['tipo'],
				'capacidad'=>$this->params['capacidad'],
				'mausoleo.medidas.largo'=>$this->params['largo'],
				'mausoleo.medidas.ancho'=>$this->params['ancho'],
				'mausoleo.medidas.altura1'=>$this->params['altura1'],
				'mausoleo.medidas.altura2'=>$this->params['altura2'],
			)
		) );
	}
	protected function save_exe_amp(){
		global $f;
		$trabajador = $f->session->userDB;
		$this->oper->update( array('_id'=>new MongoId($this->params['_id'])) , array(
			'$set'=>array(
				'ejecucion'=>array(
					'fecini'=>new MongoDate(strtotime($this->params['fecini'])),
					'fecfin'=>new MongoDate(),
					'trabajador'=>$trabajador,
					'recibido'=>$this->params['recibido'],
					'observ'=>$this->params['observ']
				),
				'ampliacion.finalizacion'=>array(
					'capacidad'=>$this->params['capacidad'],
					'tipo'=>$this->params['tipo'],
					'largo'=>$this->params['largo'],
					'ancho'=>$this->params['ancho'],
					'altura1'=>$this->params['altura1'],
					'altura2'=>$this->params['altura2'],
				)
			)
		) );
		$this->db->cm_espacios->update( array('_id'=>new MongoId($this->params['espacio'])) , array(
			'$set'=>array(
				'mausoleo.tipo'=>$this->params['tipo'],
				'capacidad'=>$this->params['capacidad'],
				'mausoleo.medidas.largo'=>$this->params['largo'],
				'mausoleo.medidas.ancho'=>$this->params['ancho'],
				'mausoleo.medidas.altura1'=>$this->params['altura1'],
				'mausoleo.medidas.altura2'=>$this->params['altura2'],
			)
		) );
	}
	protected function get_one(){
		global $f;
		$this->items = $this->oper->findOne( array('_id'=>new MongoId($this->params['_id'])));
	}
	protected function get_one2(){
		global $f;
		$this->items = $this->oper->findOne( array('_id'=>$this->params['_id']));
	}
	protected function get_ocup(){
		global $f;
		$data = $this->oper->find( array('ocupante._id'=>new MongoId($this->params['_id'])))->sort(array('_id'=>-1));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_prop(){
		global $f;
		$data = $this->oper->find( array('propietario._id'=>new MongoId($this->params['_id'])))->sort(array('_id'=>-1));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_concesion(){
		global $f;
		$cursor = $this->oper->find( array('espacio._id'=>$this->params['_id'],'concesion'=>array('$exists'=>true)))->sort(array('_id'=>-1))->limit(1);
		foreach ($cursor as $obj) {
		    $this->items = $obj;
		}
	}
	protected function get_concesiones(){
		global $f;
		$cursor = $this->oper->find( array('espacio._id'=>$this->params['_id'],'concesion'=>array('$exists'=>true)))->sort(array('_id'=>-1));
		foreach ($cursor as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_concesiones_periodo(){
		global $f;
		$ini = new MongoDate(date("d-m-Y", mktime(0, 0, 0,$this->params['mes'], 1,$this->params['ano'])));
		$fin = new MongoDate(date("d-m-Y", mktime(0, 0, 0,$this->params['mes'], 0,$this->params['ano'])));


		$fin_ano = intval($this->params['ano']);
		$fin_mes = intval($this->params['mes'])+1;
		if($fin_mes==13) $fin_ano++;
		if($fin_mes<10) $fin_mes = '0'.$fin_mes;
		$fin_mes .= '';




		$ini = new MongoDate(strtotime($this->params['ano']."-".$this->params['mes']."-01 -1 hour"));
		$fin = new MongoDate(strtotime($fin_ano."-".$fin_mes."-01 -1 hour"));




		$cursor = $this->oper->find(array(
			'concesion'=>array('$exists'=>true),
			'fecreg'=>array(
				'$gt'=>$ini,
				'$lt'=>$fin
			)
		))->sort(array('_id'=>-1));
		foreach ($cursor as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_last_asignacion(){
		global $f;
		$cursor = $this->oper->find( array('ocupante._id'=>$this->params['_id'],'asignacion'=>array('$exists'=>true)))->sort(array('_id'=>-1))->limit(1);
		foreach ($cursor as $obj) {
		    $this->items = $obj;
		}
	}
	protected function get_inhumaciones(){
		global $f;
		$ini = new MongoDate(mktime(0, 0, 0,$this->params['mes'], 1,$this->params['ano']));
		$fin = new MongoDate(mktime(0, 0, 0,intval($this->params['mes'])+1, 0,$this->params['ano']));

		$fin_ano = intval($this->params['ano']);
		$fin_mes = intval($this->params['mes'])+1;
		if($fin_mes==13){
			$fin_ano++;
			$fin_mes = 1;
		}
		if($fin_mes<10) $fin_mes = '0'.$fin_mes;
		$fin_mes .= '';




		$ini = new MongoDate(strtotime($this->params['ano']."-".$this->params['mes']."-01 -1 hour"));
		$fin = new MongoDate(strtotime($fin_ano."-".$fin_mes."-01 -1 hour"));
		//echo date('Y-m-d h:i:s', $fin->sec);die();
		$cursor = $this->oper->find(array(
			'anulacion'=>array('$exists'=>false),
			'inhumacion'=>array('$exists'=>true),
			'programacion.fecprog'=>array(
				'$gte'=>$ini,
				'$lte'=>$fin
			)
		))->sort(array('programacion.fecprog'=>-1));
		foreach ($cursor as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_last_inhumacion(){
		global $f;
		$cursor = $this->oper->find( array('ocupante._id'=>$this->params['_id'],'inhumacion'=>array('$exists'=>true)))->sort(array('_id'=>-1))->limit(1);
		foreach ($cursor as $obj) {
		    $this->items = $obj;
		}
	}
	protected function get_last_traslado(){
		global $f;
		$cursor = $this->oper->find( array('ocupante._id'=>$this->params['_id'],'traslado'=>array('$exists'=>true)))->sort(array('_id'=>-1))->limit(1);
		foreach ($cursor as $obj) {
		    $this->items = $obj;
		}
	}
	protected function get_construccion(){
		global $f;
		$cursor = $this->oper->find( array('espacio._id'=>$this->params['_id'],'construccion'=>array('$exists'=>true)))->sort(array('fecreg'=>-1))->limit(1);
		foreach ($cursor as $obj) {
		    $this->items = $obj;
		}
	}
	protected function save_anul_asig(){
		global $f;
		$this->oper->update(
			array('ocupante._id'=>new MongoId($this->params['ocupante']),'asignacion'=>true),
			array('$set'=>array(
				'asignacion'=>array(
					'anulacion'=>array(
						'fecanl'=>new MongoDate(),
						'observ'=>$this->params['observ']
					)
				)
			))
		);
	}
	protected function save_upd_trasp_conce(){
		global $f;
		$conce = $this->oper->findOne(array(
			'espacio._id'=>new MongoId($this->params['espacio']['_id']),
			'concesion.estado'=>'A'
		));
		$this->oper->update(
			array('_id'=>$conce['_id']),
			array('$set'=>array(
				'concesion.estado'=>'F'
			))
		);
		$array = array();
		$array['estado'] = 'A';
		$array['fecreg'] = new MongoDate();
		$array['propietario'] = $this->params['new_propietario'];
		$array['propietario']['_id'] = new MongoId($array['propietario']['_id']);
		$array['espacio'] = $this->params['espacio'];
		$array['espacio']['_id'] = new MongoId($array['espacio']['_id']);
		$array['trabajador'] = $f->session->userDB;
		$array['concesion']['condicion'] = $conce['concesion']['condicion'];
		if($conce['concesion']['condicion']=="T"){
			$array['concesion']['fecven'] = $conce['concesion']['fecven'];
		}
		$array['concesion']['estado'] = 'A';
		$this->oper->insert($array);
	}
	protected function save_custom(){
		global $f;
		$this->oper->update( array('_id'=>$this->params['_id']) , $this->params['data'] );
	}
	protected function save_filter(){
		global $f;
		$this->oper->update( $this->params['filter'] , $this->params['data'] );
	}
	protected function get_all_conc(){
		global $f;
		$ini = new MongoDate(strtotime($this->params['ano']."-".$this->params['mes']."-01"));
		$fin = new MongoDate(strtotime($this->params['ano']."-".(floatval($this->params['mes'])+1)."-01"));
		$data = $this->oper->find(array(
			"concesion"=>array('$exists'=>true),
			"ocupante_anterior"=>array('$exists'=>false),
			"fecreg"=>array(
					'$gte'=>$ini,
					'$lt'=>$fin
			)
		));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_all_tras(){
		global $f;
		$ini = new MongoDate(strtotime($this->params['ano']."-".$this->params['mes']."-01"));
		$fin = new MongoDate(strtotime($this->params['ano']."-".(floatval($this->params['mes'])+1)."-01"));
		$data = $this->oper->find(array(
								"traslado"=>array('$exists'=>true),
								"ejecucion.fecfin"=>array('$exists'=>true),
								"fecreg"=>array(
										'$gte'=>$ini,
										'$lt'=>$fin
								)
		));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_all_oper(){
		global $f;
		$data = $this->oper->find(array(
								"concesion"=>array('$exists'=>true),
								"cuentas_cobrar"=>array('$exists'=>true)
		));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
}
?>
