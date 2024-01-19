<?php
class Controller_pe_conf extends Controller {
	function execute_index(){
		global $f;
		$f->response->view('pe/conf');
	}
	function execute_get(){
		global $f;
		$conf = $f->model('cj/conf')->params(array('cod'=>'PE'))->get('cod')->items;
		$f->response->json($conf);
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
  		$data['fecmod'] = new MongoDate();
  		$data['trabajador'] = $f->session->userDB;
		$conf = $f->model('cj/conf')->params(array('cod'=>'PE'))->get('cod')->items;
		if(isset($data['IGV']))
			$data['IGV']['_id'] = new MongoId($data['IGV']['_id']);
		if(isset($data['cuenta']))
			$data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		if(!isset($conf)){
			$f->model("cj/conf")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("cj/conf")->params(array('_id'=>$conf['_id'],'data'=>$data))->save("update");
		}
		$f->model('ac/log')->params(array(
			'modulo'=>'CJ',
			'bandeja'=>'Configuracion',
			'descr'=>'Se modifico la <b>Configuracion de Recursos Humanos</b>'
		))->save('insert');
		$f->response->print('true');
	}
	function execute_generar(){
		global $f;
		$ini = $f->request->data['ini'];
		$fin = $f->request->data['fin'];
		$trabs = $f->model('mg/entidad')->params(array(
			'filter'=>array(
				array('nomb'=>'roles.trabajador.turno','value'=>array('$exists'=>true))
			)
		))->get('lista')->items;
		foreach ($trabs as $i=>$trab) {
			$turno = $f->model('pe/turn')->params(array('_id'=>$trab['roles']['trabajador']['turno']['_id']))->get('one')->items;
			if($turno!=null){
				if(!isset($turno['tipo']))
					$turno['tipo'] = 'N';
				$f->datastore->mg_entidades->update(array('_id'=>$trab['_id']),array('$set'=>array('roles.trabajador.turno.tipo'=>$turno['tipo'])));
			}
		}
		$f->response->json(array('rpta'=>true));
	}
	function execute_generar_view(){
		global $f;
		$f->response->view('pe/conf.generar');
	}
	function execute_resetear_conceptos(){
		global $f;
		$conceptos = $f->datastore->pe_conceptos->find(array('historico'=>array('$exists'=>true)));
		foreach ($conceptos as $i=>$conc) {
			if(isset($conc['historico'])){
				foreach ($conc['historico'] as $j=>$item) {
					$item['concepto'] = $conc['_id'];
					$f->model('pe/conh')->params(array('data'=>$item))->save('insert');
				}
				$f->datastore->pe_conceptos->update(array('_id'=>$conc['_id']),array('$unset'=>array('historico'=>1)));
			}
		}
		print_r('true');
	}
	function execute_resetear_trabajadores(){
		global $f;
		$trabajadores = $f->datastore->mg_entidades->find(array('roles.trabajador.historico'=>array('$exists'=>true)));
		foreach ($trabajadores as $i=>$trab) {
			if(isset($trab['roles']['trabajador']['historico'])){
				foreach ($trab['roles']['trabajador']['historico'] as $j=>$item) {
					$item['trabajador'] = $trab['_id'];
					$f->model('pe/trah')->params(array('data'=>$item))->save('insert');
				}
				$f->datastore->mg_entidades->update(array('_id'=>$trab['_id']),array('$unset'=>array('roles.trabajador.historico'=>1)));
			}
		}
		print_r('true');
	}
}
?>