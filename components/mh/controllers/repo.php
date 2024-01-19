<?php
class Controller_mh_repo extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("mh/repo.view");
	}
}
?>