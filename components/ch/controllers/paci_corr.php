<?php
class Controller_ch_paci_corr extends Controller {
	function corregir(){
		global $f;
		$data = $f->request->data;
		$response=array(
			'status'=>'error',
			'message'=>'Error: A ocurrido un error desconocido',
			'data'=>array()
		);
		try{
			if(!isset($data['his_cli'])) throw new Exception("Error: no se envio el his_cli");
			{
				$his_cli = $f->datastore->ch_pacientes->find(array("his_cli"=>intval($data['his_cli'])));
				$last_pac = $f->datastore->ch_pacientes->find(array("his_cli"=>intval($data['his_cli'])))->sort(array('$natural'=>1))->limit(1);
				$pac_def;
				#CONTADORES
				$c_social=0;
				$c_pardia=0;
				$c_camas=0;
				$c_camos=0;
				$c_psic=0;
				$c_psiq=0;
				$c_hosp=0;
				if($last_pac->count()==0) throw new Exception("Error: no se encontro la historia clinica del definitivo");
				foreach ($last_pac as $utlimo_paciente){
					/*Ultimo paciente correctamente ingresado naturalmente, este es el que quedara permanentemente*/
					$pac_def=$utlimo_paciente;
				}
				if($his_cli->count()==0) throw new Exception("Error: no se encontro la historia clinica");
				foreach ($his_cli as $paciente){
					if($paciente['paciente']['_id']->{'$id'}!=$pac_def['_id']->{'$id'}){
						$social = $f->datastore->ch_FichaSocial->find(array("paciente._id"=>($paciente['_id'])));
						$c_social=$c_social+($social->count());

						$ParDia = $f->datastore->ch_ParteDiario->find(array("consulta.paciente._id"=>($paciente['_id'])));
						$c_pardia=$c_pardia+($ParDia->count());

						$camas = $f->datastore->ch_camas->find(array("paciente._id"=>($paciente['_id'])));
						$c_camas=($c_camas+$camas->count());

						$camos = $f->datastore->ch_camos->find(array("paciente._id"=>($paciente['_id'])));
						$c_camos=$c_camos+($camos->count());

						$psic = $f->datastore->ch_fichaspsicologicas->find(array("paciente._id"=>($paciente['_id'])));
						$c_psic=$c_psic+($psic->count());

						$psiq = $f->datastore->ch_fichaspsiquiatricas->find(array("paciente._id"=>($paciente['_id'])));
						$c_psiq=$c_psiq+($psiq->count());

						$hosp = $f->datastore->ch_pacientes_hospitalizados->find(array("paciente._id"=>($paciente['_id'])));
						$c_hosp=$c_psiq+($hosp->count());
					}
				}
				print_r("Cambios en Ficha social = ".$c_social." </br>");
				print_r("Cambios en Parte Diario = ".$c_pardia." </br>");
				print_r("Cambios en Camas = ".$c_camas." </br>");
				print_r("Cambios en Camas Movimientos = ".$c_camos." </br>");
				print_r("Cambios en Fichas Psiquiatricas = ".$c_psiq." </br>");
				print_r("Cambios en Fichas Psicologicas = ".$c_psic." </br>");
				print_r("Cambios en Fichas Hospitalizacion = ".$c_hosp." </br>");
				
			}
			//$response['status'] = 'success';
			//$response['message'] = 'correcto';

		}
		catch (Exception $e)
		{
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}

	function escribir(){
		global $f;
		$data = $f->request->data;
		$response=array(
			'status'=>'error',
			'message'=>'Error: A ocurrido un error desconocido',
			'data'=>array()
		);
		try{
			if(!isset($data['his_cli'])) throw new Exception("Error: no se envio el his_cli");
			{
				$his_cli = $f->datastore->ch_pacientes->find(array("his_cli"=>intval($data['his_cli'])));
				$last_pac = $f->datastore->ch_pacientes->find(array("his_cli"=>intval($data['his_cli'])))->sort(array('$natural'=>-1))->limit(1);
				$pac_def;
				#CONTADORES
				$c_social=0;
				$c_pardia=0;
				$c_camas=0;
				$c_camos=0;
				$c_psic=0;
				$c_psiq=0;
				$c_hosp=0;
				if($last_pac->count()==0) throw new Exception("Error: no se encontro la historia clinica del definitivo");
				foreach ($last_pac as $utlimo_paciente){
					/*Ultimo paciente correctamente ingresado naturalmente, este es el que quedara permanentemente*/
					$pac_def=$utlimo_paciente;
				}
				print_r($pac_def);
				$pac_def['his_cli']=intval($pac_def['his_cli']);
				if($his_cli->count()==0) throw new Exception("Error: no se encontro la historia clinica");
				foreach ($his_cli as $paciente){
					$social = $f->datastore->ch_FichaSocial->find(array("paciente._id"=>($paciente['_id'])));
//					$c_social=$c_social+($social->count());
//					print_r("Cambios en Ficha social".$social->count()." </br>");
//					foreach ($social as $fic_soc) {
//						/*las fichas sociales seran modificados para que apunten al paciente permanente*/
//						echo '<pre>';
//						print_r($fic_soc);
//						echo '</pre>';
//					}

					$ParDia = $f->datastore->ch_ParteDiario->find(array("consulta.paciente.his_cli"=>($paciente['his_cli'])));
//					$c_pardia=$c_pardia+($ParDia->count());
//					print_r("Cambios en Parte Diario".$ParDia->count()." </br>");
					foreach ($ParDia as $par_dia) {
						print_r($par_dia);
						if(isset($par_dia['consulta'])){
							foreach ($par_dia['consulta'] as $key => $consulta) {
								//print_r($key);
								//print_r($consulta['paciente']);
								//print_r($par_dia['consulta'][$key]);
								//print_r($pac_def['his_cli']);
								if($par_dia['consulta'][$key]['paciente']['his_cli']==$pac_def['his_cli']){
									//print_r($key.'</br>');
									//var_dump($par_dia['consulta'][$key]['his_cli'].'</br>');
									//var_dump($pac_def['his_cli'].'</br>');
									//var_dump($par_dia['consulta'][$key]['paciente']['_id'].'</br>');
									//print_r($par_dia);
									$par_dia['consulta'][$key]['paciente']['_id']=$pac_def['_id'];



									/**/
									//$par_dia['consulta'][$key]['paciente']['_id']=$pac_def['_id'];
									//$par_dia['consulta'][$key]['paciente']['_id']=$pac_def['_id'];

									/*UPDATE*/
									//print_r('-+-+-+</br>');
									//var_dump($par_dia['consulta'][$key]['paciente']['_id']);
									//print_r('------</br>');
									//print_r($par_dia);
									//var_dump($pac_def['_id']);
									//print_r('+++++++</br>');
									//var_dump($par_dia['consulta'][$key]['paciente']['_id'].'</br>');
									//var_dump($pac_def['_id'].'</br>');
									//var_dump($par_dia['consulta'][$key]['paciente']['_id'].'</br>');
									//var_dump($pac_def['_id'].'</br>');
									//$parte=$f->datastore->ch_ParteDiario->update(array("_id"=>$par_dia['_id']),array('$set'=>array('consulta.'.$key.'.paciente._id'=>$pac_def['_id'])));
									//print_r($parte);
									//print_r($par_dia);
								}
							}
							//print_r($par_dia);
							//$parte=$f->datastore->ch_ParteDiario->update(array('_id'=>$par_dia['_id']),$par_dia);
						}
					}
					$camas = $f->datastore->ch_camas->find(array("paciente._id"=>($paciente['_id'])));
//					$c_camas=($c_camas+$camas->count());
//					print_r("Cambios en Camas".$camas->count()." </br>");
//					foreach ($camas as $cam_as) {
//						/*los partes diarios seran modificados para que apunten al paciente permanente*/
//						echo '<pre>';
//						print_r($cam_as);
//						echo '</pre>';
//					}
					$camos = $f->datastore->ch_camos->find(array("paciente._id"=>($paciente['_id'])));
//					$c_camos=$c_camos+($camos->count());
//					print_r("Cambios en cama movimientos".$camos->count()." </br>");
//					foreach ($camos as $cam_os) {
//						/*los partes diarios seran modificados para que apunten al paciente permanente*/
//						echo '<pre>';
//						print_r($cam_os);
//						echo '</pre>';
//					}
					$psic = $f->datastore->ch_fichaspsicologicas->find(array("paciente._id"=>($paciente['_id'])));
//					$c_psic=$c_psic+($psic->count());
//					print_r("Cambios en fichas psicologicas".$psic->count()." </br>");
//					foreach ($psic as $fic_psi) {
//						/*los partes diarios seran modificados para que apunten al paciente permanente*/
//						echo '<pre>';
//						print_r($fic_psi);
//						echo '</pre>';
//					}
					$psiq = $f->datastore->ch_fichaspsiquiatricas->find(array("paciente._id"=>($paciente['_id'])));
//					$c_psiq=$c_psiq+($psiq->count());
//					print_r("Cambios en fichas psiquiatricas".$psiq->count()." </br>");
//					foreach ($psiq as $fic_psiq) {
//						/*los partes diarios seran modificados para que apunten al paciente permanente*/
//						echo '<pre>';
//						print_r($fic_psiq);
//						echo '</pre>';
//					}
					$hosp = $f->datastore->ch_pacientes_hospitalizados->find(array("paciente._id"=>($paciente['_id'])));
//					$c_hosp=$c_hosp+($hosp->count());
///					print_r("Cambios en fichas hospitalizacion".$hosp->count()." </br>");
//					foreach ($hosp as $fic_hosp) {
//						/*los partes diarios seran modificados para que apunten al paciente permanente*/
//						echo '<pre>';
//						print_r($fic_hosp);
//						echo '</pre>';
//					}
//					print_r("-----------------------------------------------------</br>");
				}
				
				//print_r("Cambios en Ficha social".$c_social." </br>");
				print_r("Cambios en Parte Diario".$c_pardia." </br>");
				/*
				print_r("Cambios en Camas".$c_camas." </br>");
				print_r("Cambios en Camas Movimientos".$c_camos." </br>");
				print_r("Cambios en Fichas Psiquiatricas".$c_psiq." </br>");
				print_r("Cambios en Fichas Psicologicas".$c_psic." </br>");
				print_r("Cambios en Fichas Hospitalizacion".$c_hosp." </br>");
				*/
			}
			$response['status'] = 'success';
			$response['message'] = 'correcto';

		}
		catch (Exception $e)
		{
			$response['status'] = 'error';
			$response['message'] = $e->getMessage();
		}
		$f->response->json($response);
	}
}
?>