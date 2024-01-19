<?php
class Controller_ac_noti extends Controller {
	function execute_index() {
		global $f;
		//
	}
	function execute_last(){
		global $f;
		//session_destroy();
		if($_SESSION['valid']!=true){
			header("HTTP/1.0 403 Session");
			exit;
		}
		session_write_close();
		/*$time = time();
		//while((time() - $time) < 25) {*/
			$model = $f->model("ac/noti")->params(array("_id"=>$f->session->enti['roles']['trabajador']['oficina']['_id']))->get("last");
			if($model->count!=null) {
				$f->model("ac/noti")->params(array("_id"=>$f->session->enti['roles']['trabajador']['oficina']['_id']))->save("sended");
				$f->response->json( array("count"=>$model->count) );
				//break;
			}
			/*usleep(10000);
		}*/
	}
	function execute_lista(){
		global $f;
		$params = array(
			"page"=>$f->request->data['page'],
			"page_rows"=>$f->request->data['page_rows'],
			"_id"=>$f->session->enti['roles']['trabajador']['oficina']['_id']
		);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['filter'])){
			$params['filter'] = $f->request->data['filter'];
			foreach ($params['filter'] as $i=>$filter){
				if(gettype($filter['value'])=="array"){
					if(isset($filter['value']['$exists'])){
						if($filter['value']['$exists']=='true') $filter['value'] = array('$exists'=>1);
						else $filter['value'] = array('$exists'=>0);
					}
				}
				$params['filter'][$i]['value'] = $filter['value'];
			}
		}
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$data = $f->model("ac/noti")->params($params)->get("lista");
		$f->model("ac/noti")->params(array("_id"=>$f->session->enti['roles']['trabajador']['oficina']['_id']))->save("readed");
		$f->response->json( $data );
	}
}
?>