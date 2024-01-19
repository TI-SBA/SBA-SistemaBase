<?php
class Controller_pr_clas extends Controller {
	function execute_index() {
		global $f;
		$f->response->print("<div style='height:30px;line-height:30px;'>");
		$f->response->view("ci/ci.search");
		$f->response->print('<span name="FilTipo">
			<input type="radio" name="rbtnTipo" id="rbtnTipoI" value="I" checked="checked"><label for="rbtnTipoI">Ingreso</label>
			<input type="radio" name="rbtnTipo" id="rbtnTipoG" value="G"><label for="rbtnTipoG">Gasto</label>
		</span>');
		$f->response->print('<button name="btnAgregar">Nuevo Clasificador</button>');
		$f->response->print("</div>");
		$header_grid = array("cols"=>array(
			0=>array( "nomb"=>"&nbsp;","w"=>10 ),
			1=>array( "nomb"=>"&nbsp;","w"=>30 ),
			2=>array( "nomb"=>"C&oacute;digo","w"=>250 ),
			3=>array( "nomb"=>"Nombre","w"=>300 ),
			4=>array( "nomb"=>"Registrado","w"=>150 )
		));
		$f->response->view("ci/ci.grid",$header_grid);
		$f->response->print("<div class='div-bottom ui-dialog-buttonpane ui-widget-content'>");
		$f->response->view("ci/ci.grid.total");
		$f->response->view("ci/ci.grid.foot");
		$f->response->print("</div>");
	}
	function execute_index2(){
		global $f;
		$f->response->print('<table id="addtree"></table> <div id="paddtree"></div>');
	}
	function execute_three(){
		global $f;
		$param = array();
		if(isset($f->request->data["nodeid"])) $param["nodeid"] = $f->request->data["nodeid"];		
		$model = $f->model("pr/clas")->params($param)->get("node");
		$rows = array();
		if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) { header("Content-type: application/xhtml+xml;charset=utf-8"); } else { header("Content-type: text/xml;charset=utf-8"); } $et = ">"; 
		echo "<?xml version='1.0' encoding='utf-8'?$et\n"; 
		echo "<rows>"; 
		echo "<page>1</page>"; 
		echo "<total>1</total>"; 
		echo "<records>1</records>";
		foreach($model->items as $i=>$item){
			echo "<row>"; 
			echo "<cell>".$item["_id"]->{'$id'}."</cell>";
			echo "<cell>".$item["nomb"]."</cell>";
			echo "</row>";
		}
		echo "</rows>"; 
		//$f->response->json(array("page"=>1,"total"=>1,"records"=>3,"rows"=>$rows));
	}
	function execute_lista(){		
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['tipo']))
			if($f->request->data['tipo']!='')
				$params['tipo'] = $f->request->data['tipo'];
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("pr/clas")->params($params)->get("lista") );
	}
	function execute_lista_2(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("pr/clas")->params($params)->get("lista") );
	}
	function execute_search(){
		global $f;
		$params = array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto);
		if(isset($f->request->data['tipo']))
			$params['tipo'] = $f->request->data['tipo'];
		$model = $f->model("pr/clas")->params($params)->get("search");
		$f->response->json( $model );
	}
	function execute_get(){
		global $f;
		$model = $f->model("pr/clas")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		if(isset($f->request->data["datah"])){
			if(isset($model->items["clasificadores"]["hijos"])){
				foreach($model->items["clasificadores"]["hijos"] as $i=>$hijo){			
					$model->items["clasificadores"]["hijos"][$i] = $f->model("pr/clas")->params(array("_id"=>$hijo["id"]))->get("one")->items;
				}
			}
		}
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			if($data['clasificadores']['padre']==null){
				$data['cod']=(int)$data['cod'];
			}else{
				$data['cod']=$data['cod'];
			}
			$f->model("pr/clas")->params(array('data'=>$data))->save("insert");
			$clas['id'] = $data['_id'];
			$parent = $data['clasificadores']['padre'];
			$f->model("pr/clas")->params(array('_id'=>new MongoId($parent),'data'=>$clas))->save("push");
		}else{
			$f->model("pr/clas")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
		}
		$f->response->print("true");
	}
	function execute_save_order(){
		global $f;
		$data = $f->request->data;
		if(isset($data["clasificadores"])){
			$edit = array();
			foreach($data["clasificadores"] as $i=>$cuen){
				$edit["clasificadores.hijos"][$i]["id"] = new MongoId($cuen);
			}
			$f->model("pr/clas")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$edit))->save("update");
		}
		
		if(isset($data["eliminados"])){
			foreach($data["eliminados"] as $eli){
				$f->model("pr/clas")->params(array('_id'=>$eli))->delete("clas");
			}
		}
	}
	function execute_details(){
		global $f;
		$f->response->view("pr/clas.details");
	}
	function execute_edit(){
		global $f;
		$f->response->view("pr/clas.edit");
	}
	function execute_select(){
		global $f;
		$f->response->view("pr/clas.select");
	}
	function execute_deleteclas(){
		global $f;
		if($f->request->data['padre']!=""){
			$model = $f->model('pr/clas')->params(array("amodificar"=>$f->request->padre,"aeliminar"=>$f->request->id))->save('pull_clas');
		}
		if($f->request->data['hijos']!=""){
			for($i=0;$i<count($f->request->hijos);$i++){
			$model = $f->model('pr/clas')->params(array("_id"=>$f->request->hijos[$i]['id']['$id']))->delete('clas');
			}
		}
		$model = $f->model('pr/clas')->params(array("_id"=>$f->request->id))->delete('clas');	
    	$f->response->print( "true" );
	}
}
?>