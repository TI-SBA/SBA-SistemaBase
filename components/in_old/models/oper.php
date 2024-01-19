<?php
class Model_in_oper extends Model {
	private $oper;
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$m = new Mongo('localhost');
		$this->db = $m->beneficencia;
		$this->oper = $this->db->in_operaciones;
		$this->days_rec = 30;
	}
	protected function get_one(){
		global $f;
		$this->items = $this->oper->findOne( array('_id'=>$this->params['_id']));
	}
	protected function get_search(){
		global $f;
		if($this->params["text"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["text"];
			$criteria = $helper->paramsSearch($this->params["text"], array(
				'arrendatario.nomb','arrendatario.appat','arrendatario.apmat',
				'espacio.ubic.local.nomb','espacio.ubic.local.direc','espacio.descr',
				'arrendamiento.contrato'
			));
		}else $criteria = array();
		$criteria['arrendamiento'] = array('$exists'=>true);
		if(isset($this->params['estado']))
			$criteria['estado'] = $this->params['estado'];
		if(isset($this->params['oper']))
			$criteria[$this->params['oper']] = array('$exists'=>true);
		$cursor = $this->oper->find($criteria)->sort( array('espacio.descr'=>1,'fecreg'=>-1) )->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_lista_arre_pen(){
		global $f;
		if(isset($f->request->data['filter']))
			$sort = array ( $f->request->data['filter'] => intval($f->request->data['order']));
		else
			$sort = array ( "fecreg" => -1);
		$filter = array(
			'arrendamiento.estado'=>'P'
		);
		$fields = array();
		$cursor = $this->oper->find($filter,$fields)->sort($sort)->skip($this->params["page_rows"]*($this->params["page"]-1))->limit($this->params["page_rows"]);
		foreach ($cursor as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_lista_arre_rec(){
		global $f;
		if(isset($f->request->data['filter']))
			$sort = array ( $f->request->data['filter'] => intval($f->request->data['order']));
		else
			$sort = array ( "fecreg" => -1);
		$now = date("Y-m-d");
		/*$fecini = new MongoDate($now);
		$fecfin = new MongoDate(strtotime($f->request->data['fecbus'].' + '.$this->days_rec.' days'));*/
		//$fecfin = new MongoDate($now);
		$fecfin = new MongoDate(strtotime($now.' + 1 days'));
		$fecini = new MongoDate(strtotime($now.' - '.$this->days_rec.' days'));
		$filter = array(
			'arrendamiento.estado'=>'A',
			'fecreg'=>array('$gte'=>$fecini,'$lt'=>$fecfin)
		);
		$fields = array(  );
		$cursor = $this->oper->find( $filter , $fields )->sort($sort)->skip( $f->request->page_rows * ($f->request->page-1) )->limit( $f->request->page_rows );
		foreach ($cursor as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_lista_arre_ven(){
		global $f;
		if(isset($f->request->data['filter']))
			$sort = array ( $f->request->data['filter'] => intval($f->request->data['order']));
		else
			$sort = array ( "fecreg" => -1);
		$now = date("Y-m-d");
		$fecfin = new MongoDate(strtotime($now));
		$filter = array(
			'arrendamiento.estado'=>'A',
			'arrendamiento.fecven'=>array('$lte'=>$fecfin)
		);
		$fields = array(  );
		$cursor = $this->oper->find( $filter , $fields )->sort($sort)->skip( $f->request->page_rows * ($f->request->page-1) )->limit( $f->request->page_rows );
		foreach ($cursor as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_lista_arre_all(){
		global $f;
		if(isset($f->request->data['filter']))
			$sort = array ( $f->request->data['filter'] => intval($f->request->data['order']));
		else
			$sort = array ( "fecreg" => -1);
		$filter = array(
			'arrendamiento'=>array('$exists'=>true)
		);
		$fields = array(  );
		$cursor = $this->oper->find( $filter , $fields )->sort($sort)->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_lista_arre_por(){
		global $f;
		if(isset($f->request->data['filter']))
			$sort = array ( $f->request->data['filter'] => intval($f->request->data['order']));
		else
			$sort = array ( "fecreg" => -1);
		$now = date("Y-m-d");
		$fecini = new MongoDate(strtotime($now));
		$fecfin = new MongoDate(strtotime($now.' + '.$this->days_rec.' days'));
		$filter = array(
			'arrendamiento.estado'=>'A',
			'arrendamiento.fecven'=>array('$lte'=>$fecfin,'$gte'=>$fecini)
		);
		$fields = array(  );
		$cursor = $this->oper->find( $filter , $fields )->sort($sort)->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->limit( $this->params["page_rows"] );
		foreach ($cursor as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_arre_nuev(){
		global $f;
		$sort = array ( "fecreg" => 1);
		$now = date("Y-m-d");//Y-m-d format
		$fecfin = new MongoDate(strtotime($now));
		$fecini = new MongoDate(strtotime($this->params["feccon"]));
		$filter = array(
			'arrendamiento.estado'=>'A',
			'arrendamiento.fecven'=>array('$lte'=>$fecfin,'$gte'=>$fecini)
		);
		$fields = array(  );
		$cursor = $this->oper->find( $filter , $fields )->sort($sort);
		foreach ($cursor as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function get_rentven(){
		global $f;
		$now = date("Y-m-d");
		//$fecini = new MongoDate(strtotime($now.' - '.$this->days_ven.' days'));
		$fecfin = new MongoDate(strtotime($now));
		$ordenacion = array('espacio.descr'=>1);
		$criteria = array(
			'arrendamiento.estado'=>$this->params['estado'],
			'arrendamiento.rentas' => array('$elemMatch'=>array(
				//'fecpago' => array('$gte'=>$fecini,'$lt'=>$fecfin),
				'fecpago' => array('$lt'=>$fecfin),
				'estado'=>'CR'/*,
				'letra'=>array('$ne'=>'')*/
			))
		);
		$fields = array("arrendatario"=>true,"espacio"=>true,"arrendamiento"=>true);
		$data = $this->oper->find($criteria,$fields)->sort($ordenacion);
		foreach ($data as $ob) {
	    	$this->items[] = $ob;
		}
	}
	protected function get_listarentven(){
		global $f;
		$now = date("Y-m-d");
		//$fecini = new MongoDate(strtotime($now.' - '.$this->days_ven.' days'));
		$fecfin = new MongoDate(strtotime($now));
		$ordenacion = array('_id'=>1);
		$criteria = array(
			//'arrendamiento.estado'=>'A',
			'arrendamiento.contrato'=>array('$ne'=>''),
			'arrendamiento.rentas' => array('$elemMatch'=>array(
				//'fecpago' => array('$gte'=>$fecini,'$lt'=>$fecfin),
				'fecpago' => array('$lt'=>$fecfin),
				'estado'=>'CR'/*,
				'letra'=>array('$ne'=>'')*/
			))
		);
		$fields = array("arrendatario"=>true,"espacio"=>true,"arrendamiento.rentas"=>true);
		$data = $this->oper->find($criteria,$fields)->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->sort($ordenacion)->limit( $this->params["page_rows"] );
		foreach ($data as $ob) {
	    	$this->items[] = $ob;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_listarentpro(){
		global $f;
		$ordenacion = array('_id'=>1);
		$criteria = array(
			//'arrendamiento.estado'=>'A',
			'arrendamiento.rentas' => array('$elemMatch'=>array(
				'estado'=>'PT'/*,
				'letra'=>array('$ne'=>'')*/
			))
		);
		$fields = array("arrendatario"=>true,"espacio"=>true,"arrendamiento.rentas"=>true);
		$data = $this->oper->find($criteria,$fields)->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->sort($ordenacion)->limit( $this->params["page_rows"] );
		foreach ($data as $ob) {
	    	$this->items[] = $ob;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_listarentall(){
		global $f;
		$ordenacion = array('_id'=>1);
		$criteria = array(
			'arrendamiento'=>array('$exists'=>true),
		);
		$fields = array("arrendatario"=>true,"espacio"=>true,"arrendamiento.rentas"=>true);
		$data = $this->oper->find($criteria,$fields)->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->sort($ordenacion)->limit( $this->params["page_rows"] );
		foreach ($data as $ob) {
	    	$this->items[] = $ob;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_arrenprop(){
		global $f;
		$criteria = array(
			'arrendamiento'=>array('$exists'=>true),
			'arrendatario._id'=>$this->params['arrendatario']
		);
		$fields = array("espacio.descr"=>true,"arrendamiento.estado"=>true,"arrendamiento.tipo"=>true,"arrendamiento.fecocu"=>true,"arrendamiento.desocupacion.fecdes"=>true);
		$data = $this->oper->find($criteria,$fields)->sort(array('fecreg'=>-1));
		foreach ($data as $ob) {
	    	$this->items[] = $ob;
		}
	}
	protected function get_custom_data(){
		global $f;
		if(!isset($this->params['fields']))
			$this->params['fields'] = array();
		if(!isset($this->params['sort']))
			$this->params['sort'] = array('_id'=>1);
		$cursor = $this->oper->find($this->params['filter'],$this->params['fields'])->sort( $this->params['sort'] );
		foreach ($cursor as $obj) {
			$this->items[] = $obj;
		}
	}
	protected function get_arrendamiento(){
		global $f;
		$this->items = $this->oper->findOne(array(
			'arrendatario._id'=>$this->params['arrendatario'],
			'espacio._id'=>$this->params['espacio'],
			'arrendamiento.estado'=>'A'
		));
	}
	protected function get_rent_ven_sin_cont(){
		global $f;
		$now = date("Y-m-d");
		//$fecini = new MongoDate(strtotime($now.' - '.$this->days_ven.' days'));
		$fecfin = new MongoDate(strtotime($now));
		$order = array('espacio.local._id'=>1);
		$criteria = array(
			'arrendamiento.estado'=>'F',
			'arrendamiento.rentas' => array('$elemMatch'=>array(
				//'fecpago' => array('$gte'=>$fecini,'$lt'=>$fecfin),
				'fecpago' => array('$lt'=>$fecfin),
				'estado'=>'CR'/*,
				'letra'=>array('$ne'=>'')*/
			))
		);
		$data = $this->oper->find($criteria)->sort($order);
		foreach ($data as $ob) {
	    	$this->items[] = $ob;
		}
	}
	protected function get_rent_ven_con_cont(){
		global $f;
		$now = date("Y-m-d");
		//$fecini = new MongoDate(strtotime($now.' - '.$this->days_ven.' days'));
		$fecfin = new MongoDate(strtotime($now));
		$order = array('espacio.local._id'=>1);
		$criteria = array(
			'arrendamiento.estado'=>'A',
			'arrendamiento.rentas' => array('$elemMatch'=>array(
				//'fecpago' => array('$gte'=>$fecini,'$lt'=>$fecfin),
				'fecpago' => array('$lt'=>$fecfin),
				'estado'=>'CR'/*,
				'letra'=>array('$ne'=>'')*/
			))
		);
		$data = $this->oper->find($criteria)->sort($order);
		foreach ($data as $ob) {
	    	$this->items[] = $ob;
		}
	}
	protected function get_liquidaciones(){
		global $f;
		$criteria = array(
			'arrendatario._id'=>$this->params["arren"]
		);
		$order = array();
		$data = $this->oper->find($criteria)->sort(array('fecreg'=>1));
		foreach ($data as $ob) {
	    	$this->items[] = $ob;
		}
	}
	protected function get_all_filter(){
		global $f;
		$criteria = array();
		if(isset($this->params["espa"]))$criteria["espacio._id"] = $this->params["espa"];
		if(isset($this->params["arren"])){
			$criteria["arrendatario._id"] = $this->params["arren"];
		}
		$order = array();
		$data = $this->oper->find($criteria)->sort(array('fecreg'=>1));
		foreach ($data as $ob) {
	    	$this->items[] = $ob;
		}
	}
	protected function get_custom(){
		global $f;
		$data = $this->oper->find($this->params['filter']);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function save_oper(){
		global $f;
		$this->oper->insert( $this->params['data'] );
		$this->obj = $this->params['data'];
	}
	protected function save_upd(){
		global $f;
		$this->oper->update( array('_id'=>$this->params['_id']) , $this->params['data'] );
		$this->params['data']['_id'] = $this->params['_id'];
		$this->obj = $this->params['data'];
	}
	protected function save_upd_custom(){
		global $f;
		$this->oper->update( $this->params['filter'] , $this->params['data'] );
	}
}
?>