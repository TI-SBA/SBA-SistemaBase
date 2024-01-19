<?php
class Controller_pr_plan extends Controller {
	function execute_index() {
	}
	function execute_index_prog() {
		global $f;
		$f->response->print('
			<div>
				<table>
					<tr>
						<td><label>Periodo</label></td>
						<td><input type="text" name="periodo" style="width: 50px"></td>
						<td><label name="organomb"></label></td>
						<td name="FilOrga">
						<input type="hidden" name="organizacion" value="">
						<span name="orgainput">
						<input type="radio" name="rbtnOrga" id="rbtnOrgaSelect" value="S"><label for="rbtnOrgaSelect">Seleccionar</label>
						<input type="radio" name="rbtnOrga" id="rbtnOrgaX" value="X" checked="checked"><label for="rbtnOrgaX">X</label>
						</span>
						</td>
						<td name="estadolabel"></td>
						<td>&nbsp;<button name="btnImprimir">Imprimir</button></td>
						<td>&nbsp;<button name="btnAprobar">Aprobar</button></td>	
						<td>&nbsp;<button name="btnAgregar">Nueva Actividad</button></td>		
					</tr>
				</table>
			</div>		
		');
		$f->response->view("pr/planprog");
	}
	function execute_index_prog_v1() {
		global $f;
		$f->response->print('
			<div>
				<table>
					<tr>
						<td><label>Periodo</label></td>
						<td><input type="text" name="periodo" style="width: 50px"></td>
						<td><label name="organomb"></label></td>
						<td name="FilOrga">
						<input type="hidden" name="programa" value="">
						<span name="orgainput">
						<input type="radio" name="rbtnOrga" id="rbtnOrgaSelect" value="S"><label for="rbtnOrgaSelect">Seleccionar</label>
						<input type="radio" name="rbtnOrga" id="rbtnOrgaX" value="X" checked="checked"><label for="rbtnOrgaX">X</label>
						</span>
						</td>
						<td name="estadolabel"></td>
						<td>&nbsp;<button name="btnImprimir">Imprimir</button></td>
						<td>&nbsp;<button name="btnAprobar">Aprobar</button></td>	
						<td>&nbsp;<button name="btnAgregar">Nueva Actividad</button></td>		
					</tr>
				</table>
			</div>		
		');
		$f->response->view("pr/planprog");
	}
	function execute_index_ejec() {
		global $f;
		$f->response->print("<div>");
		$f->response->print('Periodo : <input type="text" name="periodo" style="width: 50px">');
		$f->response->print('<td><label name="organomb"></label></td>
			<span name="FilOrga">
			<input type="hidden" name="organizacion" value="">
			<span name="orgainput">
			<input type="radio" name="rbtnOrga" id="rbtnOrgaSelect" value="S"><label for="rbtnOrgaSelect">Seleccionar</label>
			<input type="radio" name="rbtnOrga" id="rbtnOrgaX" value="X" checked="checked"><label for="rbtnOrgaX">X</label>
			</span>
			</span>');
		$f->response->print('&nbsp;Trimestre : <select name="trimestre">
				<option value="1">I Trimestre</option>
				<option value="2">II Trimestre</option>
				<option value="3">III Trimestre</option>
				<option value="4">IV Trimestre</option>
		</select>');
		$f->response->print('<button name="btnImprimir">Imprimir</button>');
		$f->response->print('<button name="btnAgregarAct">Nueva Actividad</button>');
		$f->response->print('<button name="btnAgregar">Evaluaci&oacute;n</button>');
		$f->response->print('<button name="btnCerrarTrim">Cerrar Trimestre</button>');
		$f->response->print('<button name="btnImprimirEva">Imprimir Evaluaci&oacute;n</button>');
		$f->response->print("</div>");
		
		$f->response->view("pr/planejec");
	}
	function execute_index_ejec_v1() {
		global $f;
		$f->response->print("<div>");
		$f->response->print('Periodo : <input type="text" name="periodo" style="width: 50px">');
		$f->response->print('<td><label name="organomb"></label></td>
			<span name="FilOrga">
			<input type="hidden" name="programa" value="">
			<span name="orgainput">
			<input type="radio" name="rbtnOrga" id="rbtnOrgaSelect" value="S"><label for="rbtnOrgaSelect">Seleccionar</label>
			<input type="radio" name="rbtnOrga" id="rbtnOrgaX" value="X" checked="checked"><label for="rbtnOrgaX">X</label>
			</span>
			</span>');
		$f->response->print('&nbsp;Trimestre : <select name="trimestre">
				<option value="1">I Trimestre</option>
				<option value="2">II Trimestre</option>
				<option value="3">III Trimestre</option>
				<option value="4">IV Trimestre</option>
		</select>');
		$f->response->print('<button name="btnImprimir">Imprimir</button>');
		$f->response->print('<button name="btnAgregarAct">Nueva Actividad</button>');
		$f->response->print('<button name="btnAgregar">Evaluaci&oacute;n</button>');
		$f->response->print('<button name="btnCerrarTrim">Cerrar Trimestre</button>');
		$f->response->print('<button name="btnImprimirEva">Imprimir Evaluaci&oacute;n</button>');
		$f->response->print("</div>");
		
		$f->response->view("pr/planejec");
	}
	function execute_prog_lista(){
		global $f;
		$model = $f->model("pr/plan")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"etapa"=>"","organizacion"=>$f->request->organizacion,"periodo"=>$f->request->periodo))->get("lista");
		$f->response->json( $model );
	}

	function execute_prog_lista_v1(){
		global $f;
		$model = $f->model("pr/plan")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"etapa"=>"","programa"=>$f->request->programa,"periodo"=>$f->request->periodo))->get("lista_v1");
		$f->response->json( $model );
	}

	function execute_ejec_lista(){
		global $f;
		$model = $f->model("pr/plan")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"etapa"=>$f->request->etapa,"organizacion"=>$f->request->organizacion,"periodo"=>$f->request->periodo))->get("lista");
		$model2 = $f->model("pr/plan")->params(array("periodo"=>$f->request->periodo,"organizacion"=>$f->request->organizacion,"trimestre"=>$f->request->trimestre))->get("eval");
		$model->eval = $model2->items;
		$f->response->json( $model );
	}

	function execute_ejec_lista_v1(){
		global $f;
		$model = $f->model("pr/plan")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"etapa"=>$f->request->etapa,"programa"=>$f->request->programa,"periodo"=>$f->request->periodo))->get("lista_v1");
		$model2 = $f->model("pr/plan")->params(array("periodo"=>$f->request->periodo,"programa"=>$f->request->programa,"trimestre"=>$f->request->trimestre))->get("eval_v1");
		$model->eval = $model2->items;
		$f->response->json( $model );
	}

	function execute_prog_print(){
		global $f;
		$model = $f->model("pr/plan")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"etapa"=>"","organizacion"=>$f->request->organizacion,"periodo"=>$f->request->periodo))->get("lista");
		$model->filtros = $f->request->data;
		$f->response->view("pr/planprog.print",$model);
	}

	function execute_prog_print_v1(){
		global $f;
		$model = $f->model("pr/plan")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"etapa"=>"","programa"=>$f->request->programa,"periodo"=>$f->request->periodo))->get("lista_v1");
		$model->filtros = $f->request->data;
		$f->response->view("pr/planprog.print",$model);
	}

	function execute_ejec_print(){
		global $f;
		$model = $f->model("pr/plan")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"etapa"=>$f->request->etapa,"organizacion"=>$f->request->organizacion,"periodo"=>$f->request->periodo))->get("lista");
		$model->trim_data = $f->request->trimestre;
		$model->filtros = $f->request->data;
		$f->response->view("pr/planejec.print",$model);
	}
	function execute_ejec_print_v1(){
		global $f;
		$model = $f->model("pr/plan")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"etapa"=>$f->request->etapa,"programa"=>$f->request->programa,"periodo"=>$f->request->periodo))->get("lista_v1");
		$model->trim_data = $f->request->trimestre;
		$model->filtros = $f->request->data;
		$f->response->view("pr/planejec.print",$model);
	}
	function execute_search(){
		global $f;
		$model = $f->model("pr/clas")->params(array("page"=>$f->request->page,"page_rows"=>$f->request->page_rows,"texto"=>$f->request->texto,"tipo"=>$f->request->tipo))->get("search");
		$f->response->json( $model );
	}
	function execute_get_plan_eval(){
		global $f;
		$model = $f->model("pr/plan")->params(array("periodo"=>$f->request->periodo,"organizacion"=>$f->request->organizacion,"trimestre"=>$f->request->trimestre))->get("eval");
		$f->response->json( $model->items );
	}
	function execute_get_plan_eval_v1(){
		global $f;
		$model = $f->model("pr/plan")->params(array("periodo"=>$f->request->periodo,"programa"=>$f->request->programa,"trimestre"=>$f->request->trimestre))->get("eval_v1");
		$f->response->json( $model->items );
	}
	function execute_get_eval(){
		global $f;
		$model = $f->model("pr/plan")->params(array("_id"=>new MongoId($f->request->id)))->get("one_eval");
		$f->response->json( $model->items );
	}
	function execute_get(){
		global $f;
		$model = $f->model("pr/plan")->params(array("_id"=>new MongoId($f->request->id)))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save_activ(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data["organizacion"] = $f->session->userDB["cargo"]["organizacion"];
			$data["organizacion"]["_id"] = $data["organizacion"]["_id"]->{'$id'};
			$data["organizacion"]["actividad"]["_id"] = $data["organizacion"]["actividad"]["_id"]->{'$id'};
			$data["organizacion"]["componente"]["_id"] = $data["organizacion"]["componente"]["_id"]->{'$id'};
			$data['trabajador'] = $f->session->userDB;
			$f->model("pr/plan")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Plan Operativo: Programaci&oacute;n',
				'descr'=>'Se Cre&oacute; La Actividad <b>'.$data['actividad'].'</b> para <b>'.$data['organizacion']['nomb'].'</b> del periodo <b>'.$data['periodo'].'</b>.'
			))->save('insert');
		}else{
			$trimestre = $data['trimestre'];
			if($trimestre=="1"){
				$set["metas.ejecutadas.0"]=$data["metas"]["ejecutadas"][0];
				$set["metas.ejecutadas.1"]=$data["metas"]["ejecutadas"][1];
				$set["metas.ejecutadas.2"]=$data["metas"]["ejecutadas"][2];
			}elseif($trimestre=="2"){
				$set["metas.ejecutadas.3"]=$data["metas"]["ejecutadas"][0];
				$set["metas.ejecutadas.4"]=$data["metas"]["ejecutadas"][1];
				$set["metas.ejecutadas.5"]=$data["metas"]["ejecutadas"][2];
			}elseif($trimestre=="3"){
				$set["metas.ejecutadas.6"]=$data["metas"]["ejecutadas"][0];
				$set["metas.ejecutadas.7"]=$data["metas"]["ejecutadas"][1];
				$set["metas.ejecutadas.8"]=$data["metas"]["ejecutadas"][2];
			}elseif($trimestre=="4"){
				$set["metas.ejecutadas.9"]=$data["metas"]["ejecutadas"][0];
				$set["metas.ejecutadas.10"]=$data["metas"]["ejecutadas"][1];
				$set["metas.ejecutadas.11"]=$data["metas"]["ejecutadas"][2];
			}
			$f->model("pr/plan")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$set))->save("update");
		}
		$f->response->print("true");
	}
	function execute_save_activ_v1(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data["programa"] = $f->session->userDB["cargo"]["programa"];
			$data["programa"]["_id"] = $data["programa"]["_id"]->{'$id'};
			$data["programa"]["actividad"]["_id"] = $data["programa"]["actividad"]["_id"]->{'$id'};
			$data["programa"]["componente"]["_id"] = $data["programa"]["componente"]["_id"]->{'$id'};
			$data['trabajador'] = $f->session->userDB;
			$f->model("pr/plan")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Plan Operativo: Programaci&oacute;n',
				'descr'=>'Se Cre&oacute; La Actividad <b>'.$data['actividad'].'</b> para <b>'.$data['programa']['nomb'].'</b> del periodo <b>'.$data['periodo'].'</b>.'
			))->save('insert');
		}else{
			$trimestre = $data['trimestre'];
			if($trimestre=="1"){
				$set["metas.ejecutadas.0"]=$data["metas"]["ejecutadas"][0];
				$set["metas.ejecutadas.1"]=$data["metas"]["ejecutadas"][1];
				$set["metas.ejecutadas.2"]=$data["metas"]["ejecutadas"][2];
			}elseif($trimestre=="2"){
				$set["metas.ejecutadas.3"]=$data["metas"]["ejecutadas"][0];
				$set["metas.ejecutadas.4"]=$data["metas"]["ejecutadas"][1];
				$set["metas.ejecutadas.5"]=$data["metas"]["ejecutadas"][2];
			}elseif($trimestre=="3"){
				$set["metas.ejecutadas.6"]=$data["metas"]["ejecutadas"][0];
				$set["metas.ejecutadas.7"]=$data["metas"]["ejecutadas"][1];
				$set["metas.ejecutadas.8"]=$data["metas"]["ejecutadas"][2];
			}elseif($trimestre=="4"){
				$set["metas.ejecutadas.9"]=$data["metas"]["ejecutadas"][0];
				$set["metas.ejecutadas.10"]=$data["metas"]["ejecutadas"][1];
				$set["metas.ejecutadas.11"]=$data["metas"]["ejecutadas"][2];
			}
			$f->model("pr/plan")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$set))->save("update");
		}
		$f->response->print("true");
	}
	function execute_save_update_activ(){
		global $f;
		$data = $f->request->data;
		if(isset($f->request->data['_id'])){
			$f->model("pr/plan")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$plan = $f->model("pr/plan")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Plan Operativo: Programaci&oacute;n',
				'descr'=>'Se Modific&oacute; La Actividad <b>'.$plan['actividad'].'</b> para <b>'.$plan["organizacion"]["nomb"].'</b> del periodo <b>'.$plan["periodo"].'</b>.'
			))->save('insert');
		}
		$f->response->print("true");
	}
	function execute_save_update_activ_v1(){
		global $f;
		$data = $f->request->data;
		if(isset($f->request->data['_id'])){
			$f->model("pr/plan")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update");
			$plan = $f->model("pr/plan")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Plan Operativo: Programaci&oacute;n',
				'descr'=>'Se Modific&oacute; La Actividad <b>'.$plan['actividad'].'</b> para <b>'.$plan["programa"]["nomb"].'</b> del periodo <b>'.$plan["periodo"].'</b>.'
			))->save('insert');
		}
		$f->response->print("true");
	}
	function execute_save_aprobar(){
		global $f;
		$data = $f->request->data;
		$f->model("pr/plan")->params(array("_id"=>new MongoId($f->request->id)))->save("aprobar");
		$f->response->print("true");
	}
	function execute_save_create_eval(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			for($i=1;$i<=4;$i++){
				$data2 = array();
				$data2['fecreg'] = new MongoId();
				$data2['periodo'] = $f->request->periodo;
				$data2['trimestre'] = "".$i;
				$data2['estado'] = "P";
				$data2['organizacion']['_id'] = $f->request->organizacion_id;
				$data2['organizacion']['nomb'] = $f->request->organizacion_nomb;
				$data2['logros'] = "";
				$data2['dificultades'] = "";
				$data2['recomendaciones'] = "";
				$data2['conclusiones'] = "";
				$f->model("pr/plan")->params(array('data'=>$data2))->save("eval_insert");
			}
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Plan Operativo: Programaci&oacute;n',
				'descr'=>'Se Aprob&oacute; POI de <b>'.$f->request->organizacion_nomb.'</b> del periodo <b>'.$f->request->periodo.'</b>.'
			))->save('insert');
		}else{
			$f->model("pr/plan")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update_eval");
			$plan = $f->model("pr/plan")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one_eval")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Plan Operativo: Ejecuci&oacute;n',
				'descr'=>'Se Ingres&oacute;/Modific&oacute; La Evaluaci&oacute;n del POE para <b>'.$plan["organizacion"]["nomb"].'</b> del periodo <b>'.$plan["periodo"].'</b>.'
			))->save('insert');
		}
		$f->response->print("true");
	}
function execute_save_create_eval_v1(){
		global $f;
		$data = $f->request->data;
		if(!isset($f->request->data['_id'])){
			for($i=1;$i<=4;$i++){
				$data2 = array();
				$data2['fecreg'] = new MongoId();
				$data2['periodo'] = $f->request->periodo;
				$data2['trimestre'] = "".$i;
				$data2['estado'] = "P";
				$data2['programa']['_id'] = $f->request->programa_id;
				$data2['programa']['nomb'] = $f->request->programa_nomb;
				$data2['logros'] = "";
				$data2['dificultades'] = "";
				$data2['recomendaciones'] = "";
				$data2['conclusiones'] = "";
				$f->model("pr/plan")->params(array('data'=>$data2))->save("eval_insert");
			}
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Plan Operativo: Programaci&oacute;n',
				'descr'=>'Se Aprob&oacute; POI de <b>'.$f->request->programa_nomb.'</b> del periodo <b>'.$f->request->periodo.'</b>.'
			))->save('insert');
		}else{
			$f->model("pr/plan")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>$data))->save("update_eval");
			$plan = $f->model("pr/plan")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one_eval")->items;
			$f->model('ac/log')->params(array(
				'modulo'=>'PR',
				'bandeja'=>'Plan Operativo: Ejecuci&oacute;n',
				'descr'=>'Se Ingres&oacute;/Modific&oacute; La Evaluaci&oacute;n del POE para <b>'.$plan["programa"]["nomb"].'</b> del periodo <b>'.$plan["periodo"].'</b>.'
			))->save('insert');
		}
		$f->response->print("true");
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
	function execute_progeditacti(){
		global $f;
		$f->response->view("pr/plan.prog.edit.acti");
	}
	function execute_ejecediteval(){
		global $f;
		$f->response->view("pr/plan.ejec.edit.eval");
	}
	function execute_ejecdetailseval(){
		global $f;
		$f->response->view("pr/plan.ejec.details.eval");
	}
	function execute_ejeceditmet(){
		global $f;
		$f->response->view("pr/plan.ejec.edit.met");
	}	
	function execute_deleteplan(){
		global $f;
    	$model = $f->model('pr/plan')->params(array("_id"=>$f->request->id))->delete('plan');
    	$plan = $f->model("pr/plan")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'PR',
			'bandeja'=>'Plan Operativo: Programaci&oacute;n',
			'descr'=>'Se Elimin&oacute; La Actividad <b>'.$plan['actividad'].'</b> para <b>'.$plan["organizacion"]["nomb"].'</b> del periodo <b>'.$plan["periodo"].'</b>.'
		))->save('insert');
    	$f->response->print( "true" );
	}
	function execute_deleteplan_v1(){
		global $f;
    	$model = $f->model('pr/plan')->params(array("_id"=>$f->request->id))->delete('plan');
    	$plan = $f->model("pr/plan")->params(array('_id'=>new MongoId($f->request->data['_id'])))->get("one")->items;
		$f->model('ac/log')->params(array(
			'modulo'=>'PR',
			'bandeja'=>'Plan Operativo: Programaci&oacute;n',
			'descr'=>'Se Elimin&oacute; La Actividad <b>'.$plan['actividad'].'</b> para <b>'.$plan["programa"]["nomb"].'</b> del periodo <b>'.$plan["periodo"].'</b>.'
		))->save('insert');
    	$f->response->print( "true" );
	}
	function execute_actualizar(){
		global $f;
		$model = $f->model("pr/plan")->get("all");
		//print_r($model->items);
		foreach($model->items as $item){
			$orga = $f->model("mg/orga")->params(array("_id"=>new MongoId($item["organizacion"]["_id"])))->get("one")->items;
			//$set = array();
			$set["organizacion"] = array(
				"_id"=>$orga["_id"]->{'$id'},
				"nomb"=>$orga["nomb"],
				"actividad"=>array(
					"_id"=>$orga["actividad"]["_id"]->{'$id'},
					"cod"=>$orga["actividad"]["cod"],
					"nomb"=>$orga["actividad"]["nomb"]
				),
				"componente"=>array(
					"_id"=>$orga["componente"]["_id"]->{'$id'},
					"cod"=>$orga["componente"]["cod"],
					"nomb"=>$orga["componente"]["nomb"]
				)
			);
			$f->model("pr/plan")->params(array('_id'=>$item["_id"],'data'=>$set))->save("update");
		}
	}
	function execute_actualizar_v1(){
		global $f;
		$model = $f->model("pr/plan")->get("all");
		//print_r($model->items);
		foreach($model->items as $item){
			$prog = $f->model("mg/prog")->params(array("_id"=>new MongoId($item["programa"]["_id"])))->get("one")->items;
			//$set = array();
			$set["programa"] = array(
				"_id"=>$orga["_id"]->{'$id'},
				"nomb"=>$orga["nomb"],
				"actividad"=>array(
					"_id"=>$orga["actividad"]["_id"]->{'$id'},
					"cod"=>$orga["actividad"]["cod"],
					"nomb"=>$orga["actividad"]["nomb"]
				),
				"componente"=>array(
					"_id"=>$orga["componente"]["_id"]->{'$id'},
					"cod"=>$orga["componente"]["cod"],
					"nomb"=>$orga["componente"]["nomb"]
				)
			);
			$f->model("pr/plan")->params(array('_id'=>$item["_id"],'data'=>$set))->save("update");
		}
	}
	function execute_planevac_print(){
		global $f;
		$model = $f->model("pr/plan")->params(array("periodo"=>$f->request->periodo,"organizacion"=>$f->request->organizacion,"trimestre"=>$f->request->trimestre))->get("eval");
		$model->trim_data = $f->request->trimestre;
		$model->filtros = $f->request->data;
		//print_r($model);die;
		$f->response->view("pr/planevac.print",$model);
	}
	function execute_planevac_print_v1(){
		global $f;
		$model = $f->model("pr/plan")->params(array("periodo"=>$f->request->periodo,"programa"=>$f->request->programa,"trimestre"=>$f->request->trimestre))->get("eval_v1");
		$model->trim_data = $f->request->trimestre;
		$model->filtros = $f->request->data;
		//print_r($model);die;
		$f->response->view("pr/planevac.print",$model);
	}
}
?>