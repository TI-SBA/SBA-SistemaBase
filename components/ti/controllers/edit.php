<?php
class Controller_ti_edit extends Controller {
	function execute_index(){
		global $f;
		$f->response->view('ti/edit');
	}
	function execute_lista(){
		global $f;
		$data = $f->request->data;
		$f->library('ftp_sis');
		$ftp = new ftp_sis();
		$id_ftp = $ftp->ConectarFTP();
		$empty = false;
		if(isset($data['dir'])){
			if (@ftp_chdir($id_ftp,$data['dir'])) {
				//echo "Current directory is now: " . ftp_pwd($conn_id) . "\n";
			} else {
				if (@ftp_chdir($id_ftp,$data['ruta'])) {
					//echo "Current directory is now: " . ftp_pwd($conn_id) . "\n";
				} else { 
					$empty = true;
				}
			}
		}else{
			$data['dir'] = '/';
		}
		if($empty==false){
			$rpta = array(
				'ruta'=>ftp_pwd($id_ftp),
				'items'=>array()
			);
			$lista=ftp_nlist($id_ftp,'');
			$lista=array_reverse($lista);
			while ($item=array_pop($lista)){
				$tamano=number_format(((ftp_size($id_ftp,$item))/1024),2)." Kb";
				if($tamano=="-0.00 Kb"){
					$tipo = 'F';
					$item=$item;
					$tamano="&nbsp;";
					$fecha="&nbsp;";
				}else{
					$tipo = 'A';
					$fecha=date("Y-m-d h:i:s", ftp_mdtm($id_ftp,$item));
				}
				$rpta['items'][] = array(
					'tipo'=>$tipo,
					'item'=>$item,
					'tamano'=>$tamano,
					'fecha'=>$fecha
				);
			}
		}else{
			$rpta = array(
				'ruta'=>$data['ruta'],
				'items'=>array()
			);
		}
		$f->response->json($rpta);
	}
	function execute_get(){
		global $f;
		$url = $f->request->data['url'];
		$f->response->print(file_get_contents($url));
	}
	function execute_save(){
		global $f;
		$path = $f->request->data['p'];
		$content = $f->request->data['c'];
		$file = fopen($path,"w");
		fwrite($file,$content);
		fclose($file);
		$f->response->print('true');
	}
}
?>