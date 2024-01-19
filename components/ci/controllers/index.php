<?php
class Controller_ci_index extends Controller {
	function execute_index() {
		global $f;
		if ($f->session->valid){
			$trabs = $f->model('mg/entidad')->params(array('filter'=>array(
				array('nomb'=>'roles.trabajador.fecnac','value'=>array('$exists'=>true))
			),'fields'=>array(
				'nomb'=>1,
				'appat'=>1,
				'apmat'=>1,
				'roles.trabajador.fecnac'=>1
			)))->get('search_all')->items;
			if(isset($f->request->data['new'])){
				$f->response->view("ci/bootstrap-inspinia",array('trabs'=>$trabs));
			}else
				$f->response->view("ci/mainWindow");
		} else {
			$f->response->view("ci/loginWindow");
			$f->session->login_msg= "Ingrese Usuario o Contrase&ntilde;a";
		}
	}
	function execute_login() {
		global $f;
		$user = $f->model("ac/user")->params(array("userid"=>$f->request->l_user,"passwd"=>$f->request->l_pass))->get("login");
		if(is_null($user->items['owner']['_id'])){
			$f->session->valid = false;
			$f->session->login_msg= "El Usuario o Contrase&ntilde;a no son v&aacute;lidos";
			$f->response->redirect($f->request->root);
			return false;
		}
		else if(isset($user->items['status']) && $user->items['status']=="Disabled")
		{
			$data['modulo']="AC";
			$data['bandeja']="Loggin User disabled";
			$data['descr']="Intento de Loggin a través de una cuenta desabilitada";
			$data['usuario']=array("_id"=>$user->items['_id'],"userid"=>$f->request->l_user);
			$data['entidad']=array("_id"=>$user->items['owner']['_id'],"nomb"=>$user->items['owner']['nomb'],"appat"=>$user->items['owner']['appat'],
			"apmat"=>$user->items['owner']['apmat']);
			$create_log=$f->model("ac/log")->params($data)->save("loggindown");
			$f->session->valid = false;
			$f->session->login_msg= "El Usuario o Contrase&ntilde;a no son v&aacute;lidos";
			$f->response->redirect($f->request->root);
			return false;
		}
		$model = $f->model('ac/user')->params(array("userid"=>$f->request->l_user))->get('permisos');
		$enti = $f->model("mg/entidad")->params(array("_id"=>new MongoId($user->items['owner']['_id'])))->get("one");
	
		if(!isset($enti->items['roles']['trabajador'])){
			$f->session->valid = false;
			$f->session->login_msg= "El Usuario no es v&aacute;lido, ac&eacute;rquese a inform&aacute;tica";
			$f->response->redirect($f->request->root);
			return false;
		}
		$permisos = array();$pre="";
		foreach ($model->items as $permiso){
			if($pre!=substr($permiso['taskid'],0,2)){
				$permisos[substr($permiso['taskid'],0,2)]=1;
				$pre=substr($permiso['taskid'],0,2);
			}
			$permisos[$permiso['taskid']]=1;
		}
		$f->session->tasks=$permisos;
		$f->session->titular=$f->model("mg/entidad")->get("titular")->items;
		
		$f->session->user=$user->items;
		$f->session->enti = $enti->items;
		
		$f->library('helpers');
		$helper=new helper();
		$f->session->userDB = $helper->getUser();
		$f->session->userDBMin = $helper->getUserMin();
		
		if($f->session->user!=null){
			$f->session->valid = true;
			//$f->response->redirect($f->request->root);
		}else{
			$f->session->valid = false;
			$f->session->login_msg= "El Usuario o Contrase&ntilde;a no son v&aacute;lidos";
			//$f->response->redirect($f->request->root);
		}
	}
	function execute_logout() {
		global $f;
		$f->session->valid = false;
		$f->response->redirect($f->request->root);
	}
	function execute_skin_config(){
		global $f;
		$f->response->view("ci/skin_config");
	}
	function execute_dashboard() {
		global $f;
		$f->response->view("ci/dashboard");
	}
	function execute_about() {
		global $f;
		$f->response->view("ci/about");
	}
	function execute_eliminar(){
		global $f;
		$f->load->view("ci/dialogEliminar");
	}
	function execute_delete(){
		global $f;
		$f->response->view("ci/ci.delete");
	}
	function execute_kunanui(){
		global $f;
		$f->response->view("ci/kunanui");
	}
	function execute_lopsem(){
		global $f;
		$f->response->view("ci/lopsem");
	}
	function execute_error(){
		global $f;
		$f->response->view("ci/ci.error");
	}
	function execute_view_data(){
		global $f;
		print_r($f->request);
	}
	function execute_import(){
		global $f;
		die();
		ini_set ('auto_detect_line_endings','1');
		$fp = fopen ('lg.csv', 'r');
		$i = 1;
		//$unid = array();
		while ($data = fgetcsv ($fp, 1000, ',')){
			print_r($data[2]);
			$unid = $f->datastore->lg_unidades->findOne(array('nomb'=>$data[2]));
			$descr = $data[1];
			if($desr=='') $descr = $data[0];
			$item = array(
				'nomb'=>$data[0],
				'descr'=>$descr,
				'unidad'=>array(
					'_id'=>$unid['_id'],
					'nomb'=>$unid['nomb']
				),
				'precio'=>$data[3],
				'tipo_producto'=>$data[4],
				'estado'=>'H',
				'cod'=>$i
			);
			$i++;
			$f->datastore->lg_productos->insert($item);
			//print_r($item);
			/*if(in_array($data[2],$unid)){
				//
			}else{
				$und = array(
					'nomb'=>$data[2],
					'abrev'=>$data[2]
				);
				$f->datastore->lg_unidades->insert($und);
				$unid[] = $data[2];
			}*/
		}
		fclose($fp);
	}
	function execute_reset_enti(){
		global $f;
		$operaciones = $f->datastore->cm_operaciones->find();
		foreach ($operaciones as $ob){
			if(isset($ob['ocupante'])){
				$entidad = $f->datastore->mg_entidades->findOne(array(_id=>$ob['ocupante']['_id']));
				$name = $entidad['nomb'];
				if(isset($entidad['appat'])){
					$name .= ' '.$entidad['appat'].' '.$entidad['apmat'];
				}
				if($entidad['docident'][0]['tipo']=='DNI'){
					$f->datastore->cm_operaciones->update(
						array('_id'=>$ob['_id']),
						array('$set'=>array(
							'ocupante.n'=>$name,
							'ocupante.doc'=>$entidad['docident'][0]['num']
						))
					);
				}else{
					$f->datastore->cm_operaciones->update(
						array('_id'=>$ob['_id']),
						array('$set'=>array(
							'ocupante.n'=>$name,
							'ocupante.doc'=>$entidad['docident'][0]['num']
						))
					);
				}
			}
		}
	}
	function execute_view_chat(){
		global $f;
		$f->response->view('ci/chat');
	}
	function execute_view_observ(){
		global $f;
		$f->response->view('ci/ci.observ');
	}
	function execute_mail(){
		global $f;
		error_reporting(-1);
ini_set('display_errors', 'On');
set_error_handler("var_dump");
		//mail('informatica@sbparequipa.gob.pe', "Prueba", $f->request->data['data'], "From: informatica@sbparequipa.gob.pe");
		$headers = array("From: informatica@sbparequipa.gob.pe",
		    "Reply-To: informatica@sbparequipa.gob.pe",
		    "X-Mailer: PHP/" . PHP_VERSION
		);
		$headers = implode("\r\n", $headers);
		if(mail('informatica@sbparequipa.gob.pe', "Prueba", 'aasdasdasdasd',$headers)===TRUE)
			$f->response->json(array('a'=>1));
		else
			$f->response->json(array('b'=>2));
	}
	function phpinfo(){
		global $f;
		phpinfo();
	}
	function test_info(){
		global $f;
		phpinfo();
	}
	function execute_all_data(){
		global $f;
		$rpta = array();
		$rpta['almacenes'] = $f->model('lg/alma')->get('all')->items;
		$rpta['programas'] = $f->model("mg/prog")->params(array('filter'=>array('estado'=>'H'),'fields'=>array('nomb'=>true)))->get("all")->items;
		//$rpta['almacenes'] = $f->model('lg/alma')->get('all')->items;
		$rpta['variables'] = $f->model("mg/vari")->params(array("fields"=>array('cod'=>true,'nomb'=>true,'valor'=>true)))->get("all")->items;
		$rpta['cajas'] = array();
		if(isset($f->session->enti['roles']['cajero'])){
			foreach ($f->session->enti['roles']['cajero']['cajas'] as $caja){
				$rpta['cajas'][] = $f->model("cj/caja")->params(array("_id"=>new MongoId($caja)))->get("one")->items;
			}
		}
		$rpta['ctban'] = $f->model("ts/ctban")->get("all")->items;
		$f->response->json( $rpta );
	}
}
?>