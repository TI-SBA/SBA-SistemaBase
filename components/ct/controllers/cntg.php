<?php
class Controller_ct_cntg extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("ct/cntg.main");
	}
}
?>