<?php
class Controller_pe_conc extends Controller {
	function execute_lista(){
		global $f;
		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['estado']))
			if($f->request->data['estado']!='')
				$params['estado'] = $f->request->data['estado'];
		if(isset($f->request->data["tipo"])){
			if($f->request->tipo!=""){
				$params["tipo"] = new MongoId($f->request->tipo);
			}
		}
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("pe/conc")->params($params)->get("lista") );
	}
	function execute_all(){
		global $f;
		$model = $f->model('pe/conc')->get('all');
		$f->response->json($model->items);
	}
	function execute_all_order(){
		global $f;
		$model = $f->model('pe/conc')->params(array("contrato"=>new MongoId($f->request->data['contrato']),'estado'=>'H','fields'=>array(
			'nomb'=>true,'cod'=>true,'tipo'=>true,'descr'=>true,'orden'=>true
		)))->get('all');
		$f->response->json($model);
	}
	function execute_all_var(){
		global $f;
		$model = $f->model('pe/conc')->params(array("tipo"=>$f->request->data['tipo'],'fields'=>array('cod'=>true,'nomb'=>true)))->get('all');
		$vari = $f->model('mg/vari')->params(array('fields'=>array('cod'=>true,'nomb'=>true)))->get('all');
		$defs = array(
			array('nomb'=>'Suma de todos los bonos del trabajador','cod'=>'TOTAL_BONOS'),
			array('nomb'=>'Edad del trabajador','cod'=>'EDAD'),
			array('nomb'=>'Mes actual (1 = Enero, 2 = Febrero)','cod'=>'MES_ACTUAL'),
			array('nomb'=>'Total de d&iacute;as del mes actual','cod'=>'TOTAL_DIAS_MES'),
			array('nomb'=>'Minutos efectivos de labor por trabajador','cod'=>'MIN_EFE'),
			array('nomb'=>'Minutos programados a laborar por trabajador','cod'=>'MIN_PRO'),
			array('nomb'=>'Minutos de Permiso por trabajador','cod'=>'MIN_PERMISO'),
			array('nomb'=>'Salario seg&uacute;n Nivel remunerativo','cod'=>'SALARIO_NIVEL'),
			array('nomb'=>'Nivel remunerativo (Abreviatura: Ejem. F-1, F-2, etc)','cod'=>'NIVEL_REMUNERATIVO'),
			array('nomb'=>'Pensi&oacute;n de trabajador','cod'=>'PENS'),
			array('nomb'=>'EPS','cod'=>'EPS'),
			array('nomb'=>'Total Horas extras (minutos)','cod'=>'MIN_EXT'),
			array('nomb'=>'Total Tardanzas (minutos)','cod'=>'MIN_TAR'),
			/*array('nomb'=>'Porcentaje de pensi&oacute;n','cod'=>'PORC_PEN'),
			array('nomb'=>'Porcentaje de seguro','cod'=>'PORC_SEG'),
			array('nomb'=>'Porcentaje de comisi&oacute;n','cod'=>'POR_COM'),*/
			array('nomb'=>'D&iacute;as trabajados seg&uacute;n calendario de 30 d&iacute;as','cod'=>'DIAS_TRAB'),
			array('nomb'=>'D&iacute;as trabajados','cod'=>'DIAS_TRAB_TOTAL'),
			array('nomb'=>'D&iacute;as de inasistencia por cese','cod'=>'DIAS_CESE'),
			array('nomb'=>'D&iacute;as de inasistencia','cod'=>'DIAS_INA'),
			array('nomb'=>'D&iacute;as de licencia','cod'=>'DIAS_LIC'),
			array('nomb'=>'D&iacute;as de suspensi&oacute;n','cod'=>'DIAS_SUS'),
			array('nomb'=>'D&iacute;as de Permiso','cod'=>'DIAS_PERMISO'),
			array('nomb'=>'Bonificaci&oacute;n Familiar (Cantidad)','cod'=>'HIJOS'),
			array('nomb'=>'Resoluci&oacute;n de Subsidio Familiar (Verdadero o Falso)','cod'=>'SUBSIDIO_FAMILIAR'),	
			//array('nomb'=>'Dias de permiso particular','cod'=>'PER_DIA'),
			//array('nomb'=>'Minutos de permiso particular','cod'=>'PER_MIN'),
			array('nomb'=>'Salario del Trabajador CAS','cod'=>'SALARIO_CAS'),
			array('nomb'=>'Dias subsidiados','cod'=>'SUB_DIA'),
			array('nomb'=>'Remuneraci&oacute;n B&aacute;sica','cod'=>'REM_BAS'),
			array('nomb'=>'Remuneraci&oacute;n Reunificada','cod'=>'REM_REU'),
			array('nomb'=>'Fecha de ingreso a SBPA','cod'=>'FEC_ING'),
			array('nomb'=>'Escala de Productividad','cod'=>'INCENTIVO'),
			array('nomb'=>'Vacaciones (TRUE / FALSE)','cod'=>'VACACIONES'),
			array('nomb'=>'Tipo de Trabajador (Contratado=>"C", Nombrado=>"N", Salud - Contratado=>"SC", Salud - Nombrado=>"SN")','cod'=>'TIPO_TRAB'),
			array('nomb'=>'Tipo de Contrato (Contratados CAS=>"1057", Nombrados=>"276")','cod'=>'TIPO_CONT'),
			array('nomb'=>'C&aacute;lculo de d&iacute;as aguinaldo por Fiestas Patrias','cod'=>'DIAS_AGUI_FP'),
			array('nomb'=>'C&aacute;lculo de d&iacute;as aguinaldo por Navidad','cod'=>'DIAS_AGUI_NA'),
			array('nomb'=>'Porcentaje de pensi&oacute;n N&deg;1','cod'=>'PORC_PEN_1'),
			array('nomb'=>'Porcentaje de pensi&oacute;n N&deg;2','cod'=>'PORC_PEN_2'),
			array('nomb'=>'Porcentaje de pensi&oacute;n N&deg;3','cod'=>'PORC_PEN_3'),
			array('nomb'=>'Porcentaje de pensi&oacute;n N&deg;4','cod'=>'PORC_PEN_4'),
			array('nomb'=>'Porcentaje de pensi&oacute;n N&deg;5','cod'=>'PORC_PEN_5'),
			array('nomb'=>'Porcentaje de pensi&oacute;n N&deg;6','cod'=>'PORC_PEN_6'),
			array('nomb'=>'Porcentaje de pensi&oacute;n N&deg;7','cod'=>'PORC_PEN_7'),
			array('nomb'=>'Porcentaje de pensi&oacute;n N&deg;8','cod'=>'PORC_PEN_8'),
			array('nomb'=>'Porcentaje de pensi&oacute;n N&deg;9','cod'=>'PORC_PEN_9'),
			array('nomb'=>'Porcentaje de pensi&oacute;n N&deg;10','cod'=>'PORC_PEN_10'),
			array('nomb'=>'Numero de guardias ordinarias','cod'=>'NUM_GUARDIAS_ORD'),
			array('nomb'=>'Numero de guardias extraordinarias','cod'=>'NUM_GUARDIAS_EXT')
		);
		$f->response->json(array('conc'=>$model->items,'vari'=>$vari->items,'defs'=>$defs));
	}
	function execute_get(){
		global $f;
		$conc = $f->model("pe/conc")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
		if(isset($f->request->data['all'])){
			$conc['historico'] = $f->model('pe/conh')->params(array('filter'=>array('concepto'=>$conc['_id'])))->get('all')->items;
		}
		$f->response->json( $conc );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$trabajador = $f->session->userDBMin;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $trabajador;
		if(isset($data['contrato']['_id'])) $data['contrato']['_id'] = new MongoId($data['contrato']['_id']);
		if(isset($data['clasif']['_id'])) $data['clasif']['_id'] = new MongoId($data['clasif']['_id']);
		if(isset($data['cuenta']['_id'])) $data['cuenta']['_id'] = new MongoId($data['cuenta']['_id']);
		if(isset($data['clasif']['cuenta']['_id'])) $data['clasif']['cuenta']['_id'] = new MongoId($data['clasif']['cuenta']['_id']);
		if(isset($data['imprimir'])){
			$data['imprimir'] = floatval($data['imprimir']);
		}
		if(isset($data['planilla'])){
			$data['planilla'] = floatval($data['planilla']);
		}
		if(isset($data['boleta'])){
			if($data['boleta']==1) $data['boleta'] = true;
			else $data['boleta'] = false;
		}
		if(isset($data['vacaciones'])){
			if($data['vacaciones']==1) $data['vacaciones'] = true;
			else $data['vacaciones'] = false;
		}
		if(isset($data['cts'])){
			if($data['cts']==1) $data['cts'] = true;
			else $data['cts'] = false;
		}
		if(isset($data['gravidez'])){
			if($data['gravidez']==1) $data['gravidez'] = true;
			else $data['gravidez'] = false;
		}
		if(isset($data['enfermedad'])){
			if($data['enfermedad']==1) $data['enfermedad'] = true;
			else $data['enfermedad'] = false;
		}
		if(isset($data['sepelio'])){
			if($data['sepelio']==1) $data['sepelio'] = true;
			else $data['sepelio'] = false;
		}
		if(isset($data['luto'])){
			if($data['luto']==1) $data['luto'] = true;
			else $data['luto'] = false;
		}
		if(isset($data['quinquenio'])){
			if($data['quinquenio']==1) $data['quinquenio'] = true;
			else $data['quinquenio'] = false;
		}
		if(isset($data['renta'])){
			if($data['renta']==1) $data['renta'] = true;
			else $data['renta'] = false;
		}
		if(isset($data['beneficiario'])){
			if(is_array($data['beneficiario']))
				$data['beneficiario']['_id'] = new MongoId($data['beneficiario']['_id']);
		}
		if(isset($data['filter'])){
			foreach ($data['filter'] as $i=>$filt){
				if(is_array($filt['valor'])){
					$data['filter'][$i]['valor']['_id'] = new MongoId($filt['valor']['_id']);
				}
			}
		}
		if(!isset($f->request->data['_id'])){
			$data['fecreg'] = new MongoDate();
			$data['autor'] = $f->session->userDB;
			$data['estado'] = 'H';
			$data['historico'] = array(0=>array(
				'trabajador'=>$trabajador,
				'descr'=>$data['descr'],
				'formula'=>$data['formula'],
				'fecreg'=>$data['fecmod']
			));
			$f->model("pe/conc")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'PE',
				'bandeja'=>'Conceptos',
				'descr'=>'Se cre&oacute; el concepto <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			unset($data['_id']);
			$vari = $f->model("pe/conc")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			$tmp = $data;
			if(!isset($data['formula'])){
				$tmp['descr'] = $vari['descr'];
				$tmp['nomb'] = $vari['nomb'];
				$tmp['formula'] = $vari['formula'];
				$tmp['fecreg'] = $vari['fecmod'];
			}
			$f->model("pe/conh")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array(
				'trabajador'=>$trabajador,
				'concepto'=>$vari['_id'],
				'descr'=>$tmp['descr'],
				'nomb'=>$tmp['nomb'],
				'formula'=>$tmp['formula'],
				'fecreg'=>$tmp['fecmod']
			)))->save("insert");
			$f->model("pe/conc")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array(
				'$set'=>$data
			)))->save("update");
			if(isset($data['estado'])){
				if($data['estado']=='H') $word = 'habilit&oacute;';
				else $word = 'deshabilit&oacute;';
				$f->model('ac/log')->params(array(
					'modulo'=>'PE',
					'bandeja'=>'Conceptos',
					'descr'=>'Se '.$word.' el concepto <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'PE',
					'bandeja'=>'Conceptos',
					'descr'=>'Se actualiz&oacute; el concepto <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
			}
		}
		$f->response->print("true");
	}
	function execute_save_order(){
		global $f;
		$data = $f->request->data;
		if(isset($data["items"])){
			foreach($data["items"] as $item){
				$f->model("pe/conc")->params(array('_id'=>new MongoId($item['_id']),'data'=>array('$set'=>array("orden"=>floatval($item["orden"])))))->save("update");
			}
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("pe/conc.edit");
	}
	function execute_form(){
		global $f;
		$f->response->view("pe/conc.form");
	}
	function execute_details(){
		global $f;
		$f->response->view("pe/conc.details");
	}
}
?>