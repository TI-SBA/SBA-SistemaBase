<?php
class Controller_mh_dash extends Controller {
	function execute_index(){
		global $f;
		$f->response->view("mh/dashboard");
	}
}
?>