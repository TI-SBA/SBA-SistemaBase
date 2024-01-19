<?php
class Controller_ts_movi extends Controller {
	function execute_index() {
		global $f;
		$f->response->view("ts/movi.efec");
	}
	function execute_cuen() {
		global $f;
		$f->response->view("ts/movi.cuen");
	}
	function execute_ban() {
		global $f;
		$f->response->view("ts/movi.ban");
	}
	function execute_cjb(){
		global $f;
		$f->response->print('
		<table>
			<tr>
				<td><label>Periodo</label></td>
				<td><input type="text" size="11" name="periodo"></td>
				<td><button name="btnExportar">Exportar</button></td>
			</tr>
		</table>
		<div id="mainContent"></div>
		');
	}
	function execute_cjb_body(){
		global $f;	
		$f->response->view("ts/movi.cjb");
	}
	function execute_lista(){
		global $f;
		$params = array(
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows
		);
		$model = $f->model("ts/poli")->params($params)->get("lista");
		$f->response->json( $model );
	}
	function execute_lista_efe(){
		global $f;
		$model = $f->model("ts/moef")->params(array('periodo'=>$f->request->data['periodo']))->get("all_periodo")->items;
		$f->response->json( $model );
	}
	function execute_lista_cue(){
		global $f;
		$model = $f->model("ts/movcue")->params(array('periodo'=>$f->request->data['periodo'],'ctban'=>new MongoId($f->request->data['ctban'])))->get("all_periodo")->items;
		$f->response->json( $model );
	}
	function execute_lista_ban(){
		global $f;
		$model = $f->model("ts/moba")->params(array('periodo'=>$f->request->data['periodo'],'ctban'=>new MongoId($f->request->data['ctban'])))->get("all_periodo")->items;
		$f->response->json( $model );
	}
	function execute_lista_cjb(){
		global $f;
		$model = $f->model("ts/cjba")->params(array("mes"=>$f->request->mes,"ano"=>$f->request->ano))->get("all");
		$array["cuentas_debe"] = array();
		$array["cuentas_haber"] = array();
		$array["items"] = array();
		if($model->items!=null){
			foreach($model->items as $i=>$item){
				if(count($item["organizacion"])>1){
					foreach($item["organizacion"] as $j=>$org){
						$orga = $f->model("mg/orga")->params(array("_id"=>$org["_id"]))->get("one");
						$item["organizacion"][$j] = $orga;
					}
				}
				$item["cuentas_debe"] = array();
				$item["cuentas_haber"] = array();				
				foreach($item["cuentas"] as $cuenta){
					if($cuenta["tipo"]=="D"){
						if(!isset($array["cuentas_debe"][$cuenta["cuenta"]["_id"]->{'$id'}])){
							$array["cuentas_debe"][$cuenta["cuenta"]["_id"]->{'$id'}] = $cuenta["cuenta"];
							$array["cuentas_debe"][$cuenta["cuenta"]["_id"]->{'$id'}]["total"] = floatval($cuenta["monto"]);
						}else{
							$array["cuentas_debe"][$cuenta["cuenta"]["_id"]->{'$id'}]["total"] += floatval($cuenta["monto"]);
						}
						array_push($item["cuentas_debe"],$cuenta);			
					}else{
						if(!isset($array["cuentas_haber"][$cuenta["cuenta"]["_id"]->{'$id'}])){
							$array["cuentas_haber"][$cuenta["cuenta"]["_id"]->{'$id'}] = $cuenta["cuenta"];
							$array["cuentas_haber"][$cuenta["cuenta"]["_id"]->{'$id'}]["total"] = floatval($cuenta["monto"]);
						}else{
							$array["cuentas_haber"][$cuenta["cuenta"]["_id"]->{'$id'}]["total"] += floatval($cuenta["monto"]);
						}
						array_push($item["cuentas_haber"],$cuenta);	
					}				
				}
				array_push($array["items"],$item);
			}
		}
		$array["cuentas_debe"] = array_values($array["cuentas_debe"]);
		$array["cuentas_haber"] = array_values($array["cuentas_haber"]);
		$f->response->json($array);
	}
	function execute_search(){
		global $f;
		$estado = array('$exists'=>true);
		$model = $f->model("ts/poli")->params(array(
			"page"=>$f->request->page,
			"page_rows"=>$f->request->page_rows,
			"texto"=>$f->request->texto
		))->get("search");
		$f->response->json( $model );
	}
	function execute_all(){
		global $f;
		$model = $f->model('ts/poli')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("ts/poli")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['estado'] = 'R';
      		$data['autor'] = array(
				'_id'=>$f->session->enti['_id'],
				'tipo_enti'=>$f->session->enti['tipo_enti'],
				'nomb'=>$f->session->enti['nomb'],
				'cargo'=>array(
					'_id'=>$f->session->enti['roles']['trabajador']['cargo']['_id'],
					'nomb'=>$f->session->enti['roles']['trabajador']['cargo']['nomb'],
					'organizacion'=>$f->session->enti['roles']['trabajador']['organizacion']
				)
			);
			$f->model("ts/poli")->params(array('data'=>$data))->save("insert");
		}else{
			$f->model("ts/poli")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_new(){
		global $f;
		$f->response->view("ts/poli.new");
	}
	function execute_edit(){
		global $f;
		$f->response->view("ts/poli.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("ts/poli.select");
	}
	function execute_details(){
		global $f;
		$f->response->view("ts/poli.details");
	}
}
?>