<?php
class Controller_mh_doct extends Controller {
	function execute_lista(){
		global $f;
		$model = $f->model("mg/entidad")->params(array(
			"filter"=>array(
				array('nomb'=>'roles.medico','value'=>array('$exists'=>true))
			),
			'texto'=>$f->request->data['texto'],
			'fields'=>array(
				'nomb'=>true,
				'appat'=>true,
				'apmat'=>true,
				'tipo_enti'=>true,
				'docident'=>true,
				'roles.trabajador.estado'=>true,
				'roles.trabajador.contrato'=>true,
				'roles.trabajador.organizacion'=>true,
				'roles.trabajador.cargo'=>true,
				'roles.trabajador.funcion'=>true
			),
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows
		))->get("lista");
		$f->response->json( $model );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDBMin;
		$model = null;
		if(isset($data['medico'])){
			$upd = array(
				'fecmod'=>$data['fecmod'],
				'trabajador'=>$data['trabajador'],
				'roles.medico.colegiatura'=>$data['colegiatura']
			);
			$model = $f->model("mg/entidad")->params(array('_id'=>new MongoId($data['medico']['_id']),'data'=>$upd))->save("update");
		}
		$f->response->json($model);
	}
	function execute_edit(){
		global $f;
		$f->response->view("mh/doct.edit");
	}

}