<?php
class Controller_ti_back extends Controller {
	function execute_index(){
		global $f;
		$f->response->view('ti/back');
	}
	function execute_save(){
		global $f;
		set_time_limit(0);
		$f->library('mongodumper');
		$dumper = new MongoDumper("c:/backup");
		$dumper->run("beneficencia", true);
		$f->response->print('true');
	}
}
?>