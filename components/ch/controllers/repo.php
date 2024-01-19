<?php
class Controller_ch_repo extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("ch/repo.view");
	}
}
?>