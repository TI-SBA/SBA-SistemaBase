<?php
class Controller_ad_repo extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("ad/repo.view");
	}
}
?>