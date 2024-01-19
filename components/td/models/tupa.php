<?php
class Model_td_tupa extends Model {
	private $tupa;
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore;
		$this->tupa = $this->db->td_tupa;
	}
	protected function get_alltupas(){
		global $f;
		$i = 0;
		$parametros=array();
		if($f->session->tasks["td.tupa.ant"]=="0"){
		$parametros = array("estado"=>"V");
		}
		$cursor = $this->tupa->find( $parametros,array('_id'=>true,'anio' => true, 'estado' =>true))->sort(array ( 'estado' => 1));
		foreach ($cursor as $obj) {
		    $this->items[$i] = $obj;
			$i++;
		}
	}
	protected function get_lista(){
		global $f;
		$i = 0;
		$cursor = $this->tupa->find( array( '_id' => new MongoId($this->params['tupa']) ) , array('procedimientos.estado'=>true,'procedimientos._id'=>true,'procedimientos.item'=>true,'procedimientos.titulo' => true,'procedimientos.organizacion' => true,'procedimientos.modalidades.descr' => true,'procedimientos.modalidades.plazo' => true,'procedimientos.modalidades.reqs' => true,'procedimientos.modalidades.aprueba' => true) );
		foreach ($cursor as $obj) {
			if(isset($obj['procedimientos']))
			foreach($obj['procedimientos'] as $proc){
				$this->items[$i] = $proc;
				$i++;
			}
		}
	}
	protected function get_vigente(){
		global $f;$like=array();
		$cursor = $this->tupa->find( array( 'estado' => 'V' ) );
		$proses=array();		
	
		foreach ($cursor as $obj) {
			$proses['_id']=$obj['_id'];
			$proses['anio']=$obj['anio'];
			$proses['estado']=$obj['estado'];
			$proses['procedimientos']=array();			
			if(isset($this->params['textSearch'])){
				$text='';
				$palabras = explode(' ', $this->params['textSearch']);
				$tot_pal=0;
				foreach ($palabras as $palabra) {
					if($palabra!=''){
						$text.=$palabra.'|';
						$tot_pal++;
					}
				}
				$text=substr($text,0,-1);
				$expreg='/('.$text.')/i';
				foreach ($obj['procedimientos'] as $pros) {
					if($pros['estado']=='H'){
					//	if(stristr($pros['titulo'], $this->params['textSearch'])){
						preg_match_all($expreg, $pros['titulo'], $resp);					
						if($tot_pal==count($resp[0])){
							array_push($proses['procedimientos'], $pros);
						}
					}
				}
				$this->items = $proses;
			}else{
				$this->items = $obj;
			}			
		}
	}
	protected function get_search(){
		global $f;
		$i = 0;
		$parametro=$this->params["texto"];
		$criteria = array(
    		'nomb' => new MongoRegex('/^'.$parametro.'/i')
  		);
		$cursor = $this->tupa->find( $criteria )->skip( $this->params['page_rows'] * ($this->params['page']-1) )->limit( $this->params['page_rows'] );
		foreach ($cursor as $obj) {
		    $this->items[$i] = $obj;
			$i++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$cursor->count());
	}
	protected function get_one(){
		global $f;
		$tupa = $this->tupa->findOne( array('procedimientos._id'=>new MongoId($this->params['_id'])) , array('procedimientos'=>1) );
		foreach($tupa['procedimientos'] as $obj){
			if($obj['_id']==$this->params['_id'])
				$this->items = $obj;
		}
	}
	protected function save_create(){
		global $f;
		$this->tupa->update( array() , array('$set'=>array('estado'=>'I')) );
		$array = (array)$this->params['data'];
		$this->tupa->insert($array);
	}
	protected function save_datos(){
		global $f;
		$array = (array)$f->request->data['data'];
		$array['organizacion']['_id'] = new MongoId($array['organizacion']['_id']);
		foreach($array['modalidades'] as $i=>$obj) {
			if(isset($this->params['_id'])) $array['modalidades'][$i]['_id'] = new MongoId($obj['_id']);
			else $array['modalidades'][$i]['_id'] = new MongoId();
			$array['modalidades'][$i]['inicia']['organismo']['_id'] = new MongoId($obj['inicia']['organismo']['_id']);
			$array['modalidades'][$i]['aprueba']['organismo']['_id'] = new MongoId($obj['aprueba']['organismo']['_id']);
			if($obj['reconsidera']!=null) $array['modalidades'][$i]['reconsidera']['organismo']['_id'] = new MongoId($obj['reconsidera']['organismo']['_id']);
			if($obj['apela']!=null) $array['modalidades'][$i]['apela']['organismo']['_id'] = new MongoId($obj['apela']['organismo']['_id']);
		}
		if(isset($this->params['_id'])){
			$array['_id'] = new MongoId($this->params['_id']);
			$this->tupa->update( array('procedimientos._id'=>new MongoId($this->params['_id'])) , array( '$set' => array( 'procedimientos.$' => $array )  ) );
		}else{
			$array['_id'] = new MongoId();
			$this->tupa->update( array('_id'=>new MongoId($this->params['tupa'])) , array( '$push' => array( 'procedimientos' => $array )  ) );
		}
	}
	protected function save_push(){
		global $f;
		$this->tupa->update( array('_id'=>$this->params['tupa']) , array( '$push' => array( 'procedimientos' => $this->params['data'] )  ) );
	}
	protected function save_set(){
		global $f;
		$this->tupa->update(array('procedimientos._id'=>$this->params['_id']),array('$set'=>array('procedimientos.$'=>$this->params['data'])));
	}
	protected function save_estado(){
		global $f;
		$this->tupa->update(array('procedimientos._id'=>$this->params['_id']),array('$set'=>array('procedimientos.$.estado'=>$this->params['estado'])));
	}
	protected function delete_procedimiento(){
		global $f;
		$this->tupa->update( array('procedimientos._id'=>new MongoId($this->params['_id'])) , array( '$pull' => array( 'procedimientos' => array('_id'=>new MongoId($this->params['_id'])) )  ) );
	}
	protected function delete_tupa(){
		global $f;
		$this->items = array(
		    '_id' => new MongoId($f->request->id),
		);
		$this->tupa->remove($this->items);
	}
	public function get_search_all(){
		global $f;
		if($this->params["texto"]!=''){
			$tupa = $this->tupa->findOne( array("estado"=>"V") , array('procedimientos'=>1) );
			$i=0;
			foreach($tupa['procedimientos'] as $obj){
				similar_text(strtoupper($f->request->data["texto"]), strtoupper($obj['titulo']), $similarity_pst); 
				if(preg_match('/'.$this->normaliza($f->request->data["texto"]).'/',$this->normaliza($obj['titulo']))){ 
					//strpos();
					$this->items[$i] = $obj;
					$this->items[$i]["titulo"] = $this->resaltar($f->request->data["texto"],$obj["titulo"]);
					$i++;
				}
			}
		}else{
			$tupa = $this->tupa->findOne( array("estado"=>"V") , array('procedimientos'=>1) );
			foreach($tupa['procedimientos'] as $obj){
				$this->items[] = $obj;
			}
		}
	}
	private function normaliza ($cadena){
   		$originales = 'Ã€Ã�Ã‚ÃƒÃ„Ã…Ã†Ã‡ÃˆÃ‰ÃŠÃ‹ÃŒÃ�ÃŽÃ�Ã�Ã‘Ã’Ã“Ã”Ã•Ã–Ã˜Ã™ÃšÃ›ÃœÃ�Ãž
		ÃŸÃ Ã¡Ã¢Ã£Ã¤Ã¥Ã¦Ã§Ã¨Ã©ÃªÃ«Ã¬Ã­Ã®Ã¯Ã°Ã±Ã²Ã³Ã´ÃµÃ¶Ã¸Ã¹ÃºÃ»Ã½Ã½Ã¾Ã¿Å”Å•';
    	$modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuy
		bsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
    	$cadena = utf8_decode($cadena);
    	$cadena = strtr($cadena, utf8_decode($originales), $modificadas);
    	$cadena = strtolower($cadena);
    	return utf8_encode($cadena);
	}
	private function resaltar($buscar, $texto) { 
    	$claves = explode(" ",$buscar); 
    	$clave = array_unique($claves);
    	$num = count($clave); 
    	for($i=0; $i < $num; $i++) 
        	$texto = preg_replace("/(".trim($clave[$i]).")/i","<strong>\\1</strong>",$texto);
    	return $texto; 
	} 
}
?>