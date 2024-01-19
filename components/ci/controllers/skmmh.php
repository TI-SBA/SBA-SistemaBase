<?php
class Controller_ci_skmmh extends Controller {
	function execute_search_hist(){
		global $f;
		$cod = $f->request->data['cod'];
		$entidad = $f->model('mg/entidad')->params(array(
			'filter'=>array('roles.paciente.hist_cli'=>intval($cod)),
			'fields'=>array(),
			'sort'=>array('_id'=>1)
		))->get('custom_data')->items;
		if($entidad!=null){
			$f->response->json($entidad[0]);
		}else{
			print_r($entidad[0]);
		}
	}
	function execute_search_hist_ch(){
		global $f;
		$cod = $f->request->data['cod'];
		$modulo = 'CH';
		$entidad = $f->model('mg/entidad')->params(array(
			'filter'=>array('roles.paciente.hist_cli'=>intval($cod,'modulo'=>$modulo)),
			'fields'=>array(),
			'sort'=>array('_id'=>1)
		))->get('custom_data')->items;
		if($entidad!=null){
			$f->response->json($entidad[0]);
		}else{
			require('libraries/rpc/phprpc_client.php');
			$client = new PHPRPC_Client();
			$client->setProxy(NULL);
			$client->useService('http://200.10.77.59/skm_mh/index.php');
			$client->setKeyLength(1000);
			$client->setEncryptMode(3);
			$client->setCharset('UTF-8');
			$client->setTimeout(10);
			$entidad = $client->search_hist($cod);
			$data = array(
		    	'nomb'=>$entidad['Nombres'],
		    	'appat'=>$entidad['ApellidoPaterno'],
		    	'apmat'=>$entidad['ApellidoMaterno'],
		    	'doc'=>$entidad['NumeroDocumento'],
		    	'hist'=>$entidad['HistoriaClinica']
			);
			$data['fecreg'] = new MongoDate();
			$enti = array(
				'fecreg'=>new MongoDate(),
				'nomb'=>$data['nomb'],
				'appat'=>$data['appat'],
				'apmat'=>$data['apmat'],
				'fullname'=>$data['nomb'].' '.$data['appat'].' '.$data['apmat'],
				'tipo_enti'=>'P',
				'docident'=>array(0=>array(
					'tipo'=>'DNI',
					'num'=>$data['doc']
				)),
				'roles'=>array(
					'paciente'=>array(
						'centro'=>'MH',
						'hist_cli'=>$data['hist']
					)
				)
			);
			if($enti['nomb']==null){
				$enti = array();
			}else{
				$f->datastore->mg_entidades->insert($enti);
			}
			$f->response->json($enti);
		}
	}
}
?>