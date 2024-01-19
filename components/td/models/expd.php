<?php
class Model_td_expd extends Model {
	private $expd;
	private $db;
	public $items;
	public $dias;
	
	public function __construct() {
		global $f;
		$this->db = $f->datastore;
		$this->expd = $f->datastore->td_expedientes;
		$this->dias = 5;
	}
	protected function get_one_id(){
		global $f;
		$this->items = $this->expd->findOne(array('_id'=>$this->params['_id']));
	}
	protected function get_lista(){
		global $f;
  		if(isset($f->request->data['filter'])){
  			$filtro = $f->request->data['filter'];
  			if($filtro == 'gestor') $filtro = 'gestor.nomb';
			$ordenacion = array ( $filtro => intval($f->request->data['order']));
  		}
		else{
			//$ordenacion = array ( 'num_c' => -1);
			$ordenacion = array ( 'fecreg' => -1);
		}
		if(isset($f->request->data['texto'])){
			$parametro = new MongoRegex('/'.$f->request->data['texto'].'/i');
			//$col = $f->request->ncol;
			/*if($col == 'gestor')
				$criteria = array ( '$or' => array( array("gestor.nomb" => $parametro), array("gestor.appat" => $parametro), array("gestor.apmat" => $parametro), array("gestor.docident.num" => $parametro)));
			else
				$criteria = array ( $col => $parametro );*/
			$criteria = array ('$or'=>array(
				array("gestor.n" => $parametro),
				array("gestor.docident.num" => $parametro),
				/*array("documentos.num" => $parametro),*/
				array("documentos.asunto" => $parametro),
				array("num" => $parametro),
            			array("observ_expd" => $parametro),
				array("num_c" => $parametro),
				array("concepto" => $parametro),
				array("tupa.titulo" => $parametro),
				array("tupa.modalidad.descr" => $parametro),
				array("ubicacion.nomb" => $parametro)
			));
		}
		else
			$criteria = array ();
		if(isset($this->params['ini'])){
			if(!isset($criteria['$and']))
				$criteria['$and'] = array();
			$criteria['$and'][0] = array('fecreg'=>array('$gte' => $this->params['ini'], '$lte' => $this->params['fin']));
			//$criteria['fecreg'] = array('$gte' => $this->params['ini'], '$lte' => $this->params['fin']);
		}
		//print_r($criteria);die();
			# POR PEDIDO DE GERENCIA SE OCULTA ESTO
			$criteria['_id']=array(
				'$nin'=> array(
					new MongoId("5a4e4f123e6037d8688b4569"), # EXP 058-2018
				)
			);


		$data = $this->expd->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->sort($ordenacion)->limit( $f->request->page_rows );
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_listaexpdpor(){
		global $f;
		$enti = $f->session->enti['_id'];
		$orga = $f->session->enti['roles']['trabajador']['oficina']['_id'];
		if(isset($f->request->data['filter'])){
  			$filtro = $f->request->data['filter'];
  			if($filtro == 'gestor') $filtro = 'gestor.nomb';
			$ordenacion = array ( $filtro => intval($f->request->data['order']));
  		}
		else
			$ordenacion = array ( 'fecmod' => -1);
		//si existe busqueda
		if(!isset($f->request->data['bus'])){
			//$criteria = array ( 'traslados.destino.fecrec' => null, 'traslados.destino.organizacion._id' => $orga );
			//$criteria = array ( 'traslados.destino.fecrec' => null, 'traslados.destino.organizacion._id' => $orga);
			//$criteria = array ( 'traslados.destino.fecrec' => null, 'ubicacion._id' => $orga);
			$criteria = array (
				'traslados' => array('$elemMatch'=>array('destino.organizacion._id' => $orga,'destino.fecrec'=>null,'last'=>true))
			);
		}else{
			$parametro = new MongoRegex('/'.$f->request->data['bus'].'/i');
			$col = $f->request->ncol;
			//if($col == 'gestor'){
				//$criteria = array ( 'traslados.destino.fecrec' => null, 'traslados.destino.organizacion._id' => $orga,
				$criteria = array ( 'traslados.destino.fecrec' => null, 'traslados.destino.organizacion._id' => $orga,
					'$or' => array( array("num" => $parametro), array("concepto" => $parametro), array("gestor.nomb" => $parametro), array("gestor.appat" => $parametro), array("gestor.apmat" => $parametro), array("gestor.docident.num" => $parametro))
				);
				//$criteria = array( "num" => $f->request->data['bus']);
			/*}
			else {
				$criteria = array ( 'traslados.destino.fecrec' => null, 'traslados.destino.organizacion._id' => $orga, $col => $parametro );
			}*/
		}
		# POR PEDIDO DE GERENCIA SE OCULTA ESTO
			$criteria['_id']=array(
				'$nin'=> array(
					new MongoId("5a4e4f123e6037d8688b4569"), # EXP 058-2018
				)
			);
		$data = $this->expd->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->sort($ordenacion)->limit( $f->request->page_rows );
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_listaexpdreci(){
		global $f;
		$orga = $f->session->enti['roles']['trabajador']['oficina']['_id'];
		if(isset($f->request->data['filter'])){
  			$filtro = $f->request->data['filter'];
  			if($filtro == 'gestor') $filtro = 'gestor.nomb';
			$ordenacion = array ( $filtro => intval($f->request->data['order']));
  		}
		else
			//$ordenacion = array ( 'num_c' => -1);
			$ordenacion = array ( 'fecreg' => -1);
		//si existe busqueda
		if(!isset($f->request->data['texto']))
			$criteria = array (
				'traslados' => array('$elemMatch'=>array('origen.organizacion._id' => $orga,'origen.archivado'=>array('$exists'=>false)))
			);
		else{
			$parametro = new MongoRegex('/'.$f->request->data['texto'].'/i');
			//$col = $f->request->ncol;
			//if($col == 'gestor'){
				$criteria = array (
					'traslados' => array('$elemMatch'=>array('origen.organizacion._id' => $orga,'origen.archivado'=>array('$exists'=>false))),
					'$or' => array(
            array("num" => $parametro),
            array("concepto" => $parametro),
				array("documentos.num" => $parametro),
            array("gestor.nomb" => $parametro),
            array("gestor.appat" => $parametro),
            array("gestor.apmat" => $parametro),
            array("gestor.docident.num" => $parametro),
            array("num_c" => $parametro),
            array("observ_expd" => $parametro),
            array("gestor.n" => $parametro)
          )
				);
			/*}
			else {
				$criteria = array (
					'traslados' => array('$elemMatch'=>array('origen.organizacion._id' => $orga,'origen.archivado'=>array('$exists'=>false),'last'=>true))
					, $col => $parametro
				);
			}*/
		}
		# POR PEDIDO DE GERENCIA SE OCULTA ESTO
			$criteria['_id']=array(
				'$nin'=> array(
					new MongoId("5a4e4f123e6037d8688b4569"), # EXP 058-2018
				)
			);
		$data = $this->expd->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->sort($ordenacion)->limit( $f->request->page_rows );
		foreach ($data as $ob) {
	    	$this->items[] = $ob;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_listaexpdvenc(){
		global $f;
		$x = 0;
		$orga = $f->session->enti['roles']['trabajador']['oficina']['_id'];
		$fecha = new MongoDate(time() + ((int)$this->dias * (24 * 3600)));
		if(isset($f->request->data['filter'])){
  			$filtro = $f->request->data['filter'];
  			if($filtro == 'gestor') $filtro = 'gestor.nomb';
			$ordenacion = array ( $filtro => intval($f->request->data['order']));
  		}
		else
			$ordenacion = array ( 'fecven' => 1);
		//si existe busqueda
		if(!isset($f->request->data['bus']))
			$criteria = array (
				'traslados' => array('$elemMatch'=>array('origen.organizacion._id' => $orga,'origen.archivado'=>array('$exists'=>false),'last'=>true)),
				'fecven' => array('$lt' => $fecha),
				'estado' => 'P'
			);
		else{
			$parametro = new MongoRegex('/'.$f->request->data['bus'].'/i');
			$col = $f->request->ncol;
			//if($col == 'gestor'){
				$criteria = array (
					'traslados' => array('$elemMatch'=>array('origen.organizacion._id' => $orga,'origen.archivado'=>array('$exists'=>false),'last'=>true)),
					'fecven' => array('$lt' => $fecha),
					'estado' => 'P',
					'$or' => array(
            			array("num" => $parametro),
						array("num_c" => $parametro),
						array("observ_expd" => $parametro),
						array("concepto" => $parametro),
						array("gestor.n" => $parametro),
						array("gestor.nomb" => $parametro),
						array("gestor.appat" => $parametro),
						array("gestor.apmat" => $parametro),
            			array("gestor.docident.num" => $parametro)
          			)
				);
			/*}
			else {
				$criteria = array (
					'traslados' => array('$elemMatch'=>array('origen.organizacion._id' => $orga,'origen.archivado'=>array('$exists'=>false),'last'=>true)),
					'fecven' => array('$lt' => $fecha),
					'estado' => 'P',
					$col => $parametro );
			}*/
		}
		# POR PEDIDO DE GERENCIA SE OCULTA ESTO
			$criteria['_id']=array(
				'$nin'=> array(
					new MongoId("5a4e4f123e6037d8688b4569"), # EXP 058-2018
				)
			);
		$data = $this->expd->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->sort($ordenacion)->limit( $f->request->page_rows );
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_listaexpdarch(){
		global $f;
		$enti = $f->session->enti['_id'];
  		$orga = $f->session->enti['roles']['trabajador']['oficina']['_id'];
		if(isset($f->request->data['filter'])){
  			$filtro = $f->request->data['filter'];
  			if($filtro == 'gestor') $filtro = 'gestor.nomb';
			$ordenacion = array ( $filtro => intval($f->request->data['order']));
  		}
		else
			//$ordenacion = array ( 'num_c' => 1);
			$ordenacion = array ( 'fecreg' => -1);
		//si existe busqueda
		if(!isset($f->request->data['bus']))
			$criteria = array (
				'traslados' => array('$elemMatch'=>array('origen.organizacion._id' => $orga,'origen.archivado'=>array('$exists'=>true),'last'=>true))
			);
		else{
			$parametro = new MongoRegex('/'.$f->request->data['bus'].'/i');
			$col = $f->request->ncol;
			//if($col == 'gestor'){
				$criteria = array (
					'traslados' => array('$elemMatch'=>array('origen.organizacion._id' => $orga,'origen.archivado'=>array('$exists'=>true),'last'=>true)),
					'$or' => array(
            array("num" => $parametro),
            array("num_c" => $parametro),
            array("observ_expd" => $parametro),
            array("gestor.n" => $parametro),
            array("concepto" => $parametro),
            array("gestor.nomb" => $parametro),
            array("gestor.appat" => $parametro),
            array("gestor.apmat" => $parametro),
            array("gestor.docident.num" => $parametro)
          )
				);
			/*}
			else {
				$criteria = array (
					'traslados' => array('$elemMatch'=>array('origen.organizacion._id' => $orga,'origen.archivado'=>array('$exists'=>true),'last'=>true)),
					$col => $parametro
				);
			}*/
		}
		# POR PEDIDO DE GERENCIA SE OCULTA ESTO
			$criteria['_id']=array(
				'$nin'=> array(
					new MongoId("5a4e4f123e6037d8688b4569"), # EXP 058-2018
				)
			);
  		$data = $this->expd->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->sort($ordenacion)->limit( $f->request->page_rows );
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_listaexpdcopi(){
		global $f;
  		$x = 0;
  		$orga = $f->session->enti['roles']['trabajador']['oficina']['_id'];
  		if(isset($f->request->data['filter']))
			$ordenacion = array ( $f->request->data['filter'] => intval($f->request->data['order']));
		else
			$ordenacion = array ( 'fecmod' => -1);
		//si existe busqueda
		if(!isset($f->request->data['bus']))
			$criteria = array ( 'traslados.copias.organizacion._id' => $orga );
		else{
			$parametro = new MongoRegex('/'.$f->request->data['bus'].'/i');
			$col = $f->request->ncol;
			//if($col == 'gestor'){
				$criteria = array ( 'traslados.copias.organizacion._id' => $orga,
					'$or' => array( array("num" => $parametro), array("concepto" => $parametro), array("gestor.nomb" => $parametro), array("gestor.appat" => $parametro), array("gestor.apmat" => $parametro), array("gestor.docident.num" => $parametro))
				);
			/*}
			else {
				$criteria = array ( 'traslados.copias.organizacion._id' => $orga, $col => $parametro );
			}*/
		}
		# POR PEDIDO DE GERENCIA SE OCULTA ESTO
			$criteria['_id']=array(
				'$nin'=> array(
					new MongoId("5a4e4f123e6037d8688b4569"), # EXP 058-2018
				)
			);
  		$data = $this->expd->find($criteria,array('observ_expd'=>true,'num'=>true,'gestor'=>true,'concepto'=>true,'ubicacion'=>true,'traslados'=>true,'estado'=>true))->skip( $f->request->page_rows * ($f->request->page-1) )->sort($ordenacion)->limit( $f->request->page_rows );
		$i = 0;
  		foreach ($data as $ob) {
		    for($x = count( $ob['traslados'])-1; $x>=0;$x-- ){
		    	if(isset($ob['traslados'][$x]['copias'])){
			    	$y = count( $ob['traslados'][$x]['copias'])-1;
			    	while ( $y>=0 ){
				    	if( $ob['traslados'][$x]['copias'][$y]['organizacion']['_id'] == $orga ){
				    		$this->items[$i]['_id'] = $ob['_id'];
				    		$this->items[$i]['num'] = $ob['num'];
				    		$this->items[$i]['observ_expd'] = $ob['observ_expd'];
					    	$this->items[$i]['gestor'] = $ob['gestor'];
					    	$this->items[$i]['estado'] = $ob['estado'];
					    	$this->items[$i]['concepto'] = $ob['concepto'];
						    $this->items[$i]['origen']['_id'] = $ob['traslados'][$x]['origen']['organizacion']['_id'];
						    $this->items[$i]['origen']['nomb'] = $ob['traslados'][$x]['origen']['organizacion']['nomb'];
						    $this->items[$i]['destino']['_id'] = $ob['traslados'][$x]['destino']['organizacion']['_id'];
						    $this->items[$i]['destino']['nomb'] = $ob['traslados'][$x]['destino']['organizacion']['nomb'];
						    $this->items[$i]['fecenv'] = $ob['traslados'][$x]['copias'][$y]['fecenv'];
						    $i++;
				    	}
				    	$y--;
		    		}
		    	}
		    }
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_listaexpdrebi(){
		global $f;
  		$x = 0;
  		$orga = $f->session->enti['roles']['trabajador']['oficina']['_id'];
		if(isset($f->request->data['filter'])){
  			$filtro = $f->request->data['filter'];
  			if($filtro == 'gestor') $filtro = 'gestor.nomb';
			$ordenacion = array ( $filtro => intval($f->request->data['order']));
  		}
		else
			//$ordenacion = array ( 'num_c' => 1);
			$ordenacion = array ( 'fecreg' => -1);
		if(!isset($f->request->data['bus']))
			$criteria = array (
				//'traslados' => array('$elemMatch'=>array('destino.organizacion._id'=>$orga,'destino.estado'=>'A','last'=>true))
				'traslados' => array('$elemMatch'=>array('destino.organizacion._id'=>$orga,'destino.estado'=>'A'))
			);
		else{
			$parametro = new MongoRegex('/'.$f->request->data['bus'].'/i');
			$col = $f->request->ncol;
			//if($col == 'gestor'){
				//$criteria = array ( 'traslados' => array('$elemMatch'=>array('destino.organizacion._id'=>$orga,'destino.estado'=>'A','last'=>true)),
				$criteria = array ( 'traslados' => array('$elemMatch'=>array('destino.organizacion._id'=>$orga)),
					'$or' => array(
            array("num" => $parametro),
            array("num_c" => $parametro),
            array("observ_expd" => $parametro),
            array("gestor.n" => $parametro),
            array("concepto" => $parametro),
            array("gestor.nomb" => $parametro),
            array("gestor.appat" => $parametro),
            array("gestor.apmat" => $parametro),
            array("gestor.docident.num" => $parametro)
          )
				);
			/*}
			else {
				$criteria = array ( 'traslados' => array('$elemMatch'=>array('destino.organizacion._id'=>$orga,'destino.estado'=>'A')), $col => $parametro );
			}*/
		}
		# POR PEDIDO DE GERENCIA SE OCULTA ESTO
			$criteria['_id']=array(
				'$nin'=> array(
					new MongoId("5a4e4f123e6037d8688b4569"), # EXP 058-2018
				)
			);
  		$data = $this->expd->find($criteria,array('observ_expd'=>true,'estado'=>true,'num'=>true,'gestor'=>true,'concepto'=>true,'traslados'=>true))->skip( $f->request->page_rows * ($f->request->page-1) )->sort($ordenacion)->limit( $f->request->page_rows );
		$i = 0;
  		foreach ($data as $ob) {
			$this->items[$i]['_id'] = $ob['_id'];
    		$this->items[$i]['estado'] = $ob['estado'];
    		$this->items[$i]['num'] = $ob['num'];
				    		$this->items[$i]['observ_expd'] = $ob['observ_expd'];
    		if(isset($ob['gestor']['appat']))
	    		$this->items[$i]['gestor'] = $ob['gestor']['nomb'] .' '.$ob['gestor']['appat'] . ' ' . $ob['gestor']['apmat'];
	    	else
	    		$this->items[$i]['gestor'] = $ob['gestor']['nomb'];
	    	$this->items[$i]['concepto'] = $ob['concepto'];
		    $this->items[$i]['origen']['_id'] = $ob['traslados'][$x]['origen']['organizacion']['_id'];
		    $this->items[$i]['origen']['nomb'] = $ob['traslados'][$x]['origen']['organizacion']['nomb'];
		    $this->items[$i]['fecenv'] = $ob['traslados'][$x]['destino']['fecenv'];
		    $this->items[$i]['fecrec'] = $ob['traslados'][$x]['destino']['fecrec'];
		    $i++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_listaexpdenvi(){
		global $f;
  		$x = 0;
  		$orga = $f->session->enti['roles']['trabajador']['oficina']['_id'];
		if(isset($f->request->data['filter'])){
  			$filtro = $f->request->data['filter'];
  			if($filtro == 'gestor') $filtro = 'gestor.nomb';
			$ordenacion = array ( $filtro => intval($f->request->data['order']));
  		}
		else
			$ordenacion = array ( 'fecven' => 1);
		if(!isset($f->request->data['bus']))
			$criteria = array ( 'traslados' => array('$elemMatch'=>array('origen.organizacion._id' => $orga,'destino'=>array('$exists'=>true))) );
		else{
			$parametro = new MongoRegex('/'.$f->request->data['bus'].'/i');
			$col = $f->request->ncol;
			//if($col == 'gestor'){
				$criteria = array ( 'traslados' => array('$elemMatch'=>array('origen.organizacion._id' => $orga,'destino'=>array('$exists'=>true))),
					'$or' => array(
						array("num" => $parametro),
            array("num_c" => $parametro),
            array("observ_expd" => $parametro),
            array("gestor.n" => $parametro),
						array("concepto" => $parametro),
						array("gestor.nomb" => $parametro),
						array("gestor.appat" => $parametro),
						array("gestor.apmat" => $parametro),
						array("gestor.docident.num" => $parametro))
				);
			/*}
			else {
				$criteria = array ( 'traslados' => array('$elemMatch'=>array('origen.organizacion._id' => $orga,'destino'=>array('$exists'=>true))), $col => $parametro );
			}*/
		}
		# POR PEDIDO DE GERENCIA SE OCULTA ESTO
			$criteria['_id']=array(
				'$nin'=> array(
					new MongoId("5a4e4f123e6037d8688b4569"), # EXP 058-2018
				)
			);
  		$data = $this->expd->find($criteria,array('observ_expd'=>true,'estado'=>true,'num'=>true,'gestor'=>true,'concepto'=>true,'traslados'=>true))->skip( $f->request->page_rows * ($f->request->page-1) )->sort($ordenacion)->limit( $f->request->page_rows );
		$i = 0;
  		foreach ($data as $ob) {
    		$this->items[$i]['_id'] = $ob['_id'];
    		$this->items[$i]['estado'] = $ob['estado'];
    		$this->items[$i]['num'] = $ob['num'];
				    		$this->items[$i]['observ_expd'] = $ob['observ_expd'];
	    	if(isset($ob['gestor']['appat']))
	    		$this->items[$i]['gestor'] = $ob['gestor']['nomb'] .' '.$ob['gestor']['appat'] . ' ' . $ob['gestor']['apmat'];
	    	else
	    		$this->items[$i]['gestor'] = $ob['gestor']['nomb'];
	    	$this->items[$i]['concepto'] = $ob['concepto'];
		    $this->items[$i]['origen']['_id'] = $ob['traslados'][$x]['origen']['organizacion']['_id'];
		    $this->items[$i]['origen']['nomb'] = $ob['traslados'][$x]['origen']['organizacion']['nomb'];
		    $this->items[$i]['destino']['_id'] = $ob['traslados'][$x]['destino']['organizacion']['_id'];
		    $this->items[$i]['destino']['nomb'] = $ob['traslados'][$x]['destino']['organizacion']['nomb'];
		    $this->items[$i]['fecenv'] = $ob['traslados'][$x]['destino']['fecenv'];
		    $this->items[$i]['fecrec'] = $ob['traslados'][$x]['destino']['fecrec'];
		    $i++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_listaexpdgest(){
		global $f;
		$data = $this->expd->find(array ('gestor._id' => $this->params['gestor']))->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort(array('num_c'=>-1))->limit( $this->params['page_rows'] );
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_listaexpdhistvenc(){
		global $f;
		$x = 0;
		$fecha = new MongoDate(time() + ((int)$this->dias * (24 * 3600)));
		if(isset($f->request->data['filter']))
			$ordenacion = array ( $f->request->data['filter'] => intval($f->request->data['order']));
		else
			$ordenacion = array ( 'fecven' => 1);
		$data = $this->expd->find(array ( 'fecven' => array('$lt' => $fecha), 'estado' => 'P' ))->skip( $this->params["page_rows"] * ($this->params["page"]-1) )->sort($ordenacion)->limit( $this->params["page_rows"] );
		foreach ($data as $ob) {
	    	$this->items[$x] = $ob;
	    	$x++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_all(){
		global $f;
		$data = $this->expd->find()->skip( $f->request->page_rows * ($f->request->page-1) )->sort(array('num_c'=>-1))->limit( $f->request->page_rows );
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_all_filter(){
		global $f;
		$filter=array();		
		if($this->params["desde"]!="" && $this->params["hasta"]==""){
			$filter["fecreg"] = array('$gte' => new MongoDate(strtotime($this->params["desde"])));
		}elseif($this->params["desde"]=="" && $this->params["hasta"]!=""){
			$filter["fecreg"] = array('$lte' => new MongoDate(strtotime($this->params["hasta"])));
		}elseif($this->params["desde"]!="" && $this->params["hasta"]!=""){
			$filter["fecreg"] = array('$gte' => new MongoDate(strtotime($this->params["desde"])), '$lte' => new MongoDate(strtotime($this->params["hasta"])));
		}
		
		if($this->params["usuario"]!=""){
			$filter["gestor._id"] = new MongoId($this->params["usuario"]);
		}	
		if($this->params["oficina"]!=""){
			$filter["gestor.organizacion._id"] = new MongoId($this->params["oficina"]);
		}		
		if($this->params["proc"]){
			$filter["tupa.procedimiento._id"] = new MongoId($this->params["proc"]);
		}
		if($this->params["venc"]=="1"){
			$filter["estado"] = "C";
			$filter["fecven"] = array('$lte' => new MongoDate());
		}
		if($this->params["noaten"]=="1"){
			$filter["estado"] = "P";
		}
		if($this->params["tipo"]=="I"){
			$filter["tupa"] = array('$exists'=>false);
		}else{
			$filter["tupa"] = array('$exists'=>true);
		}
		if($this->params["archivado"]=="S"){
			$filter["traslados"]["origen"]["archivado"] = array('$exists'=>true);
		}
		if($this->params["estado"]!=""){
			if($this->params["estado"]=="V"){
				$filter['estado']='P';
				$filter['fecven'] = array('$lte' => new MongoDate());
			}else{
				$filter["estado"] = $this->params["estado"];
			}
		}
		/*$filter = array(
			"fecreg"=>$fecreg,
			"gestor._id"=>$usuario,
			"gestor.organizacion"=>$oficina,
			"tupa.procedimiento._id"=>$proc,
			"fecven"=>$fecven,
			"estado"=>$estado
		);*/
		$data = $this->expd->find($filter);
		foreach($data as $ob){
			$this->items[] = $ob;
		}
	}
	protected function get_one(){
		global $f;
		$this->items = $this->expd->findOne(array('_id'=>new MongoId($f->request->data['_id'])));
		$ref = array(
			'$ref' => 'mg_entidades',
			'$id' => $this->items['gestor']['_id']
		);
		$this->items['gestor'] = $this->db->getDBRef($ref);
	}
	protected function get_search(){
		global $f;
		$i = 0;
		$criteria = array ( $f->request->ncol => new MongoRegex('/'.$f->request->nomb.'/i'));
		$data = $this->expd->find($criteria)->skip( $f->request->page_rows * ($f->request->page-1) )->limit( $f->request->page_rows );
		foreach ($data as $obj) {
		    $this->items[$i] = $obj;
			$i++;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_search_all(){
		global $f;
		$filter = array();
		if($this->params["text"]!=''){
			$f->library('helpers');
			$helper=new helper();
			$parametro = $this->params["text"];
			$filter = $helper->paramsSearch($this->params["text"], array('num_c','observ_expd','gestor.n','gestor.nomb','gestor.appat','gestor.apmat','gestor.docident.num'));
		}
		if(isset($this->params['num'])){
			$filter['num'] = $this->params['num'];
		}
		$fields = array();
		$order = array('num_c'=>1);
		$data = $this->expd->find($filter,$fields)->skip( $this->params['page_rows'] * ($this->params['page']-1) )->sort($order)->limit( $this->params['page_rows'] );
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
		$this->paging($this->params["page"],$this->params["page_rows"],$data->count());
	}
	protected function get_repo(){
		global $f;
		$ini = new MongoDate(strtotime($this->params['ano']."-".$this->params['mes']."-01"));
		$fin = new MongoDate(strtotime($this->params['ano']."-".$this->params['mes']."-01 +".$this->params['cant']." month"));
		$data = $this->expd->find(array(
			'$or'=>array(
				array(
					'fecreg'=>array(
						'$gte'=>$ini,
						'$lt'=>$fin
					)
				),
				array(
					'estado'=>'C',
					'feccon'=>array(
						'$gte'=>$ini,
						'$lt'=>$fin
					)
				),
				array(
					'feccon'=>array(
						'$gte'=>$fin
					),
					'fecreg'=>array(
						'$lt'=>$ini
					)
				)
			)
		));
		foreach ($data as $ob) {
		    $this->items[] = $ob;
		}
	}
	protected function get_custom(){
		global $f;
		$fields = array();
		if(isset($this->params['fields']))
			$fields = $this->params['fields'];
		$data = $this->expd->find($this->params['filter'],$fields);
		foreach ($data as $obj) {
		    $this->items[] = $obj;
		}
	}
	protected function save_datos(){
		global $f;
		//El array al parecer no guarda las fechas como se quiere.
		$array = (array)$f->request->data['data'];
		if(isset($f->request->data['_id'])){
			//$array['_id'] = new MongoId($f->request->data['_id']);
			//$this->tdoc->update(array( '_id' => new MongoId($f->request->data['_id']) ),$array);
		}else{
				$dat = $this->expd->find(array(),array("num"=>true))->sort(array("fecreg"=>-1))->limit(1);
				foreach ($dat as $ob) {
				    //$tem =  split('-',$ob['num']);
				    $tem = explode("-", $ob['num']);
				}
				/** Reiniciar al inicio del año */
				//if(date('Y')=='2018'&&$tem[1]=='2017'){
				if(date('Y')=='2019'&&$tem[1]=='2018'){
					$tem[0]='';
				}
				/** ./Reiniciar al inicio del año */
				
				if($tem[0]=="") $tem[0]="0000";
		  		$t = "1000"+$tem[0];
		  		$int = ($t+1)-1000;
			    for($i=0; $i<(strlen($tem[0])-strlen($int)); $i++)
			        $nulls .= '0';
				$array['num'] = $nulls.$int."-".date("Y");
				$array['num_c'] = intval($int);
			$array['fecreg'] = new MongoDate();
			$array['fecmod'] = new MongoDate();
			$array['circular_env']=false;
			$array['gestor']['_id'] = new MongoId($array['gestor']['_id']);
			if($array['gestor']['organizacion']!=null){
				$array['gestor']['organizacion']['_id'] = new MongoId($array['gestor']['organizacion']['_id']);
			}
			if($f->request->dias!=null && $f->request->dias!=''){
				$array['fecven'] = new MongoDate(time() + ((int)$f->request->dias * (24 * 60 * 60)));
			}
			$array['estado'] = "P";
			$array['flujos']['iniciacion']['fecini'] = new MongoDate();
			if(isset($array['documentos'])){
				$i = 0;
				foreach ($array['documentos'] as $obj){
					$array['documentos'][$i]['_id'] = new MongoId();
					$array['documentos'][$i]['flujo'] = "iniciado";
					$array['documentos'][$i]['tipo_documento']['_id'] = new MongoId($array['documentos'][$i]['tipo_documento']['_id']);
					$array['documentos'][$i]['organizacion']['_id'] = $f->session->enti['roles']['trabajador']['oficina']['_id'];
					$array['documentos'][$i]['organizacion']['nomb'] = $f->session->enti['roles']['trabajador']['oficina']['nomb'];
					$array['documentos'][$i]['fecreg'] = new MongoDate();
					$array['documentos'][$i]['asunto'] = $array['documentos'][$i]['asunto'];
					$array['traslados'][0]['origen']['documentos'][$i]['_id'] = new MongoId($array['documentos'][$i]['_id']);
					$i++;
				}
			}
			$array['traslados'][0]['origen']['entidad']['_id'] = $f->session->enti['_id'];
			$array['traslados'][0]['origen']['entidad']['nomb'] = $f->session->enti['nomb'];
			if(isset($f->session->enti['appat']))
				$array['traslados'][0]['origen']['entidad']['appat'] = $f->session->enti['appat'];
			if(isset($f->session->enti['apmat']))
				$array['traslados'][0]['origen']['entidad']['apmat'] = $f->session->enti['apmat'];
			$array['traslados'][0]['origen']['organizacion']['_id'] = $f->session->enti['roles']['trabajador']['oficina']['_id'];
			$array['traslados'][0]['origen']['organizacion']['nomb'] = $f->session->enti['roles']['trabajador']['oficina']['nomb'];
			$array['traslados'][0]['origen']['fecreg'] = new MongoDate();
			$array['traslados'][0]['last'] = true;
			if(isset($array['tupa']['_id'])){
				$array['tupa']['_id']  = new MongoId($array['tupa']['_id']);
				$array['tupa']['procedimiento']['_id']  = new MongoId($array['tupa']['procedimiento']['_id']);
				$array['tupa']['procedimiento']['modalidad']['_id']  = new MongoId($array['tupa']['procedimiento']['modalidad']['_id']);
			}
			$array['ubicacion'] = $array['traslados'][0]['origen']['organizacion'];
			$this->expd->insert($array);
			//registro
			$this->items = $this->expd->findOne(array('num'=>$array['num']),array("_id"=>true,"num"=>true));
		}
	}
	protected function save_doc(){
		global $f;
		$array = (array)$f->request->data['data'];
		if(isset($f->request->data['data']['index'])){
			if(isset($array['expd']))
				unset($array['expd']);
			$array['tipo_documento']['_id'] = new MongoId($array['tipo_documento']['_id']);
			$array['organizacion']['_id'] = $f->session->enti['roles']['trabajador']['oficina']['_id'];
			$array['organizacion']['nomb'] = $f->session->enti['roles']['trabajador']['oficina']['nomb'];
			$array['fecreg'] = new MongoDate();
			$array['trabajador'] = $f->session->userDB;
			$this->expd->update(
				array('_id'=>new MongoId($f->request->data['_id'])) ,
				array( '$set' => array( 'documentos.'.$array['index'] => $array ))
			);
		}else{
			if(isset($array['expd']))
				unset($array['expd']);
			$array['tipo_documento']['_id'] = new MongoId($array['tipo_documento']['_id']);
			$array['organizacion']['_id'] = $f->session->enti['roles']['trabajador']['oficina']['_id'];
			$array['organizacion']['nomb'] = $f->session->enti['roles']['trabajador']['oficina']['nomb'];
			$array['fecreg'] = new MongoDate();
			$array['trabajador'] = $f->session->userDB;
			$this->expd->update(
				array('_id'=>new MongoId($f->request->data['_id'])) ,
				array( '$push' => array( 'documentos' => $array ))
			);
		}
	}
	protected function save_expdsend(){
		global $f;
		$i = 0;
		$array = (array)$f->request->data['data'];
		if(isset($f->request->data['copias']))
			$copias = (array)$f->request->data['copias'];
		//Organizacion Destino
		$array['organizacion']['_id'] = new MongoId($array['organizacion']['_id']);
		$ref = array('$ref' => 'mg_oficinas','$id' => $array['organizacion']['_id']);
		$temp = $this->db->getDBRef($ref);
		$array['organizacion']['nomb'] = $temp['nomb'];
		
		$array['estado'] = "P";
		$array['fecenv'] = new MongoDate();
		$array['fecrec'] = null;
		$array['observ'] = " ";
		$va;
		$data = $this->expd->find(array ('_id' => new MongoId($f->request->expd)));
		foreach ($data as $ob) {
			$tras = $ob["traslados"];
		    $va = count($ob['traslados'])-1;
		}
		//Organizaciones de Copias
		$i = 0;
		if(isset($copias)){
		      	foreach ($copias as $obj){
			        $co[$i]['organizacion']['_id'] = new MongoId($obj['_id']);
			        $co[$i]['organizacion']['nomb'] = $obj['nomb'];
			        $co[$i]['fecenv'] = $array['fecenv'];
			        $i++;
		      	}
	     	}
	     	$upd_expd = array(
			'ubicacion'=> $array['organizacion'],
			'fecmod' => new MongoDate(),
			"traslados.$va.destino" => $array
		);
		if(isset($co))
			$upd_expd["traslados.$va.copias"] = $co;
		//Guarda Organizacion de Destino
		$this->expd->update(
			array('_id'=>new MongoId($f->request->expd)) ,
			array( '$set' => $upd_expd )
		);
		$this->expddata = $data;
	}
	protected function save_expdsend_circular(){
		global $f;
		$array = (array)$f->request->data;
		$expd_copy = $this->expd->findOne(array('_id'=>new MongoId($array['expd'])));
		if(isset($array['destinos'])){
			if(count($array['destinos'])>0){
				foreach($array['destinos'] as $i=>$destino){
					if($i==0){
						$tras = $expd_copy["traslados"];
		    			$va = count($expd_copy['traslados'])-1;
						$upd_expd = array(
							//'ubicacion'=> $array['organizacion'],
							'circular_env'=>true,
							'fecmod' => new MongoDate(),
							"traslados.$va.destino" => array(
								'organizacion'=>array(
						   			"_id"=>new MongoId($destino['_id']),
						   			"nomb"=>$destino['nomb'],
						   			"sigla"=>"--"
								),
								"estado"=>"P",
						    	"fecenv"=>new MongoDate(),
						    	"fecrec"=>null,
						    	"observ"=>" "
							),
							'ubicacion'=>array(
					   			"_id"=>new MongoId($destino['_id']),
					   			"nomb"=>$destino['nomb'],
					   			"sigla"=>"--"
							)
						);
						//Guarda Organizacion de Destino
						$this->expd->update(
							array('_id'=>new MongoId($array['expd'])) ,
							array( '$set' => $upd_expd )
						);
					}else{
						//echo "entro";
						$expd = array();
						$expd = $expd_copy;
						$expd['_id']= new MongoId();
						$expd['circular_env'] = true;
						$expd['traslados'][0]['destino'] = array(
							"organizacion"=> array(
					   			"_id"=>new MongoId($destino['_id']),
					   			"nomb"=>$destino['nomb'],
					   			"sigla"=>"--"
							),
					  		"estado"=>"P",
					    	"fecenv"=>new MongoDate(),
					    	"fecrec"=>null,
					    	"observ"=>" "
						);
						$expd['ubicacion'] = array(
				   			"_id"=>new MongoId($destino['_id']),
				   			"nomb"=>$destino['nomb'],
				   			"sigla"=>"--"
						);
						$this->expd->insert($expd);	
					}
				}
			}
		}
		//$this->expddata = $data;
	}
	protected function save_expdsendout(){
		global $f;
		$i = 0;
		$array = $f->request->data;
		//Organizacion Destino
		$array['entidad']['_id'] = new MongoId($array['entidad']['_id']);
		$array['fecenv'] = new MongoDate();
		$array['fecrec'] = new MongoDate();
		//$array['observ'] = " ";
		$va;
		$data = $this->expd->find(array ('_id' => new MongoId($array["_id"])));		
		foreach ($data as $ob) {
			$tras = $ob["traslados"];
		    $va = count($ob['traslados'])-1;
			$array['estado'] = $ob["estado"];			
		}
		if(isset($tras[$va]['origen']['archivado'])){
			if($tras[$va]['origen']['archivado']==true){
				$array["origen"]['archivado']=true;
				$array["origen"]["fecarc"] = $tras[$va]['origen']["fecarc"];
			}
		}
		//Guarda Organizacion de Destino
		$this->expd->update(
			array('_id'=>new MongoId($array["_id"])) ,
			array( '$set' => array(
				'estado'=>'F',
				'fecmod' => new MongoDate(),
				"traslados.$va.destino" => $array,
				"traslados.$va.last"=>false
			) )
		);
		//crea nuevo traslado
		$array_n['origen']['entidad']['_id'] = $tras[$va]['origen']['entidad']['_id'];
		$array_n['origen']['entidad']['nomb'] = $tras[$va]['origen']['entidad']['nomb'];
		if(isset($f->session->enti['appat']))
			$array_n['origen']['entidad']['appat'] = $tras[$va]['origen']['entidad']['appat'];
		if(isset($f->session->enti['apmat']))
			$array_n['origen']['entidad']['apmat'] = $tras[$va]['origen']['entidad']['apmat'];
		$array_n['origen']['organizacion']['_id'] = $tras[$va]['origen']['organizacion']['_id'];
		$array_n['origen']['organizacion']['nomb'] = $tras[$va]['origen']['organizacion']['nomb'];
		if(isset($tras[$va]['origen']['archivado'])){
			if($tras[$va]['origen']['archivado']==true){
				$array_n['origen']['archivado']=true;
				$array_n['origen']["fecarc"] = $tras[$va]['origen']["fecarc"];
			}
		}
		$array_n['origen']['entidad_ext'] = $array['entidad'];
		$array_n['origen']['fecreg'] = new MongoDate();
		$array_n['origen']['observ'] = $array['observ'];
		$array_n['last'] = true;
		$this->expd->update(
			array('_id'=>new MongoId($array["_id"])) ,
			array( '$push' => array(
				"traslados" => $array_n
			))
		);
		$this->expd->update(
			array('_id'=>new MongoId($f->request->data['_id'])) ,
			array( '$set' => array(
				"ubicacion"=>$array_n['origen']['entidad_ext']
			))
		);
		$this->expddata = $data;
	}
	protected function save_expdsendin(){
		global $f;
		$i = 0;
		$array = $f->request->data;
		$va;
		$data = $this->expd->find(array ('_id' => new MongoId($array["_id"])));		
		foreach ($data as $ob) {
			$expd = $ob;
			$tras = $ob["traslados"];
		    $va = count($ob['traslados'])-1;
			$array['estado'] = $ob["estado"];			
		}
		if(isset($tras[$va]['origen']['archivado'])){
			if($tras[$va]['origen']['archivado']==true){
				$array["origen"]['archivado']=true;
				$array["origen"]["fecarc"] = $tras[$va]['origen']["fecarc"];
			}
		}
		//Guarda Organizacion de Destino
		$array_l['entidad']['_id'] = $tras[$va]['origen']['entidad']['_id'];
		$array_l['entidad']['nomb'] = $tras[$va]['origen']['entidad']['nomb'];
		if(isset($f->session->enti['appat']))
			$array_l['entidad']['appat'] = $tras[$va]['origen']['entidad']['appat'];
		if(isset($f->session->enti['apmat']))
			$array_l['entidad']['apmat'] = $tras[$va]['origen']['entidad']['apmat'];
		$array_l['organizacion']['_id'] = $tras[$va]['origen']['organizacion']['_id'];
		$array_l['organizacion']['nomb'] = $tras[$va]['origen']['organizacion']['nomb'];
		if(isset($tras[$va]['origen']['archivado'])){
			if($tras[$va]['origen']['archivado']==true){
				$array_l['archivado']=true;
				$array_l["fecarc"] = $tras[$va]['origen']["fecarc"];
			}
		}
		$array_l['fecreg'] = new MongoDate();
		$array_l['fecrev'] = new MongoDate();
		$array_l['fecenv'] = new MongoDate();
		$array_l['observ'] = $array['observ'];
		$this->expd->update(
			array('_id'=>new MongoId($array["_id"])) ,
			array( '$set' => array(
				'estado'=>$tras[($va-1)]['destino']['estado'],
				'fecmod' => new MongoDate(),
				"traslados.$va.destino" => $array_l,
				"traslados.$va.last"=>false
			) )
		);
		//crea nuevo traslado
		$array_n['origen']['entidad']['_id'] = $tras[$va]['origen']['entidad']['_id'];
		$array_n['origen']['entidad']['nomb'] = $tras[$va]['origen']['entidad']['nomb'];
		if(isset($f->session->enti['appat']))
			$array_n['origen']['entidad']['appat'] = $tras[$va]['origen']['entidad']['appat'];
		if(isset($f->session->enti['apmat']))
			$array_n['origen']['entidad']['apmat'] = $tras[$va]['origen']['entidad']['apmat'];
		$array_n['origen']['organizacion']['_id'] = $tras[$va]['origen']['organizacion']['_id'];
		$array_n['origen']['organizacion']['nomb'] = $tras[$va]['origen']['organizacion']['nomb'];
		if(isset($tras[$va]['origen']['archivado'])){
			if($tras[$va]['origen']['archivado']==true){
				$array_n['origen']['archivado']=true;
				$array_n['origen']["fecarc"] = $tras[$va]['origen']["fecarc"];
			}
		}
		$array_n['origen']['entidad_ext'] = $array['entidad'];
		$array_n['origen']['fecreg'] = new MongoDate();
		$array_n['origen']['observ'] = $array['observ'];
		$array_n['last'] = true;
		$this->expd->update(
			array('_id'=>new MongoId($array["_id"])) ,
			array( '$push' => array(
				"traslados" => $array_n
			))
		);
		$this->expd->update(
			array('_id'=>new MongoId($f->request->data['_id'])) ,
			array( '$set' => array(
				"ubicacion"=>$array_n['origen']['organizacion']
			))
		);
		$this->expddata = $data;
	}
	protected function save_expdestado(){
		global $f;
		$i = 0;
		$va = 0;
		$data = $this->expd->find(array('_id'=>new MongoId($f->request->id)),array('traslados.origen'=>true,'traslados.destino'=>true));
		foreach ($data as $ob) {
		    $va = count($ob['traslados']);
		    $tras = $ob['traslados'];
		    $i++;
		}
		$va = $va - 1;
		//Solo Actualiza Estado
		if($f->request->fl=="0"){
			if($f->request->estado=="A"){
				$this->expd->update(
					array('_id'=>new MongoId($f->request->id)) ,
					array( '$set' => array(
						"traslados.$va.destino.estado" => $f->request->estado,
						"traslados.$va.destino.feccon"=>new MongoDate()
					))
				);
			}else{
				$this->expd->update(
					array('_id'=>new MongoId($f->request->id)) ,
					array( '$set' => array(
						"traslados.$va.destino.estado" => $f->request->estado,
						"traslados.$va.destino.observ" => $f->request->descr,
						"traslados.$va.destino.feccon"=>new MongoDate(),
						"traslados.$va.destino.fecrec"=>new MongoDate(),
						"traslados.$va.last"=>false
					))
				);
				//Modificar el Origen de Rechazo
				$array['origen']['entidad']['_id'] = $tras[$va]['origen']['entidad']['_id'];
				$array['origen']['entidad']['nomb'] = $tras[$va]['origen']['entidad']['nomb'];
				if(isset($f->session->enti['appat']))
					$array['origen']['entidad']['appat'] = $tras[$va]['origen']['entidad']['appat'];
				if(isset($f->session->enti['apmat']))
					$array['origen']['entidad']['apmat'] = $tras[$va]['origen']['entidad']['apmat'];
				$array['origen']['organizacion']['_id'] = $tras[$va]['origen']['organizacion']['_id'];
				$array['origen']['organizacion']['nomb'] = $tras[$va]['origen']['organizacion']['nomb'];
				$array['origen']['fecreg'] = new MongoDate();
				$array['last'] = true;
				$this->expd->update(
					array('_id'=>new MongoId($f->request->id)) ,
					array( '$push' => array(
						"traslados" => $array
					))
				);
				$this->expd->update(
					array('_id'=>new MongoId($f->request->id)) ,
					array( '$set' => array(
						"ubicacion"=>$array['origen']['organizacion']
					))
				);
			}
		}
		else{
			$this->expd->update(
					array('_id'=>new MongoId($f->request->id)) ,
					array( '$set' => array(
						"traslados.$va.destino.fecrec"=>new MongoDate(),
						"traslados.$va.last"=>false
					))
				);
			//Crear Nuevo Origen porque se recepciono el expd fisico
			$array['origen']['entidad'] = $f->session->userDB;
			$array['origen']['organizacion'] = $f->session->enti['roles']['trabajador']['oficina'];
			$array['origen']['fecreg'] = new MongoDate();
			$array['last'] = true;
			$this->expd->update(
				array('_id'=>new MongoId($f->request->id)) ,
				array( '$push' => array(
					"traslados" => $array
				))
			);
			$this->expd->update(
				array('_id'=>new MongoId($f->request->id)) ,
				array( '$set' => array(
					"ubicacion"=>$array['origen']['organizacion']
				))
			);
		}
	}
	protected function save_expdopen(){
		global $f;
		if($f->request->fl=="R"){
			//Reconsideracion
			$this->expd->update(
				array('_id'=>new MongoId($f->request->id)) ,
				array( '$set' => array( "flujos.reconsideracion.fecini" => new MongoDate(),"estado" =>"P","feccon" => null,"evaluacion"=>"","respuesta"=>""))
			);
		}else{
			//Apelacion
			$this->expd->update(
				array('_id'=>new MongoId($f->request->id)) ,
				array( '$set' => array( "flujos.apelacion.fecini" => new MongoDate(),"estado" =>"P","feccon" => null,"evaluacion"=>"","respuesta"=>""))
			);
		}
		
	}
	protected function save_expdarchivar(){
		global $f;
		$i = 0;
		$va = 0;
		$data = $this->expd->find(array('_id'=>new MongoId($f->request->id)),array('traslados.origen'=>true));
		foreach ($data as $ob) {
		    $temp[$i] = $ob;
		    $va = count($temp[$i]['traslados'])-1;
		    $i++;
		}
		$this->expd->update(
			array('_id'=>new MongoId($f->request->id)) ,
			array( '$set' => array(
				"traslados.$va.origen.archivado" => true,
				"traslados.$va.origen.fecarc"=>new MongoDate()/*,
				"ubicacion.origen.archivado" => true,
				"ubicacion.origen.fecarc"=>new MongoDate()*/
			))
		);
	}
	protected function save_expdconcluir(){
		global $f;
		$i = 0;
		$va = 0;
		if(isset($f->request->flujos['iniciacion'])) $flujo="iniciacion";
		if(isset($f->request->flujos['reconsideracion'])) $flujo="reconsideracion";
		if(isset($f->request->flujos['apelacion'])) $flujo="apelacion";
		$this->expd->update(
			array('_id'=>new MongoId($f->request->id)) ,
			array( '$set' => array( "estado" => $f->request->estado,
				"feccon"=>new MongoDate(),
				"evaluacion" => $f->request->evaluacion,
				"respuesta" => $f->request->respuesta,
				"observ_conc" => $f->request->data['observ_conc'],
				"flujos.$flujo.fecfin" =>new MongoDate(),
				"flujos.$flujo.evaluacion" => $f->request->evaluacion,
				"flujos.$flujo.respuesta" => $f->request->respuesta)
			)
		);
	}
	protected function delete_doc(){
		global $f;
		$expd = $this->expd->findOne(array('_id'=>$this->params['_id']));
		$docs = array_slice($expd['documentos'],0,-1);
		$this->expd->update(array('_id'=>$this->params['_id']),array('$set'=>array(
			'documentos'=>$docs
		)));
	}
}
?>
