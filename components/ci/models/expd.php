<?php
class Model_td_expd extends Model {
	private $expd;
	private $db;
	public $items;
	
	public function __construct() {
		global $f;
		$m = new Mongo('localhost');
		$this->db = $m->beneficencia;
		$this->expd = $this->db->td_expedientes;
	}
	protected function get_lista(){
		global $f;
  		$i = 0;
  		if(isset($f->request->data['filter']))
			$criteria = array ( $f->request->data['filter'] => intval($f->request->data['order']));
		else
			$criteria = array ( "_id" => 1);
		$data = $this->expd->find()->skip( $f->request->page_rows * ($f->request->page-1) )->sort($criteria)->limit( $f->request->page_rows );
		foreach ($data as $ob) {
		    $this->items[$i] = $ob;
		    $i++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_listaexpdpor(){
		global $f;
		$enti = $f->session->user['_id'];
		$orga = new MongoId("4f5675569c7684880700000c");//segun la entidad
  		$i = 0;
  		$criteria = array (
  			'traslados.destino.fecrec' => null,
  			'traslados.destino.organizacion._id' => $orga
  		);
		$data = $this->expd->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->limit( $f->request->page_rows );
		foreach ($data as $ob) {
		    $this->items[$i] = $ob;
		    $i++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_listaexpdreci(){
		global $f;
		$x = 0;
		$enti = $f->session->user['_id'];
		//print_r($_SESSION['entidad']);die();
		$data = $this->expd->find(array ('traslados.origen.entidad._id' => $enti))->skip( $f->request->page_rows * ($f->request->page-1) )->sort(array('fecreg'=>-1))->limit( $f->request->page_rows );
		foreach ($data as $ob) {
		    $temp[$i] = $ob;
		    $va = count($temp[$i]['traslados'])-1;
		    if(!isset($temp[$i]['traslados'][$va]['origen']['archivado'])){
		    	$this->items[$x] = $temp[$i];
		    	$x++;
		    }
		    $i++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$x);
	}
	protected function get_listaexpdvenc(){
		global $f;
		$enti = $f->session->user['_id'];
		$orga = "4f56754b9c7684600800000a"; //segun entidad
		$i = 0;
  		$x = 0;
  		$criteria = array (
  			'traslados.origen.organizacion._id' => new MongoId($orga)
  		);
  		$data = $this->expd->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->sort(array ( "fecven" => 1))->limit( $f->request->page_rows );
		foreach ($data as $ob) {
		    $temp[$i] = $ob;
		    $va = count($temp[$i]['traslados'])-1;
		    if(!isset($temp[$i]['traslados'][$va][destino])){
		    	$this->items[$x] = $temp[$i];
		    	$x++;
		    }
		    $i++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$x);
	}
	protected function get_listaexpdarch(){
		global $f;
		$enti = $f->session->user['_id'];
		$orga = "4f56754b9c7684600800000a"; //segun entidad
		$i = 0;
  		$x = 0;
  		$criteria = array (
  			'traslados.origen.organizacion._id' => new MongoId($orga)
  		);
  		$data = $this->expd->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->sort(array ( "fecven" => 1))->limit( $f->request->page_rows );
		foreach ($data as $ob) {
		    $temp[$i] = $ob;
		    $va = count($temp[$i]['traslados'])-1;
		    if(isset($temp[$i]['traslados'][$va]['origen']['archivado'])){
		    	$this->items[$x] = $temp[$i];
		    	$x++;
		    }
		    $i++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$x);
	}
	protected function get_listaexpdcopi(){
		global $f;
		$enti = $f->session->user['_id'];
		$orga = new MongoId("4f56755f9c7684800a000005"); //segun entidad
		
  		$x = 0;
  		$criteria = array (
  			'traslados.copias.organizacion._id' => $orga
  		);
  		$data = $this->expd->find($criteria,array('num'=>true,'gestor'=>true,'concepto'=>true,'traslados'=>true))->skip( $f->request->page_rows * ($f->request->page-1) )->sort(array ( "fecven" => 1))->limit( $f->request->page_rows );
		$i = 0;
  		foreach ($data as $ob) {
		    for($x = 0; $x<count($ob['traslados']);$x++){
		    	$y = 0;
		    	while ($y<count($ob['traslados'][$x]['copias'])){
			    	if($ob['traslados'][$x]['copias'][$y]['organizacion']['_id']==$orga){
			    		$this->items[$i]['_id'] = $ob['_id'];
			    		$this->items[$i]['num'] = $ob['num'];
				    	$this->items[$i]['gestor'] = $ob['gestor'];
				    	$this->items[$i]['concepto'] = $ob['concepto'];
					    $this->items[$i]['origen']['_id'] = $ob['traslados'][$x]['origen']['organizacion']['_id'];
					    $this->items[$i]['origen']['nomb'] = $ob['traslados'][$x]['origen']['organizacion']['nomb'];
					    $this->items[$i]['fecenv'] = $ob['traslados'][$x]['copias'][$y]['fecenv'];
					    $i++;
			    	}
			    	$y++;
		    	}
		    }
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$i);
	}
	protected function get_listaexpdrebi(){
		global $f;
		$enti = $f->session->user['_id'];
		$orga = "4f56754b9c7684600800000a"; //segun entidad
		$i = 0;
  		$x = 0;
  		$criteria = array (
  			'traslados.destino.organizacion._id' => new MongoId($orga)
  		);
  		$data = $this->expd->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->sort(array ( "fecven" => 1))->limit( $f->request->page_rows );
		foreach ($data as $ob) {
		    $total = count($ob['traslados']);
		    for($x=0; $x < $total; $x++){
		    	if($ob['traslados'][$x]['destino']['estado']=="Aceptado"){
			    	$this->items[$i] = $ob;
			    	$i++;
			    }
		    }
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$i);
	}
	protected function get_listaexpdenvi(){
		global $f;
		$enti = $f->session->user['_id'];
		$orga = "4f56754b9c7684600800000a"; //segun entidad
		$i = 0;
  		$x = 0;
  		$criteria = array (
  			'traslados.origen.organizacion._id' => new MongoId($orga)
  		);
  		$data = $this->expd->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->sort(array ( "fecven" => 1))->limit( $f->request->page_rows );
		foreach ($data as $ob) {
			if(isset($ob['traslados'][$x]['destino'])){
		    	$this->items[$i] = $ob;
		    	$i++;
		    }
		    $x++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$i);
	}
	protected function get_all(){
		global $f;
  		$i = 0;
		$data = $this->expd->find()->skip( $f->request->page_rows * ($f->request->page-1) )->limit( $f->request->page_rows );
		foreach ($data as $ob) {
		    $this->items[$i] = $ob;
		    $i++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_one(){
		global $f;
		$this->items = $this->expd->findOne(array('_id'=>new MongoId($f->request->_id)));
	}
	protected function save_datos(){
		global $f;
		$enti = $f->session->user['_id'];
		$orga = "4f56754b9c7684600800000a";
		//El array al parecer no guarda las fechas como se quiere.
		$array = (array)$f->request->data['data'];
		if(isset($f->request->data['_id'])){
			//$array['_id'] = new MongoId($f->request->data['_id']);
			//$this->tdoc->update(array( '_id' => new MongoId($f->request->data['_id']) ),$array);
		}else{
			$dat = $this->expd->find(array(),array("num"=>true))->sort(array("_id"=>-1))->limit(1);
			foreach ($dat as $ob) {
			    $tem =  split('-',$ob['num']);
			}
			if($tem[0]=="") $tem[0]="0000";
	  		$t = "1000"+$tem[0];
	  		$int = ($t+1)-1000;
		    for($i=0; $i<(strlen($tem[0])-strlen($int)); $i++)
		        $nulls .= '0';
			$array['num'] = $nulls.$int."-".date("Y");
			$array['fecreg'] = new MongoDate();
			$array['gestor']['_id'] = new MongoId($array['gestor']['_id']);
			$array['fecven'] = new MongoDate(time() + ((int)$f->request->dias * (24 * 60 * 60)));
			$array['estado'] = "Pendiente";
			$array['flujos']['iniciacion']['fecini'] = new MongoDate();
			//$array['ubicacion']['_id'] = new MongoId($array['gestor']['_id']);
			//$array['ubicacion']['nomb'] = "locacion";
			if(isset($array['documentos'])){
				$i = 0;
				foreach ($array['documentos'] as $obj){
					$array['documentos'][$i]['_id'] = new MongoId();
					$array['documentos'][$i]['flujo'] = "iniciacion";
					$array['documentos'][$i]['tipo_documento']['_id'] = new MongoId($obj['documentos'][$i]['tipo_documento']['_id']);
					//$array['origen']['_id'] = new MongoId("organizacion segun la entidad");
					//$array['origen']['nomb'] = "segun la entidad";
					$array['documentos'][$i]['fecreg'] = new MongoDate();
					$array['traslados'][0]['origen']['documentos'][$i]['_id'] = new MongoId($array['documentos'][$i]['_id']);
					$i++;
				}
			}
			$array['traslados'][0]['origen']['entidad']['_id'] = $f->session->user['_id'];
			$array['traslados'][0]['origen']['entidad']['nomb'] = $f->session->user['owner']['nomb'];
			if(isset($f->session->user['owner']['appat']))
				$array['traslados'][0]['origen']['entidad']['appat'] = $f->session->user['owner']['appat'];
			if(isset($f->session->user['owner']['apmat']))
				$array['traslados'][0]['origen']['entidad']['apmat'] = $f->session->user['owner']['apmat'];
			$array['traslados'][0]['origen']['organizacion']['_id'] = new MongoId($orga);
			$array['traslados'][0]['origen']['organizacion']['nomb'] = "OCIs";
			$array['traslados'][0]['origen']['fecreg'] = new MongoDate();
			//$array['traslados'][0]['origen']['organizacion']['sigla']
			//print_r($array);die();
			if(isset($array['tupa']['_id'])){
				$array['tupa']['_id']  = new MongoId($array['tupa']['_id']);
				$array['tupa']['procedimiento']['_id']  = new MongoId($array['tupa']['procedimiento']['_id']);
				$array['tupa']['procedimiento']['modalidad']['_id']  = new MongoId($array['tupa']['procedimiento']['modalidad']['_id']);
			}
			$this->expd->insert($array);
		}
	}
	protected function save_doc(){
		global $f;
		$array = (array)$f->request->data['data'];
		if(isset($f->request->data['data']['_id'])){
			print_r("no hay");die();
			//$array['_id'] = new MongoId($f->request->data['_id']);
			//$this->tdoc->update(array( '_id' => new MongoId($f->request->data['_id']) ),$array);
		}else{
			//$expd = $this->expd->findOne($f->request->data->_id);
			$array['tipo_documento']['_id'] = new MongoId($array['tipo_documento']['_id']);
			$array['organizacion']['_id'] = new MongoId("4f5675569c7684880700000c");
			$array['organizacion']['nomb'] = "Directorio de Presidencia";
			$array['fecreg'] = new MongoDate();
			$this->expd->update(
				array('_id'=>new MongoId($f->request->_id)) ,
				array( '$push' => array( 'documentos' => $array ))
			);
		}
	}
	protected function get_search(){
		global $f;
		$i = 0;
		$criteria = array ( $f->request->ncol => new MongoRegex('/^'.$f->request->nomb.'/i'));
		$data = $this->expd->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->limit( $f->request->page_rows );
		foreach ($data as $obj) {
		    $this->items[$i] = $obj;
			$i++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function save_expdsend(){
		global $f;
		$i = 0;
		$array = (array)$f->request->data['data'];
		$copias = (array)$f->request->data['copias'];
		//Organizacion Destino
		$array['organizacion']['_id'] = new MongoId($array['organizacion']['_id']);
		$ref = array('$ref' => 'mg_organizaciones','$id' => $array['organizacion']['_id']);
		$temp = $this->db->getDBRef($ref);
		$array['organizacion']['nomb'] = $temp['nomb'];
		$array['organizacion']['sigla'] = $temp['sigla'];
		$array['estado'] = "0";
		$array['fecenv'] = new MongoDate();
		$array['observ'] = " ";
		$va;
		$data = $this->expd->find(array ('_id' => new MongoId($f->request->expd)),array('traslados'=>true));
		foreach ($data as $ob) {
			$temp[$i] = $ob;
		    $va = count($temp[$i]['traslados'])-1;
		    $i++;
		}
		//Guarda Organizacion de Destino
		$this->expd->update(
			array('_id'=>new MongoId($f->request->expd)) ,
			array( '$set' => array( "traslados.$va.destino" => $array ))
		);
		//Guarda Organizaciones de Copias
		$i = 0;
		foreach ($copias as $obj){
			$co[$i]['organizacion']['_id'] = new MongoId($obj['id']);
			$co[$i]['organizacion']['nomb'] = $obj['nomb'];
			$co[$i]['fecenv'] = new MongoDate();
			$i++;
		}
		$this->expd->update(
			array('_id'=>new MongoId($f->request->expd)) ,
			array( '$set' => array( "traslados.$va.copias" => $co))
		);
	}
	protected function save_expdestado(){
		global $f;
		$i = 0;
		$va = 0;
		$enti = $f->session->user['_id'];
		$orga = "4f56754b9c7684600800000a";//segun la entidad
		$data = $this->expd->find(array('_id'=>new MongoId($f->request->id)),array('traslados.destino'=>true));
		foreach ($data as $ob) {
		    $temp[$i] = $ob;
		    $va = count($temp[$i]['traslados']);
		    $i++;
		}
		$va = $va - 1;
		//Solo Actualiza Estado
		if($f->request->fl=="0"){
			if($f->request->descr=="0"){
				$this->expd->update(
					array('_id'=>new MongoId($f->request->id)) ,
					array( '$set' => array( "traslados.$va.destino.estado" => $f->request->estado,"traslados.$va.destino.feccon"=>new MongoDate()))
				);
			}else{
				$this->expd->update(
					array('_id'=>new MongoId($f->request->id)) ,
					array( '$set' => array( "traslados.$va.destino.estado" => $f->request->estado,"traslados.$va.destino.observ" => $f->request->descr,"traslados.$va.destino.feccon"=>new MongoDate(),"traslados.$va.destino.fecrec"=>new MongoDate()))
				);
			}
		}
		else{
			$this->expd->update(
					array('_id'=>new MongoId($f->request->id)) ,
					array( '$set' => array( "traslados.$va.destino.fecrec"=>new MongoDate()))
				);
			//Crear Nuevo Origen porque se recepciono el expd fisico
			$array['origen']['entidad']['_id'] = new MongoId($enti);
			$ref = array('$ref' => 'mg_entidad','$id' => new MongoId($enti));
			$temp = $this->db->getDBRef($ref);
			$array['origen']['entidad']['nomb'] = $temp['nomb'];
			if(isset($temp['appat']))
				$array['origen']['entidad']['appat'] = $temp['appat'];
			if(isset($temp['apmat']))
				$array['origen']['entidad']['apmat'] = $temp['apmat'];
			$array['origen']['organizacion']['_id'] = new MongoId($orga);
			$array['origen']['organizacion']['nomb'] = "OCIs";
			$array['origen']['fecha'] = new MongoDate();
			$this->expd->update(
				array('_id'=>new MongoId($f->request->id)) ,
				array( '$push' => array( "traslados" => $array ))
			);
		}
	}
	protected function save_expdopen(){
		global $f;
		if($f->request->fl=="R"){
			//Reconsideracion
			$this->expd->update(
				array('_id'=>new MongoId($f->request->id)) ,
				array( '$set' => array( "flujos.reconsideracion.fecini" => new MongoDate(),"estado" =>"Pendiente","feccon" => null,"evaluacion"=>"","respuesta"=>""))
			);
		}else{
			//Apelacion
			$this->expd->update(
				array('_id'=>new MongoId($f->request->id)) ,
				array( '$set' => array( "flujos.apelacion.fecini" => new MongoDate(),"estado" =>"Pendiente","feccon" => null,"evaluacion"=>"","respuesta"=>""))
			);
		}
		
	}
	protected function save_expdarchivar(){
		global $f;
		$i = 0;
		$va = 0;
		//$enti = $f->session->user['_id'];
		//$orga = "4f56754b9c7684600800000a";//segun la entidad
		$data = $this->expd->find(array('_id'=>new MongoId($f->request->id)),array('traslados.origen'=>true));
		foreach ($data as $ob) {
		    $temp[$i] = $ob;
		    $va = count($temp[$i]['traslados'])-1;
		    $i++;
		}
		$this->expd->update(
			array('_id'=>new MongoId($f->request->id)) ,
			array( '$set' => array( "traslados.$va.origen.archivado" => true,"traslados.$va.origen.fecarc"=>new MongoDate()))
		);
	}
	protected function save_expdconcluir(){
		global $f;
		$i = 0;
		$va = 0;
		$flujo="inicacion";
		//$enti = $f->session->user['_id'];
		//$orga = "4f56754b9c7684600800000a";//segun la entidad
		if(isset($f->request->flujos->iniciacion)) $flujo="inicacion";
		if(isset($f->request->flujos->reconsideracion)) $flujo="reconsideracion";
		if(isset($f->request->flujos->apelacion)) $flujo="apelacion";
		$this->expd->update(
			array('_id'=>new MongoId($f->request->id)) ,
			array( '$set' => array( "estado" => $f->request->estado,
						"feccon"=>new MongoDate(),
						"evaluacion" => $f->request->evaluacion,
						"respuesta" => $f->request->respuesta,
						"flujos.$flujo.fecfin" =>new MongoDate(),
						"flujos.$flujo.evaluacion" => $f->request->evaluacion,
						"flujos.$flujo.respuesta" => $f->request->respuesta)
			)
		);
	}
}
?>