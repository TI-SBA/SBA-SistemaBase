<?php
class Controller_dd_repo extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("dd/repo.view");
	}
}
?>