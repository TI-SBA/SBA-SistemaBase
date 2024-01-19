<?php
class Controller_ha_repo extends Controller {
	function execute_index(){
		global $f;
		$f->response->view('ha/repo');
	}
	function execute_hospitalizaciones(){
		global $f;
		$data = $f->model('ha/hosp')->params(array(
			'ini'=>$f->request->data['ini'],
			'fin'=>$f->request->data['fin'],
			'tipo_hosp'=>$f->request->data['tipo_hosp'],
			'altas'=>false
		))->get('all')->items;
		if($f->request->data['type']=='xls'){
			$f->response->view("ha/repo.hospitalizaciones.xls",array(
				'data'=>$data,'params'=>$f->request->data
			));
		}elseif($f->request->data['type']=='pdf'){
			$f->response->view("ha/repo.hospitalizaciones.pdf",array(
				'data'=>$data,'params'=>$f->request->data
			));
		}
	}
	function execute_get_conceptos(){
		global $f;
		$data = $f->request->data;
		$params = array();
		if(isset($data['fecini']) && isset($data['fecfin'])) {
			$fecini = strtotime($data['fecini'].' 00:00:00');
			$fecfin = strtotime($data['fecfin'].' 23:59:59');
			$params['$and'] = array(
				array('fecreg'=>array('$gte'=>new MongoDate($fecini))),
				array('fecreg'=>array('$lte'=>new MongoDate($fecfin)))
			);
		}
		$items = $f->model("cj/comp")->params($params)->get("conceptos")->items;
		$f->response->view("cj/caja.report.php",array('conceptos'=>$items));
	
	}
	function execute_altas(){
		global $f;
		$data = $f->model('ha/hosp')->params(array(
			'ini'=>$f->request->data['ini'],
			'fin'=>$f->request->data['fin'],
			'altas'=>true
		))->get('all')->items;
		if($f->request->data['type']=='xls'){
			$f->response->view("ha/repo.altas.xls",array(
				'data'=>$data,'params'=>$f->request->data
			));
		}elseif($f->request->data['type']=='pdf'){
			$f->response->view("ha/repo.altas.pdf",array(
				'data'=>$data,'params'=>$f->request->data
			));
		}
	}
	function execute_registro_ventas(){
		global $f;
		$fec = $f->request->data['ano'].'-'.$f->request->data['mes'].'-01';
		$comp = $f->model("cj/comp")->params(array("filter"=>array(
			'modulo'=>'AD',
			'fecreg'=>array(
				'$gte'=>new MongoDate(strtotime($fec)),
				'$lte'=>new MongoDate(strtotime($fec.' +1 month -1 minute'))
			)/*,
			'estado'=>array('$ne'=>'X')*/
		),'fields'=>array(
			'fecreg'=>true,
			'tipo'=>true,
			'serie'=>true,
			'num'=>true,
			'cliente'=>true,
			'total'=>true,
			'estado'=>true,
			'cliente_nuevo'=>true
		),'sort'=>array(
			'fecreg'=>1,
			'serie'=>1,
			'num'=>1
		)))->get("all")->items;
		//echo date('Y-m-d H:i:s',strtotime($fec.' +1 month -1 hour'));die();
		
		if($f->request->data['type']=='xls'){
			$f->response->view("ha/repo.registro_ventas.xls",array(
				'data'=>$comp,'params'=>$f->request->data
			));
		}elseif($f->request->data['type']=='pdf'){
			$f->response->view("ha/repo.registro_ventas.pdf",array(
				'data'=>$comp,'params'=>$f->request->data
			));
		}
	}
	function execute_import_hopi(){
		global $f;
		set_time_limit(0);
		echo 'RECONOCIENDO CSV<br />';
		$fp = fopen ( "mov_hsp.csv", "r" );
		$cod = 0;
		$conf = $f->model('cj/conf')->params(array('cod'=>'HO'))->get('cod')->items;
		$modalidades = array(
			'mes'=>'M',
			'dia'=>'D',
			'día'=>'D'
		);
		echo 'INICIANDO PROCESO DE IMPORTACION! <br />';
		while ($data = fgetcsv ($fp, 1000, ";")){
			if($cod!=0){
				$nomb = utf8_encode(trim($data[4]));
				$apmat = utf8_encode(trim($data[3]));
				$appat = utf8_encode(trim($data[2]));
				$hcl_pac = intval($data[5]);
				$fec_hsp = trim($data[6]);

				$fec_hsp_parsed = explode('/', $fec_hsp);
				if(count($fec_hsp_parsed)>1){
					$fec_hsp_parsed = $fec_hsp_parsed[2].'-'.$fec_hsp_parsed[1].'-'.$fec_hsp_parsed[0];
				}else{
					$fec_hsp_parsed = '';
				}

				$cta_comp = trim($data[7]);
				$nro_comp = trim($data[8]);
				$des_hsp = trim($data[10]);
				$fin_hsp = trim($data[11]);
				$ffi_hsp = trim($data[12]);

				$fin_hsp_parsed = explode('/', $fin_hsp);
				if(count($fin_hsp_parsed)>1){
					$fin_hsp_parsed = $fin_hsp_parsed[2].'-'.$fin_hsp_parsed[1].'-'.$fin_hsp_parsed[0];
				}else{
					$fin_hsp_parsed = '';
				}

				$ffi_hsp_parsed = explode('/', $ffi_hsp);
				if(count($ffi_hsp_parsed)>1){
					$ffi_hsp_parsed = $ffi_hsp_parsed[2].'-'.$ffi_hsp_parsed[1].'-'.$ffi_hsp_parsed[0];
				}else{
					$ffi_hsp_parsed = '';
				}

				$fec_alt = trim($data[13]);
				$imp_tar = floatval(trim($data[14]));
				$per_tar = utf8_encode($data[15]);
				$can_tar = trim($data[16]);
				$cat_tar = trim($data[23]);

				$paciente = $f->datastore->mg_entidades->findOne(array( 'roles.paciente.hist_cli'=>$hcl_pac));
				if($paciente==null){
					$paciente = array(
						'_id'=>new MongoId(),
						'fecreg'=>new MongoDate(),
						'nomb'=>$nomb,
						'appat'=>$appat,
						'apmat'=>$apmat,
						'fullname'=>$nomb.' '.$appat.' '.$apmat,
						'docident'=>array(
							array('tipo'=>'DNI','num'=>'')
						),
						'roles'=>array(
							'paciente'=>array(
								'centro'=>'MH',
								'hist_cli'=>$hcl_pac,
								'categoria'=>$cat_tar
							)
						),
						'tipo_enti'=>'P',
						'import_date'=>date('Y-m-d'),
						'import_script'=>'ha/repo/import_hopi'
					);
					$f->datastore->mg_entidades->insert($paciente);
					echo 'SE CREO CORRECTAMETRE UN NUEVO PACIENTE <BR />';
				}
				$appat = '';
				$apmat = '';
				if(isset($paciente['appat'])) $appat = $paciente['appat'];
				if(isset($paciente['apmat'])) $apmat = $paciente['apmat'];
				$modalidad = ((isset($modalidades[$per_tar])?$modalidades[$per_tar]:""));
				$hospitalizacion = array(
					'_id'=>new MongoId(),
					'cant'=>$can_tar,
					'categoria'=>$cat_tar,
					'centro'=>'MH',
					'estado'=>'P',
					'hist_cli'=> $hcl_pac,
					'importe'=> $imp_tar,
					'modalidad'=>$modalidad,
					'num'=>$nro_comp,
					'paciente'=>array(
						'_id'=>$paciente['_id'],
						'nomb'=>$paciente['nomb'],
						'appat'=>$appat,
						'apmat'=>$apmat,
						'tipo_enti'=>$paciente['tipo_enti'],
						'docident'=>$paciente['docident']
					),
					'import_date'=>date('Y-m-d'),
					'import_script'=>'ha/repo/import_hopi'
				);
				if($fec_hsp_parsed!=''){
					$hospitalizacion['fecreg'] = strtotime($fec_hsp_parsed);
					if($hospitalizacion['fecreg']>0){
						$hospitalizacion['fecreg'] = new MongoDate($hospitalizacion['fecreg']);
					}
				}

				if($fin_hsp_parsed!=''){
					$hospitalizacion['fecini'] = strtotime($fin_hsp_parsed);
					if($hospitalizacion['fecini']>0){
						$hospitalizacion['fecini'] = new MongoDate($hospitalizacion['fecini']);
					}
				}

				if($ffi_hsp_parsed!=''){
					$hospitalizacion['fecfin'] = strtotime($ffi_hsp_parsed);
					if($hospitalizacion['fecfin']>0){
						$hospitalizacion['fecfin'] = new MongoDate($hospitalizacion['fecfin']);
					}
				}

				/*
				* VALIDACION SISTEMA SKM-HUBERT-NUEVO
				*/
				if(isset($hospitalizacion['fecini'])){
					$validate_hosp = $f->datastore->ho_hospitalizaciones->findOne(array(
						'hist_cli'=>$hcl_pac,
						'fecini'=>$hospitalizacion['fecini']
					));
					if($validate_hosp==null){
						/* Generar comprobante caja */
						if(floatval($nro_comp)>0&&$cta_comp=='RC'){
							$comprobante = array(
								'_id'=>new MongoId(),
								'modulo'=>'MH',
								'fecreal'=>$hospitalizacion['fecreg'],
								'fecreg'=> new MongoDate(),
								'estado'=>'R',
								'periodo'=>date('YYMM00',strtotime($ffi_hsp_parsed)),
								'autor'=>$f->session->userDBMin,
								'cliente'=>array(
									'_id'=>$paciente['_id'],
									'nomb'=>$paciente['nomb'],
									'appat'=>$appat,
									'apmat'=>$apmat,
									'tipo_enti'=>$paciente['tipo_enti'],
									'docident'=>$paciente['docident']
								),
								'caja'=>array(),
								'tipo'=>'R',
								'serie'=>'003',
								'num'=>floatval($nro_comp),
								'moneda'=>'S',
								'observ'=>'',
								'total'=>$imp_tar,
								'efectivos'=>array(
									array('moneda'=>'S','monto'=>$imp_tar),
									array('moneda'=>'D','monto'=>0)
								),
								'hospitalizacion'=>array(
									'_id'=>$hospitalizacion['_id'],
									'hist_cli'=>$hospitalizacion['hist_cli'],
									'cant'=>$hospitalizacion['cant'],
									'modalidad'=>$hospitalizacion['modalidad'],
									'tipo_hosp'=>$hospitalizacion['tipo_hosp'],
									'categoria'=>$hospitalizacion['categoria'],
									'fecini'=>$hospitalizacion['fecini'],
									'fecfin'=>$hospitalizacion['fecfin']
								),
								'cuenta'=>$conf['HOSP'],
								'import_date'=>date('Y-m-d'),
								'import_script'=>'ha/repo/import_hopi'
							);
							$f->datastore->cj_comprobantes->insert($comprobante);
							$hospitalizacion['recibo'] = array(
								'_id'=>$comprobante['_id'],
								'tipo'=>$comprobante['tipo'],
								'serie'=>$comprobante['serie'],
								'num'=>$comprobante['num']
							);
							$hospitalizacion['recibos'] = array();
							array_push($hospitalizacion['recibos'], $hospitalizacion['recibo']);
							$hospitalizacion['tipo_hosp']= 'P';
						}
						$f->datastore->ho_hospitalizaciones->insert($hospitalizacion);
						echo 'SE CREO HOSPITALIZACION<br />';
					}else{
						echo 'LA HOSPITALIZACION YA EXISTE EN LA BD<br />';
					}
				}
			}
			$cod++;
		}
		fclose($fp);
		echo 'LA IMPORTACIÓN SE HA REALIZADO CON EXITO! <br />';
	}
}
?>