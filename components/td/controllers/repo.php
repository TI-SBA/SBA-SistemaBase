<?php
class Controller_td_repo extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("td/repo.grid");
	}
	function execute_edit_all(){
		global $f;
		$f->response->view("td/repo.edit");
	}
	function execute_print(){
		global $f;
		$model = $f->model('td/expd')->params(array(
			'ano'=>$f->request->data['a'],
			'mes'=>$f->request->data['m'],
			'cant'=>$f->request->data['c']
		))->get('repo')->items;
		$data = array(
			'params'=>array(
				'ano'=>$f->request->data['a'],
				'mes'=>$f->request->data['m'],
				'cant'=>$f->request->data['c']
			),
			'flag'=>false,
			'ubic'=>array(),
			'orga'=>array(),
			'flujos'=>array(
				'cant'=>array(0,0,0),
				'tipo'=>array('Iniciación','Reconsideración','Apelación')
			),
			'estado'=>array(
				'cant'=>array(0,0),
				'cant_tupa'=>array(0,0),
				'tipo'=>array('Pendiente','Concluido'),
				'color'=>array('#CCCCCC','#003265')
			),
			'resolu'=>array(
				'cant'=>array(0,0),
				'tipo'=>array('Rechazado','Aceptado'),
				'color'=>array('#CC0000','#006532')
			)
		);
		$org = array();
		if($model!=null){
			$data['flag'] = true;
			foreach ($model as $expd){
				//$ind = array_search($expd['traslados'][sizeof($expd['traslados'])-1]['origen']['organizacion']['_id'], $org);
				$ind = array_search($expd['ubicacion']['_id'], $org);
				if($ind===false){
					$org[] = $expd['ubicacion']['_id'];
					$nomb = $expd['ubicacion']['nomb'];
					$tmp = explode(" ", $nomb);
					$nomb = "";
					foreach ($tmp as $t){
						if(strlen($nomb)>9&&strlen($nomb)<19) $nomb .= "\n".$t;
						else $nomb .= ' '.$t;
					}
					$data['orga'][] = $nomb;
					$data['ubic'][] = 1;
				}else{
					$data['ubic'][$ind]++;
				}
				if(isset($expd['flujos']['apelacion'])){
					$data['flujos']['cant'][2]++;
				}elseif(isset($expd['flujos']['reconsideracion'])){
					$data['flujos']['cant'][1]++;
				}else{
					$data['flujos']['cant'][0]++;
				}
				if($expd['estado']=='P'){
					$data['estado']['cant'][0]++;
					if(isset($expd['tupa']))
						$data['estado']['cant_tupa'][0]++;
				}else{
					$data['estado']['cant'][1]++;
					if(isset($expd['tupa']))
						$data['estado']['cant_tupa'][1]++;
					if($expd['respuesta']=='Negativo'){
						$data['resolu']['cant'][0]++;
					}else{
						$data['resolu']['cant'][1]++;
					}
				}
			}
		}
		$f->response->view("td/repo.print",$data);
	}
	function execute_print_all(){
		global $f;
		$model = $f->model('td/expd')->params(array(
			"desde"=>$f->request->desde,
			"hasta"=>$f->request->hasta,
			"usuario"=>$f->request->usuario,
			"oficina"=>$f->request->oficina,
			"proc"=>$f->request->proc,
			"venc"=>$f->request->venc,
			"noaten"=>$f->request->noaten,
			"tipo"=>$f->request->tipo,
			"estado"=>$f->request->estado
		))->get('all_filter');
		$model->filter=array(
			"desde"=>$f->request->desde,
			"hasta"=>$f->request->hasta,
			"usuario"=>$f->request->usuario_label,
			"oficina"=>$f->request->oficina_label,
			"proc"=>$f->request->proc_label,
			"venc"=>$f->request->venc,
			"noaten"=>$f->request->noaten,
			"tipo"=>$f->request->tipo,
			"estado"=>$f->request->estado
		);
		//$f->response->print(count($model->items));
		$f->response->view("td/expd.print",$model);
	}
	function execute_print_all_archivado(){
		global $f;
		$model = $f->model('td/expd')->params(array(
			"desde"=>$f->request->desde,
			"hasta"=>$f->request->hasta,
			"archivado"=>$f->request->archivado
		))->get('all_filter');
		$model->filter=array(
			"desde"=>$f->request->desde,
			"hasta"=>$f->request->hasta,
			"archivado"=>$f->request->archivado
		);
		//$f->response->print(count($model->items));
		$f->response->view("td/expd.print",$model);
	}
	function execute_print_all_doc(){
		global $f;
		$model = $f->model('td/expd')->params(array(
			'ano'=>$f->request->data['a'],
			'mes'=>$f->request->data['m'],
			'cant'=>$f->request->data['c']
		))->get('repo')->items;
		$ini = new MongoDate(strtotime($f->request->data['a']."-".$f->request->data['m']."-01"));
		$fin = new MongoDate(strtotime($f->request->data['a']."-".$f->request->data['m']."-01 +".$f->request->data['c']." month"));
		$data = array(
			'params'=>array(
				'ano'=>$f->request->data['a'],
				'mes'=>$f->request->data['m'],
				'cant'=>$f->request->data['c']
			),
			'total_all'=>0,
			'flag'=>false,
			'orga_ini'=>array(),
			'orga_array'=>array(),
			'orga_expd'=>array(),
			'orga'=>array(),
			'orga_descr'=>array()
		);
		$org = array();
		if($model!=null){
			$data['flag'] = true;
			foreach ($model as $expd){
				$data['total_all']++;
				$tmp_descr = false;
				$ind = array_search($expd['ubicacion']['_id'], $org);
				if($ind===false){
					$org[] = $expd['ubicacion']['_id'];
					$nomb = $expd['ubicacion']['nomb'];
					$tmp = explode(" ", $nomb);
					$nomb = "";
					foreach ($tmp as $t){
						if(strlen($nomb)>9&&strlen($nomb)<19) $nomb .= "\n".$t;
						else $nomb .= ' '.$t;
					}
					$data['orga'][] = $nomb;
					$data['orga_descr'][] = $expd['num'];
					$data['orga_array'][] = 0;
					$data['orga_expd'][] = 0;
					$data['orga_ini'][] = 1;
				}else{
					$data['orga_ini'][$ind]++;
					if($tmp_descr==false){
						$data['orga_descr'][$ind] .= ', '.$expd['num'];
						$tmp_descr = true;
					}
				}
				foreach ($expd['documentos'] as $doc) {
					if($doc['fecreg']->sec<=$fin->sec && $doc['fecreg']->sec>=$ini->sec){
						$doc['expediente'] = $expd['num'];
						$ind = array_search($doc['organizacion']['_id'], $org);
						if($ind===false){
							$org[] = $doc['organizacion']['_id'];
							$nomb = $doc['organizacion']['nomb'];
							$tmp = explode(" ", $nomb);
							$nomb = "";
							foreach ($tmp as $t){
								if(strlen($nomb)>9&&strlen($nomb)<19) $nomb .= "\n".$t;
								else $nomb .= ' '.$t;
							}
							$data['orga'][] = $nomb;
							//$data['orga_array'][] = array($doc);
							$data['orga_descr'][] = $expd['num'];
							$data['orga_array'][] = 1;
							$data['orga_expd'][] = 0;
							$data['orga_ini'][] = 0;
						}else{
							$data['orga_array'][$ind]++;
							if($tmp_descr==false){
								$data['orga_descr'][$ind] .= ', '.$expd['num'];
								$tmp_descr = true;
							}
							//$data['orga_array'][$ind][] = $doc;
						}
					}
				}
				foreach ($expd['traslados'] as $tras) {
					$ind = array_search($tras['origen']['organizacion']['_id'], $org);
					if($ind===false){
						$org[] = $tras['origen']['organizacion']['_id'];
						$nomb = $tras['origen']['organizacion']['nomb'];
						$tmp = explode(" ", $nomb);
						$nomb = "";
						foreach ($tmp as $t){
							if(strlen($nomb)>9&&strlen($nomb)<19) $nomb .= "\n".$t;
							else $nomb .= ' '.$t;
						}
						$data['orga'][] = $nomb;
						$data['orga_descr'][] = $expd['num'];
						$data['orga_expd'][] = 1;
						$data['orga_array'][] = 0;
						$data['orga_ini'][] = 0;
					}else{
						$data['orga_expd'][$ind]++;
						if($tmp_descr==false){
							$data['orga_descr'][$ind] .= ', '.$expd['num'];
							$tmp_descr = true;
						}
					}
				}
			}
		}
		//print_r($model);die();
		//print_r($data['orga']);die();
		$f->response->view("td/repo.doc.print",$data);
	}
	function execute_total(){
		
		
		
		
		/*
		 * ESTE REPORTE SE HACE DE TODAS LAS AREAS
		 * PRIMERO UN MES LUEGO OTRO (SON TRES MESES) Y AL FINAL EL TOTALIZADO 
		 */
		
		
		
		
		global $f;
		//echo $f->request->data['ini'];die();
		$ini = new MongoDate(strtotime($f->request->data['ini']." -1 hour"));
		$fin = new MongoDate(strtotime($f->request->data['fin']." +1 hour"));
		$expds = $f->datastore->td_expedientes->find(array(
			'documentos.fecreg'=> array('$gte' => $ini, '$lte' => $fin)
		));
		//print_r($exdps);die();
		$docs = 0;
		$docs_tr = 0;
		$doc_ini = 0;
		$movs = 0;
		$movs_tr = 0;
		$tra_i = 0;
		$tra_e = 0;
		$doc_int = 0;
		$doc_ext = 0;
		foreach ($expds as $expd) {
			foreach ($expd['documentos'] as $k => $doc) {
				if($doc['fecreg']->sec<$fin->sec && $doc['fecreg']->sec>$ini->sec){
					$docs++;
					if($doc['organizacion']['_id']==new MongoId('55660f96cc1e9094090000ea')){
						$docs_tr++;
					}
					if($k==0){
						$doc_ini++;
						if(isset($expd['tupa'])){
							$doc_ext++;
						}else{
							$doc_int++;
						}
					}
				}
			}
			foreach ($expd['traslados'] as $k => $flujo) {
				if($flujo['origen']['fecreg']->sec<$fin->sec && $flujo['origen']['fecreg']->sec>$ini->sec){
					$movs++;
					if($flujo['origen']['organizacion']['_id']==new MongoId('55660f96cc1e9094090000ea')){
						$movs_tr++;
					}
					if(isset($expd['tupa'])){
						$tra_e++;
					}else{
						$tra_i++;
					}
				}
			}
		}
		print_r(array(
			'TOTAL_DOCUMENTOS'=>$docs,
			/*'TOTAL_DOCUMENTOS_TRAMITE'=>$docs_tr,
			'TOTAL_MOVIMIENTOS'=>$movs,
			'TOTAL_MOVIMIENTOS_TRAMITE'=>$movs_tr,
			'TRAMITES_EXTERNOS'=>$tra_e,
			'TRAMITES_INTERNOS'=>$tra_i,*/
			'DOCUMENTOS_INICIALES'=>$doc_ini,
			'DOCUMENTOS_AGREGADOS'=>$docs-$doc_ini,
			'DOC_INT'=>$doc_int,
			'DOC_EXT'=>$doc_ext
		));
		//644 TRAMITE DOCUMENTARIO
		//5731 EN TOTAL
	}
}
?>