<?php
class Controller_ti_sist extends Controller {
	function execute_index(){
		global $f;
		$f->response->view("ti/dashboard");
	}
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("ti/sist")->params($params)->get("lista") );
	}
	function execute_get(){
		global $f;
		$items = $f->model("ti/sist")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->response->json( $items );
	}
	function execute_get_dash(){
		global $f;
		$timezone = 'America/Lima';
		// 1. Se obtendra las sesiones en las ultimas 24 horas
		$data = array(
			'sesiones'=>array(
				'legend'=>array(),
				'total'=>array(),
			),
		);
		for($i=144; $i>0; $i--){
			$legend[]= strtotime('-'.$i.'0 minutes');
		}
		asort($legend);
		foreach ($legend as $l => $leg_val) {
			$data['sesiones']['legend'][]= ($l%6 == 1) ? date('H:i:s',$leg_val) : "";
			$data['sesiones']['total'][] = ($l<count($legend)-1) ? $this->get_sesiones_fecha($legend[$l],$legend[$l+1]) : $this->get_sesiones_fecha($legend[$l],strtotime(date('H:i:s')));
		}
		header("Content-type:application/json");
        echo json_encode( $data );
	}
	function get_sesiones_fecha($ini,$fin){
		global $f;
		$params['$and'] = array(
			array('fecreg'=>array('$gte'=>new MongoDate($ini))),
			array('fecreg'=>array('$lte'=>new MongoDate($fin)))
		);
		$model = $f->model("ti/sist")->params(array('filter'=>$params,'fields'=>array('user'=>1,'_id'=>0)))->get("all")->items;
		$peticiones = array();
		if (!is_null($model)) {
			foreach ($model as $p => $peticion) {
				$peticiones[$peticion['user']->{'$id'}][] = $peticion;
			}
		} else {
			$peticiones=array();
		}	
		return count($peticiones);
	}
	function execute_get_sessiones_activas(){
		global $f;
		$fecini = strtotime("-10 minutes");
		$timezone = 'America/Lima';
		$params['$and'] = array(
			array('fecreg'=>array('$gte'=>new MongoDate($fecini))),
			array('fecreg'=>array('$lte'=>new MongoDate()))
		);

		$model = $f->model("ti/sist")->params(array('filter'=>$params,'fields'=>array('fecreg'=>1,'user'=>1,'_id'=>0)))->get("all")->items;
		$peticiones = array();
		$sesiones = array();
		foreach ($model as $p => $peticion) {
			$peticiones[$peticion['user']->{'$id'}][] = $peticion;
		}
		foreach ($peticiones as $p => $peticion) {
			$sesiones[$p]=$peticion[0]['fecreg']->toDateTime()->setTimeZone(new DateTimeZone($timezone))->format('Y-m-d H:i:s');
		}
		header("Content-type:application/json");
        echo json_encode( $sesiones );
	}
	function execute_get_peticiones_comunes(){
		global $f;
		//$fecini = strtotime("-1 day");
		$timezone = 'America/Lima';
		/*$params['$and'] = array(
			array('fecreg'=>array('$gte'=>new MongoDate($fecini))),
			array('fecreg'=>array('$lte'=>new MongoDate()))
		);*/

		$model = $f->model("ti/sist")->params(array('filter'=>$params,'fields'=>array('call'=>1,'_id'=>0)))->get("all")->items;
		$peticiones = array();
		$cuentas = array();
		$total = 0;
		foreach ($model as $p => $peticion) {
			$peticiones[$peticion['call']][] = $peticion;
		}
		foreach ($peticiones as $p => $peticion) {
			$cuentas[$p]=count($peticion);
			$total+=count($peticion);
		}
		arsort($cuentas);
		header("Content-type:application/json");
        echo json_encode( array('call'=>$cuentas,'total'=>$total));
	}

	function execute_get_extremos_coleccion(){
		global $f;
		$colecciones = $f->datastore->listCollections();
		$documentos = [];
		foreach ($colecciones as $coleccion) {
        	if($coleccion->getName() != "fs.chunks" && $coleccion->getName() != "fs.files"){
        		$nomb_col = explode("_", $coleccion->getName());
        		if($coleccion->count()>0){
	        		$documentos[$nomb_col[0]][$coleccion->getName()]['total'] = $coleccion->count();
		        	$cursor_ultimo = $coleccion -> find(array(),array("_id"=>1,'autor'=>1)) -> sort(["_id"=>-1])->limit(1);
		    		foreach ($cursor_ultimo as $documento) {
		    			$documento["fecha"] = date('Y-m-d H:i:s',$documento["_id"]->getTimestamp());
		    			if(isset($documento["autor"])) {
		    				$nombre= "--";
		    				$appat = "--";
		    				$apmat = "--";
		    				if(isset($documento["autor"]["nomb"])) $nombre = $documento["autor"]["nomb"];
		    				if(isset($documento["autor"]["appat"])) $appat = $documento["autor"]["appat"];
		    				if(isset($documento["autor"]["apmat"])) $apmat = $documento["autor"]["apmat"];
		    				$documento["autor"] = $nombre." ".$appat." ".$apmat;
		    			}
		    			$documentos[$nomb_col[0]][$coleccion->getName()][] = $documento;
		    		}
		    		$cursor_primero = $coleccion -> find(array(),array("_id"=>1,'autor'=>1)) -> sort(["_id"=>1])->limit(1);
		    		foreach ($cursor_primero as $documento) {
		    			$documento["fecha"] = date('Y-m-d H:i:s',$documento["_id"]->getTimestamp());
		    			if(isset($documento["autor"])) {
		    				$nombre= "--";
		    				$appat = "--";
		    				$apmat = "--";
		    				if(isset($documento["autor"]["nomb"])) $nombre = $documento["autor"]["nomb"];
		    				if(isset($documento["autor"]["appat"])) $appat = $documento["autor"]["appat"];
		    				if(isset($documento["autor"]["apmat"])) $apmat = $documento["autor"]["apmat"];
		    				$documento["autor"] = $nombre." ".$appat." ".$apmat;
		    			}
		    			$documentos[$nomb_col[0]][$coleccion->getName()][] = $documento;
		    		}
		    	}
        	}else{
        		
        	}
		}
		ksort($documentos);
		if(isset($f->request->data['excel'])){
			$f->response->view("ti/sist.coleccion.extr",array(
				'data'=>$documentos,
			));
		}else{
			header("Content-type:application/json");
        	echo json_encode( $documentos);
		}
	}

	function execute_details(){
		global $f;
		$f->response->view("ti/sist.details");
	}
}
?>