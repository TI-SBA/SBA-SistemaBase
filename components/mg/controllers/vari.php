<?php
class Controller_mg_vari extends Controller {
	function execute_lista2(){
		global $f;
		set_time_limit(0);

















$conexión = new MongoClient("mongodb://200.10.77.58:27017");
        $bd = $conexión->db_skm_mh;

/******************************************************************************************
* mh_Paciente
******************************************************************************************/
		$page = 1;
		$page_rows = 100;
        $col = $bd->pacientes;
        $items = $col->find(array('check1'=>array('$exists'=>false)))->skip( $page_rows * ($page-1) )->limit( $page_rows );
        foreach($items as $key => $item){
        	if(!isset($item['Telefono'])) $item['Telefono'] = '';
            $data = array(
            	'old_id'=>$item['_id'],
            	'procedencia'=>array(
            		'departamento'=>$item['IdDpto'],
            		'provincia'=>$item['IdProv'],
            		'distrito'=>$item['IdDist'],
            	),
            	'lugar_nacimiento'=>array(
            		'departamento'=>$item['IdDptoNac'],
            		'provincia'=>$item['IdProvNac'],
            		'distrito'=>$item['IdDistNac'],
            	),
                'his_cli'=>$item['HistoriaClinica'],
                'fe_regi'=>$item['Fecha'],
                'sexo'=>$item['Sexo'],
                'domi'=>$item['Domicilio'],
                'fecha_na'=>$item['FechaNacimiento'],
                'es_civil'=>$item['IDEciv'],
                'reli'=>$item['IDRelg'],
                'idio'=>$item['IdIdma'],
                'instr'=>$item['IDGins'],
                'refe'=>$item['ReferidoPor'],
                'tele'=>$item['Telefono'],
                'ocupa'=>$item['IdOcup'],
                't_deso'=>$item['TiempoDesocupacion'],
                'm_resi'=>$item['TiempoResidencia'],
                'd_ini'=>$item['IdDiag'],
                'ti_doc'=>$item['IdDocu'],
                'm_consu'=>$item['MotivoConsulta']
            );
			$paciente = $f->model('mg/entidad')->params(array(
				'filter'=>array('roles.paciente.hist_cli'=>intval($item['HistoriaClinica'])),
				'fields'=>array(),
				'sort'=>array('_id'=>1)
			))->get('custom_data')->items;
			if($paciente!=null){
				$data['paciente'] = $paciente[0];
			}else{
				$enti = array(
					'fecreg'=>new MongoDate(),
					'nomb'=>$item['Nombres'],
					'appat'=>$item['ApellidoPaterno'],
					'apmat'=>$item['ApellidoMaterno'],
					'fullname'=>$item['Nombres'].' '.$item['ApellidoPaterno'].' '.$item['ApellidoMaterno'],
					'tipo_enti'=>'P',
					'docident'=>array(0=>array(
						'tipo'=>'DNI',
						'num'=>$item['NumeroDocumento']
					)),
					'roles'=>array(
						'paciente'=>array(
							'centro'=>'MH',
							'hist_cli'=>$item['HistoriaClinica']
						)
					)
				);
				$data['paciente'] = $f->model('mg/entidad')->params(array('data'=>$enti))->save('insert')->items;
			}
			$enti = array(
				'fecreg'=>new MongoDate(),
				'nomb'=>$item['NombreApoderado'],
				'appat'=>$item['ApellidoPApoderado'],
				'apmat'=>$item['ApellidoMApoderado'],
				'fullname'=>$item['NombreApoderado'].' '.$item['ApellidoPApoderado'].' '.$item['ApellidoMApoderado'],
				'tipo_enti'=>'P',
				'domicilios'=>array(0=>array(
					'direccion'=>$item['DireccionApoderado']
				)),
				'telefonos'=>array(0=>array(
					'num'=>$item['TelefonoApoderado']
				))
			);
			$data['apoderado'] = $f->model('mg/entidad')->params(array('data'=>$enti))->save('insert')->items;
            $f->datastore->mh_pacientes->insert($data);
            $bd->pacientes->update(array('_id'=>$item['_id']),array('$set'=>array('check1'=>true)));
        }




/******************************************************************************************
* mh_FichaSocial
******************************************************************************************/
$col = $bd->pacientes;
        $items = $col->find(array('check2'=>array('$exists'=>false),'Detalles'=>array('$exists'=>true)))->skip( $page_rows * ($page-1) )->limit( $page_rows );
        foreach($items as $key => $item){
            $data = array();
            if(isset($item['Detalles'])){
                $data = array(
            		'old_id'=>$item['_id'],
                    'antece'=>utf8_encode($item['Detalles']['Antecedentes']),
                    'categoria'=>$item['Detalles']['Categoria'],
                    'instr'=>$item['Detalles']['GradoEstudio'],
                    'motivo'=>$item['Detalles']['MotivoConsulta'],
                    'trata'=>$item['Detalles']['TratamientoAnterior'],
                    'salud'=>$item['Detalles']['Salud'],
                    'soporte'=>$item['Detalles']['SoporteSocioFamiliar'],
                    'vivi'=>$item['Detalles']['Vivienda'],
                    'AODE'=>$item['Detalles']['AODE'],
                    'parientes'=>array()
                );
                if($item['Familiar']){
                    foreach ($item['Familiar'] as $j=>$fami) {
                        $data['parientes'][] = array(
                            'app'=>utf8_encode($fami['ApellidoPaterno']),
                            'amp'=>utf8_encode($fami['ApellidoMaterno']),
                            'nomp'=>utf8_encode($fami['Nombres']),
                            'docp'=>$fami['NumeroDocumento'],
                            'paren'=>$fami['Parentesco'],
                            'civp'=>$fami['EstadoCivil'],
                            'edap'=>$fami['Edad'],
                            'instr_p'=>$fami['GradoInstruccion'],
                            'ocup'=>utf8_encode($fami['Ocupacion'])
                        );
                    }
                }
				$paciente = $f->model('mg/entidad')->params(array(
					'filter'=>array('roles.paciente.hist_cli'=>intval($item['HistoriaClinica'])),
					'fields'=>array(),
					'sort'=>array('_id'=>1)
				))->get('custom_data')->items;
				if($paciente!=null){
					$data['paciente'] = $paciente[0];
				}else{
					$enti = array(
						'fecreg'=>new MongoDate(),
						'nomb'=>$item['Nombres'],
						'appat'=>$item['ApellidoPaterno'],
						'apmat'=>$item['ApellidoMaterno'],
						'fullname'=>$item['Nombres'].' '.$item['ApellidoPaterno'].' '.$item['ApellidoMaterno'],
						'tipo_enti'=>'P',
						'docident'=>array(0=>array(
							'tipo'=>'DNI',
							'num'=>$item['NumeroDocumento']
						)),
						'roles'=>array(
							'paciente'=>array(
								'centro'=>'MH',
								'hist_cli'=>$item['HistoriaClinica']
							)
						)
					);
					$data['paciente'] = $f->model('mg/entidad')->params(array('data'=>$enti))->save('insert')->items;
				}
                $f->datastore->mh_FichaSocial->insert($data);
           		$bd->pacientes->update(array('_id'=>$item['_id']),array('$set'=>array('check2'=>true)));
            }
        }






/******************************************************************************************
* mh_fichasMedicas
******************************************************************************************/
/*$col = $bd->tbl_FichasMedicas;
        $items = $col->find(array());
        foreach($items as $key => $item){
            $data = array(
                'diag'=>$item['InformeEnfermeria'][0]['Diagnostico'],
                'tra'=>$item['InformeEnfermeria'][0]['RespondeTratamiento'],
                'inter'=>$item['InformeEnfermeria'][0]['CapacidadInterrelacionarse'],
                'agres'=>$item['InformeEnfermeria'][0]['PresentaAgresividad'],
                'visi'=>$item['InformeEnfermeria'][0]['RecibeVisita'],
                'camb'=>$item['InformeEnfermeria'][0]['PresentaCambios'],
                'rein'=>$item['InformeEnfermeria'][0]['PuedeReinsertarse'],
                'peso'=>$item['InformeEnfermeria'][0]['ExamenMedico'][0]['Peso'],
                'talla'=>$item['InformeEnfermeria'][0]['ExamenMedico'][0]['Talla'],
                'temp'=>$item['InformeEnfermeria'][0]['ExamenMedico'][0]['Temperatura'],
                'obs'=>$item['InformeEnfermeria'][0]['ExamenMedico'][0]['Observacion'],
                'nomb'=>$item['EvolucionesMedicas'][0]['Diagnostico'],
                'diagno'=>$item['EvolucionesMedicas'][0]['Diagnostico'],
                'indi'=>$item['EvolucionesMedicas'][0]['IndicacionesMedicas']
            );
            $tmp = $bd->pacientes->findOne(array('_id'=>new MongoId($item['IdPaciente'])));
            $data['paciente'] = new MongoId($item['IdPaciente']);
            $data['paci'] = utf8_encode($tmp['NombreApoderado'].' '.$tmp['ApellidoPaterno'].' '.$tmp['ApellidoMaterno']);
            $f->datastore->mh_fichasMedicas->insert($data);
        }*/





/******************************************************************************************
* mh_ConsultasMedicas
******************************************************************************************/
		/*$col = $bd->DetalleParteDiario;
        $items = $col->find(array());
        foreach($items as $key => $item){
            $data = array(
                'his'=>$item['NroHra'],
                'cate'=>$item['IdTipPac'],
                'esta'=>$item['IdcPac'],
                'cie10'=>$item['IdDiag'],
                'id_parte'=>$item['IdPart'],
            );
            $tmp = $bd->ParteDiario->findOne(array('IdPart'=>$item['IdPart']));
            $data['doct'] = $tmp['IdDoct'];
            $data['part'] = $tmp['FecPar'];

            $tmp = $bd->pacientes->findOne(array('HistoriaClinica'=>$item['NroHra']));
            $data['paciente'] = $tmp['_id'];
            $data['ap'] = utf8_encode($tmp['ApellidoPaterno'].' '.$tmp['ApellidoMaterno']);
            $data['nom'] = utf8_encode($tmp['NombreApoderado']);
            $f->datastore->mh_ConsultasMedicas->insert($data);
        }*/

/******************************************************************************************
* mh_ParteDiario
******************************************************************************************/
		$page = 1;
		$page_rows = 100;
		$col = $bd->ParteDiario;
		$items = $col->find(array('check'=>array('$exists'=>false)))->skip( $page_rows * ($page-1) )->limit( $page_rows );
		foreach($items as $key => $item){
			$medico = $bd->tbl_personal->findOne(array("IdParte"=>$item["IdDoct"]));
			$entidad = $f->datastore->mg_entidades->findOne(array("docident.num"=>$medico["NumeroDocumento"]));
			if($entidad!=null){
				$data = array(
					"num"=>$item['IdPart'],
					"fech"=>$item['FecPar'],
					"fecmod"=>new MongoDate(),
					"fecreg"=>new MongoDate(),
					"consulta"=>array(),
					"medico"=>array(
						"_id"=>$entidad["_id"],
						"nomb"=>$entidad["nomb"],
						"tipo_enti"=>$entidad["tipo_enti"],
						"appat"=>$entidad["appat"],
						"apmat"=>$entidad["apmat"],
						"docident"=>$entidad["docident"],
						"domicilios"=>$entidad["domicilios"]
					)
				);
				$detalleParte = $bd->DetalleParteDiario->find(array("IdPart"=>$data["num"]));
				if($detalleParte!=null){
					foreach($detalleParte as $det){
						//$tmp = $bd->pacientes->findOne(array('HistoriaClinica'=>$item['NroHra']));
						$paciente = $f->datastore->mh_pacientes->findOne(array('his_cli'=>$det["NroHra"]));
						$_item = array(
							'his_cli'=>$paciente['his_cli'],
							'cate'=>$det['IdTipPac'],
							'esta'=>$det['IdcPac'],
							'cie10'=>$det['IdDiag'],
							'paciente'=>array(
								'_id'=>$paciente['_id'],
								'his_cli'=>$paciente['his_cli'],
								'paciente'=>$paciente['paciente'],
								'fecha_na'=>$paciente['fecha_na'],
								'sexo'=>$paciente['sexo']
							)
						);
						array_push($data["consulta"], $_item);
					}
				}
				$f->datastore->mh_ParteDiario->insert($data);
				$bd->ParteDiario->update(array('_id'=>$item['_id']),array('$set'=>array('check'=>true)));
			}
		}
        /******************************************************************************************
        * Hospitalizaciones tbl_Hospitalizaciones
        ******************************************************************************************/
        $page = 1;
        $page_rows = 200;
        $col = $bd->tbl_Hospitalizacions;
        $items = $col->find(array('check3'=>array('$exists'=>false)))->skip( $page_rows * ($page-1) )->limit( $page_rows );

        $tipo_hosp = array(
            1=>"",
            2=>"C",
            3=>"P"
        );
        $import_date = date('Y-m-d');
        $import_script = 'mg/vari/lista:298';
        foreach($items as $key => $item){
            $hospitalizacion = $f->datastore->ho_hospitalizaciones->findOne(array("hist_cli"=>$item["NroHra"],"fecini"=>$item['FechaIngreso']));
            $paciente = $f->datastore->mh_pacientes->findOne(array('his_cli'=>$item["NroHra"]));
            if($paciente!=null){
                if($hospitalizacion==null){
                    $hospitalizacion_upd = array(
                        'fecreg'=>new MongoDate(),
                        'estado'=>'P',
                        'paciente'=>$paciente['paciente'],
                        'hist_cli'=>$item["NroHra"],
                        'import_date'=>$import_date,
                        'import_script'=>$import_script
                    );
                }else{
                    $hospitalizacion_upd = array(
                        '_id'=>$hospitalizacion['_id']
                    );
                }
                if(!isset($hospitalizacion['ning']) || $hospitalizacion['ning']==null){
                    if(isset($item['NumeroIngreso'])){
                        $hospitalizacion_upd['ning'] = $item['NumeroIngreso'];
                    }
                }

                if(!isset($hospitalizacion['fecini']) || $hospitalizacion['fecini']==null){
                    if(isset($item['FechaIngreso'])){
                        $hospitalizacion_upd['fecini'] = $item['FechaIngreso'];
                    }
                }

                if(!isset($hospitalizacion['fecfin']) || $hospitalizacion['fecfin']==null){
                    if(isset($item['FechaEgreso'])){
                        $hospitalizacion_upd['fecfin'] = $item['FechaEgreso'];
                    }
                }

                if(!isset($hospitalizacion['tipo_hosp']) || $hospitalizacion['tipo_hosp']==null){
                    if(isset($item['IdTiph'])){
                        $hospitalizacion_upd['tipo_hosp'] = $tipo_hosp[$item['IdTiph']];
                    }
                }

                if(!isset($hospitalizacion['diag']) || $hospitalizacion['diag']==null){
                    if(isset($item['IdDiag'])){
                        $hospitalizacion_upd['diag'] = $item['IdDiag'];
                    }
                }

                if(!isset($hospitalizacion['pabe']) || $hospitalizacion['pabe']==null){
                    if(isset($item['Pabellon'])){
                        $hospitalizacion_upd['pabe'] = $item['Pabellon'];
                    }
                }

                if(!isset($hospitalizacion['categoria']) || $hospitalizacion['categoria']==null){
                    $ficha_social = $f->datastore->mh_FichaSocial->findOne(array('paciente.roles.paciente.hist_cli'=>$item["NroHra"]));
                    if($ficha_social!=null){
                        $hospitalizacion_upd['categoria'] = $ficha_social['categoria'];
                    }
                }

                if(!isset($hospitalizacion_upd['_id'])){
                    echo 'SE CREO UNA NUEVA HOSPITALIZACION<br />';
                    $f->datastore->ho_hospitalizaciones->insert($hospitalizacion_upd);
                }else{
                    echo 'SE MODIFICO UNA HOSPITALIZACION<br />';
                    $f->datastore->ho_hospitalizaciones->update(array('_id'=>$hospitalizacion['_id']), array('$set'=>$hospitalizacion_upd));
                }
                $bd->tbl_Hospitalizacions->update(array('_id'=>$item['_id']),array('$set'=>array('check3'=>true)));   
            }else{
                echo 'PACIENTE NO ENCONTRADO :'.$item["NroHra"].'<br />';
            }
        }
die();

/******************************************************************************************
* mh_fichaspsicologicas
******************************************************************************************/
$col = $bd->tbl_FichasPsicologicas;
        $items = $col->find(array());
        foreach($items as $key => $item){
            $data = array(
                'moti'=>$item['ExamenPsicologico']['MotivoConsulta'],
                'refe'=>$item['ExamenPsicologico']['ReferenciaFamiliar'],
                'repa'=>$item['ExamenPsicologico']['ReferenciaPaciente'],
                'his'=>$item['ExamenPsicologico']['HistoriaIndividual'],
                'orga'=>$item['Evaluaciones']['Organicidad'],
                'inte'=>$item['Evaluaciones']['Inteligencia'],
                'perso'=>$item['Evaluaciones']['Personalidad'],
                'conclu'=>$item['Evaluaciones']['Conclusiones'],
            );

            $tmp = $bd->pacientes->findOne(array('_id'=>new MongoId($item['IdPaciente'])));
            $data['paciente'] = $tmp['_id'];
            $data['paci'] = utf8_encode($tmp['NombreApoderado'].' '.$tmp['ApellidoPaterno'].' '.$tmp['ApellidoMaterno']);
            $f->datastore->mh_fichaspsicologicas->insert($data);
        }





/******************************************************************************************
* mh_fichaspsiquiatricas
******************************************************************************************/
$col = $bd->tbl_FichasPsiquiatricas;
        $items = $col->find(array());
        foreach($items as $key => $item){
            $data = array(
                'moti'=>$item['PeritajePsiquiatrico'][0]['MotivoConsulta'],
                'desc'=>$item['PeritajePsiquiatrico'][0]['Descripcion'],
                'diag'=>$item['PeritajePsiquiatrico'][0]['DescripcionDiagnostico'],
                'infor'=>$item['Anamnesis']['Informantes'],
                'sin'=>$item['Anamnesis']['SintomasPrincipales'],
                'hist'=>$item['Anamnesis']['HistoriaEnfermedad'],
                'desa'=>$item['HistoriaPersonal']['Nota1'],
                'educ'=>$item['HistoriaPersonal']['Nota2'],
                'ocup'=>$item['HistoriaPersonal']['Nota3'],
                'pisco'=>$item['HistoriaPersonal']['Nota4'],
                'mari'=>$item['HistoriaPersonal']['Nota5'],
                'recre'=>$item['HistoriaPersonal']['Nota6'],
                'habi'=>$item['HistoriaPersonal']['Nota7'],
                'reli'=>$item['HistoriaPersonal']['Nota8'],
                'mili'=>$item['HistoriaPersonal']['Nota9'],
                'movi'=>$item['HistoriaPersonal']['Nota10'],
                'deli'=>$item['HistoriaPersonal']['Nota11'],
                'enfe'=>$item['HistoriaPersonal']['Nota12'],
                'perso'=>$item['HistoriaPersonal']['Nota13'],
                'ante'=>$item['HistoriaFamiliar']['Nota1'],
                'parip'=>$item['HistoriaFamiliar']['Nota2'],
                'parim'=>$item['HistoriaFamiliar']['Nota3'],
                'padr'=>$item['HistoriaFamiliar']['Nota4'],
                'herm'=>$item['HistoriaFamiliar']['Nota5'],
                'hish'=>$item['HistoriaFamiliar']['Nota6'],
                'apar'=>$item['HistoriaFamiliar']['Nota7'],
                'apar'=>$item['ExamenMental']['Nota1'],
                'aten'=>$item['ExamenMental']['Nota2'],
                'cur'=>$item['ExamenMental']['Nota3'],
                'efec'=>$item['ExamenMental']['Nota4'],
                'cont'=>$item['ExamenMental']['Nota5'],
                'memo'=>$item['ExamenMental']['Nota6'],
                'compre'=>$item['ExamenMental']['Nota7'],
                'diagn'=>$item['ExamenMental']['Diagnostico'],
                'doc'=>$item['ExamenMental']['Doctor']
            );

            $tmp = $bd->pacientes->findOne(array('_id'=>new MongoId($item['IdPaciente'])));
            $data['paciente'] = $tmp['_id'];
            $data['paci'] = utf8_encode($tmp['NombreApoderado'].' '.$tmp['ApellidoPaterno'].' '.$tmp['ApellidoMaterno']);
            $f->datastore->mh_fichaspsiquiatricas->insert($data);
        }





/******************************************************************************************
* mh_Charlas
******************************************************************************************/
$col = $bd->tbl_Charlas;
        $items = $col->find(array());
        foreach($items as $key => $item){
            $data = array(
                'seso'=>$item['ServicioSocial'],
                'psic'=>$item['Psicologia'],
                'enfe'=>$item['Enfermeria'],
                'psiq'=>$item['Psiquiatria'],
                'tcha'=>$item['TotalCharla'],
                'atvc'=>$item['VaronesChipinilla'],
                'atmc'=>$item['MujeresChipinilla'],
                'totc'=>$item['TotalChipinilla'],
                'atvj'=>$item['VaronesJesus'],
                'atmj'=>$item['MujeresJesus'],
                'totj'=>$item['TotalJesus'],
                'atvb'=>$item['VaronesCamana'],
                'atmb'=>$item['MujeresCamana'],
                'totb'=>$item['TotalCamana'],
                'totp'=>$item['TotalHeresi'],
                'atem'=>$item['AtencionConsultaHeresi'],
                'atch'=>$item['AtencionChipinilla'],
                'ataj'=>$item['AtencionJesus'],
                'atac'=>$item['AtencionCamana'],
                'tpme'=>$item['TotalAtencionHeresi'],
                'mes'=>$item['TotalAtencionHeresi'],
                'ano'=>$item['Ano'],
                'nomb'=>$item['Firma']
            );
            $f->datastore->mh_Charlas->insert($data);
        }





/******************************************************************************************
* mh_Hospitalizacion
******************************************************************************************/
$col = $bd->ParteDiario;
        $items = $col->find(array());
        foreach($items as $key => $item){
            $data = array(
                'num'=>$item['IdDoct'],
                'doct'=>$item['IdDoct'],
                'fech'=>$item['FecPar'],
            );
            $f->datastore->mh_ParteDiario->insert($data);
        }





/******************************************************************************************
* mh_Evolucion
******************************************************************************************/
$col = $bd->tbl_EvolucionesMH;
        $items = $col->find(array());
        foreach($items as $key => $item){
            $data = array(
                'fevo'=>$item['Fecha'],
                'evol'=>$item['Descripcion'],
            );
            $f->datastore->mh_Evolucion->insert($data);
        }





/******************************************************************************************
* mh_DiagnosticoMedico
******************************************************************************************/
$col = $bd->tbl_tbl_CentroInformaciones;
        $items = $col->find(array());
        foreach($items as $key => $item){
            $data = array(
                'nomb'=>$item['Definicion'],
                'sigl'=>$item['Siglas'],
            );
            $f->datastore->mh_DiagnosticoMedico->insert($data);
        }












		











		$params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
		if(isset($f->request->data['texto']))
			if($f->request->data['texto']!='')
				$params['texto'] = $f->request->data['texto'];
		if(isset($f->request->data['sort']))
			$params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
		$f->response->json( $f->model("mg/vari")->params($params)->get("lista") );
	}
    function execute_lista(){
        global $f;
        $params = array("page"=>$f->request->data['page'],"page_rows"=>$f->request->data['page_rows']);
        if(isset($f->request->data['texto']))
            if($f->request->data['texto']!='')
                $params['texto'] = $f->request->data['texto'];
        if(isset($f->request->data['sort']))
            $params['sort'] = array($f->request->data['sort']=>floatval($f->request->data['sort_i']));
        $f->response->json( $f->model("mg/vari")->params($params)->get("lista") );
    }
	function execute_all(){
		global $f;
		$model = $f->model('mg/vari')->get('all');
		$f->response->json($model->items);
	}
	function execute_get(){
		global $f;
		$model = $f->model("mg/vari")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one");
		$f->response->json( $model->items );
	}
	function execute_save(){
		global $f;
		$data = $f->request->data;
		$data['fecmod'] = new MongoDate();
		$data['trabajador'] = $f->session->userDB;
		if(!isset($f->request->data['_id'])){
			$f->model("mg/vari")->params(array('data'=>$data))->save("insert");
			$f->model('ac/log')->params(array(
				'modulo'=>'MG',
				'bandeja'=>'Variables',
				'descr'=>'Se cre&oacute; la variable <b>'.$data['nomb'].'</b>.'
			))->save('insert');
		}else{
			$vari = $f->model("mg/vari")->params(array("_id"=>new MongoId($f->request->data['_id'])))->get("one")->items;
			if(isset($data['estado'])){
				if($data['estado']=='H') $word = 'habilit&oacute;';
				else $word = 'deshabilit&oacute;';
				$f->model('ac/log')->params(array(
					'modulo'=>'MG',
					'bandeja'=>'Variables',
					'descr'=>'Se '.$word.' la variable <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
			}else{
				$f->model('ac/log')->params(array(
					'modulo'=>'MG',
					'bandeja'=>'Variables',
					'descr'=>'Se actualiz&oacute; la variable <b>'.$vari['nomb'].'</b>.'
				))->save('insert');
			}
			$f->model("mg/vari")->params(array('_id'=>new MongoId($f->request->data['_id']),'data'=>array(
				'$set'=>array('fecmod'=>$data['fecmod'],'valor'=>$data['valor'],'nomb'=>$data['nomb']),
				'$push'=>array('historico'=>array('valor'=>$vari['valor'],'fecreg'=>$vari['fecmod']))
			)))->save("update");
		}
		$f->response->print("true");
	}
	function execute_edit(){
		global $f;
		$f->response->view("mg/vari.edit");
	}
	function execute_details(){
		global $f;
		$f->response->view("mg/vari.details");
	}
	function execute_get_sunat_tc(){
        global $f;
    
        $token = 'apis-token-4908.KojfBrVYtN64yhm6P4N6m4iwugWKHDbA';
        $fecha = date('Y-m-d');
        // Iniciar llamada a API
        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            // para usar la api versión 2
            CURLOPT_URL => 'https://api.apis.net.pe/v2/sunat/tipo-cambio?date=' . $fecha,
            // para usar la api versión 1
            // CURLOPT_URL => 'https://api.apis.net.pe/v1/tipo-cambio-sunat?fecha=' . $fecha,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Referer: https://apis.net.pe/tipo-de-cambio-sunat-api',
                'Authorization: Bearer ' . $token
            ),
        ));
    
        $response = curl_exec($curl);
        curl_close($curl);
    
        // Datos listos para usar
        $tc = json_decode($response);
        echo json_encode($tc->precioCompra);
        $f->model('mg/vari')->params(array(
			'filter'=>array('cod'=>'TC'),
			'data'=>array(
				'$set'=>array('valor'=>$tc->precioCompra,'fecmod'=>new MongoDate()),
				'$push'=>array('historico'=>array('valor'=>$tc->precioCompra,'fecreg'=>new MongoDate()))
			)
		))->save('update');
    }
}
?>