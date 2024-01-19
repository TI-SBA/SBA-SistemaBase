<?php
class Controller_dd_rped extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("dd/rped.view");
	}
}
?>