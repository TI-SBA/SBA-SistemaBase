<?php
class Controller_pe_imas extends Controller {
	function execute_index(){
		global $f;
		$f->response->view("pe/imas.main");
	}
	function execute_import(){
		global $f;
		$equipo = $f->request->data['equipo'];
		set_time_limit(0);
		$inputFileName = './tmp/'.$f->request->data['file'];

		$fp = fopen ( $inputFileName, "r" );
        $cod = 0;
        $docs = array();
        $entis = array();
        $aux = 0;
        $import_date = date('Y-m-d-H-i');
        $registros = array();
        while ($data = fgetcsv ($fp, 1000, ",")){
            array_push($registros, $data);
        }
        $registros = array_reverse($registros);
        foreach ($registros as $data){
        	$ind = array_search(substr($data[1],2), $docs);
        	$entidad;
        	$check = false;
        	if($ind===FALSE){
        		$entidad = $f->model('mg/entidad')->params(array('filter'=>array(
	        		'docident.num'=>substr($data[1],2)
	        	),'fields'=>array(
	        		'nomb'=>true,
	        		'appat'=>true,
	        		'apmat'=>true,
	        		'tipo_enti'=>true,
	        		'docident'=>true,
	        		'roles.trabajador.cargo'=>true, 
	        		'roles.trabajador.cod_tarjeta'=>true
	        	)))->get('one')->items;
	        	if($entidad!=null){
        			$docs[] = substr($data[1],2);
                    if(isset($entidad['roles']['trabajador']['cargo']))
                        $entidad['cargo'] = $entidad['roles']['trabajador']['cargo'];
                    if(isset($entidad['roles']['trabajador']['cod_tarjeta']))
                        $entidad['cod_tarjeta'] = $entidad['roles']['trabajador']['cod_tarjeta'];
        			unset($entidad['roles']);
	        		$entis[] = $entidad;
	        		$check = true;
	        	}
        	}else{
        		$entidad = $entis[$ind];
        		$check = true;
        	}
        	if($check){
                $f->library('helpers');
                $helper=new helper();
                $trabDB = $helper->getEntiDbRel($entidad);
        		$marcacion = array(
        			'fecreg'=>new MongoDate(strtotime($data[2].' '.$data[3])),
        			'tipo'=>'E',
        			'trabajador'=>$entidad,
        			'tarjeta'=>(isset($entidad['roles']['trabajador']['cod_tarjeta'])?$entidad['roles']['trabajador']['cod_tarjeta']:"--"),
        			'equipo'=>array(
        				'_id'=>new MongoId($equipo['_id']),
        				'cod'=>$equipo['cod'],
        				'nomb'=>$equipo['nomb'],
        				'local'=>array(
        					'_id'=>new MongoId($equipo['local']['_id']),
        					'descr'=>$equipo['local']['descr'],
        					'direccion'=>$equipo['local']['direccion']
        				)
        			),
                    "import"=>$import_date
        		);
        		//print_r($marcacion);die();
                /*
                 * Obtenemos las asistencias programadas del dia
                 */
                $asist_dia = $f->model("pe/asis")->params(array(
                    'trab'=>$trabDB['_id'],
                    'day'=>substr($data[2], 0, 10)
                ))->get("trab_day_all")->items;
                $asis = array();
                if($asist_dia==null){
                    $marcacion['tipo'] = 'E';
                    $asis = array(
                        'trabajador'=>$trabDB,
                        'fec'=>new MongoDate(),
                        'ejecutado'=>array()
                    );
                }else{
                    $asis['trabajador'] = $trabDB;
                    for($ii=0; $ii<sizeof($asist_dia); $ii++){
                        if(isset($asist_dia[$ii]['ejecutado'])){
                            if(!isset($asist_dia[$ii]['ejecutado']['salida'])){
                                $marcacion['tipo'] = 'S';
                                $asis = $asist_dia[$ii];
                                $ii = sizeof($asist_dia);
                            }
                        }else{
                            $marcacion['tipo'] = 'E';
                            $asis = $asist_dia[$ii]; 
                            $ii = sizeof($asist_dia);
                        }
                    }
                }
                $marcacion = $f->model('pe/marc')->params(array('data'=>$marcacion))->save('insert')->items;
                if(isset($asis['ejecutado']['entrada'])){
                    $asis['ejecutado']['salida'] = array(
                        '_id'=>$marcacion['_id'],
                        'fecreg'=>$marcacion['fecreg'],
                        'equipo'=>$marcacion['equipo']
                    );
                    $asis['ejecutado']['tiempo'] = ($asis['ejecutado']['salida']['fecreg']->sec - $asis['ejecutado']['entrada']['fecreg']->sec)/60;
                }else{
                    $asis['ejecutado']['entrada'] = array(
                        '_id'=>$marcacion['_id'],
                        'fecreg'=>$marcacion['fecreg'],
                        'equipo'=>$marcacion['equipo']
                    );
                }
                //print_r($asis);
                if(isset($asis['_id'])){
                    $data['import_update'] = $import_date;
                    $f->model('pe/asis')->params(array('_id'=>$asis['_id'],'data'=>$asis))->save('update');
                }else{
                    $f->model('pe/asis')->params(array('data'=>$asis))->save('insert');
                }
        	}
            $aux++;
            if ($aux == 10)
                sleep(5);
        }
        $f->response->print('true');
		//unlink(IndexPath.'/tmp/'.$f->request->data['file']);
	}
    function execute_import_rol(){
            global $f;
            $f->library('excel');
            $inputFileName = './tmp/'.$f->request->data['file'];
            
            //  Read your Excel workbook
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch(Exception $e) {
                die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
            }
            
            //  Get worksheet dimensions
            $sheet = $objPHPExcel->getSheet(0); 
            $highestRow = $sheet->getHighestRow(); 
            $highestColumn = $sheet->getHighestColumn();
            
            //echo $highestRow.'<br />';
            //echo $highestColumn.'<br />';

            $cols = array("D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ");
            $init_import = false;
            $t = 0;
            /*function excelDateToDate($readDate){
                $phpexcepDate = $readDate-25569; //to offset to Unix epoch
                return strtotime("+$phpexcepDate days", mktime(0,0,0,1,1,1970));
            }*/
            $days_nomb = array(
                ''
            );
            //$periodo = trim($objPHPExcel->getActiveSheet()->getCellByColumnAndRow(2, 1)->getValue());
            $periodo = trim($objPHPExcel->getActiveSheet()->getCell('C1')->getValue());
            $periodo = explode('-', $periodo);
            if(count($periodo)!=2){
                $f->response->print('false');
                return false;
            }
            $aux = 0;
            $i=3;
            $import_date = date('Y-m-d-H-i');
            for ($row = 3; $row <= $highestRow; $row++){
                    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                                NULL,
                                                TRUE,
                                                FALSE);
                    $_id = trim($rowData[0][0]);
                    
                    if($_id!=''){
                            //print_r($_id);
                            $trab = $f->model('mg/entidad')->params(array('_id'=>new MongoId($_id)))->get('one')->items;
                            $turn = null;

                            if(isset($trab['roles']['trabajador']['turno'])){
                                    //echo 'Tiene el campo turno '.$trab['roles']['trabajador']['turno']['_id']->{'$id'}.' <br />';
                                    $turn = $f->model('pe/turn')->params(array('_id'=>$trab['roles']['trabajador']['turno']['_id']))->get('one')->items;
                                    if($turn!=null){
                                            //echo 'El turno existe <br />';
                                            for($j=3;$j<34;$j++){
                                                    //echo 'Eval: '.$cols[$j].'1 '.'<br />';
                                                    $dia = trim($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($j, 1)->getValue());
                                                    $dia_n = trim($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($j, 2)->getValue());
                                                    if($dia!=''){
                                                            $asistencia = array(
                                                                    'fec'=>new MongoDate(strtotime($periodo[1].'-'.$periodo[0].'-'.$dia)),
                                                                    'trabajador'=>array(
                                                                            '_id'=>$trab['_id'],
                                                                            'tipo_enti'=>$trab['tipo_enti'],
                                                                            'nomb'=>$trab['nomb'],
                                                                            'appat'=>$trab['appat'],
                                                                            'apmat'=>$trab['apmat']
                                                                    )
                                                            );
                                                            $valor = trim($objPHPExcel->getActiveSheet()->getCellByColumnAndRow($j, $i)->getValue());
                                                            $hora_i = null;
                                                            $hora_f = null;
                                                            $hora_i2 = null;
                                                            $hora_f2 = null;

                                                            $registrar = true;
                                                            $registrar_2 = false;
                                                            switch($valor){
                                                                    case 'M':
                                                                            foreach($turn['dias'] as $row_turno){
                                                                                    if(!isset($row_turno['tipo']) || $row_turno['tipo']=='M'){
                                                                                            if(floatval(date('w',strtotime($periodo[1].'-'.$periodo[0].'-'.$dia)))==floatval($row_turno['dia'])){
                                                                                                    $hora_i = $row_turno['horas']['ini'];
                                                                                                    $hora_f = $row_turno['horas']['fin'];
                                                                                            }
                                                                                    }
                                                                            }
                                                                            break;
                                                                    case 'M/T':
                                                                            foreach($turn['dias'] as $row_turno){
                                                                                    if(!isset($row_turno['tipo']) || $row_turno['tipo']=='M'){
                                                                                            if(floatval(date('w',strtotime($periodo[1].'-'.$periodo[0].'-'.$dia)))==floatval($row_turno['dia'])){
                                                                                                    $hora_i = $row_turno['horas']['ini'];
                                                                                                    $hora_f = $row_turno['horas']['fin'];
                                                                                            }
                                                                                    }
                                                                                    if(isset($row_turno['tipo']) && $row_turno['tipo']=='T'){
                                                                                            if(floatval(date('w',strtotime($periodo[1].'-'.$periodo[0].'-'.$dia)))==floatval($row_turno['dia'])){
                                                                                                    $hora_i2 = $row_turno['horas']['ini'];
                                                                                                    $hora_f2 = $row_turno['horas']['fin'];
                                                                                            }
                                                                                            $registrar_2 = true;
                                                                                    }
                                                                            }
                                                                            break;
                                                                    case 'M/N':
                                                                            foreach($turn['dias'] as $row_turno){
                                                                                    if(!isset($row_turno['tipo']) || $row_turno['tipo']=='M'){
                                                                                            if(floatval(date('w',strtotime($periodo[1].'-'.$periodo[0].'-'.$dia)))==floatval($row_turno['dia'])){
                                                                                                    $hora_i = $row_turno['horas']['ini'];
                                                                                                    $hora_f = $row_turno['horas']['fin'];
                                                                                            }
                                                                                    }
                                                                                    if(isset($row_turno['tipo']) && $row_turno['tipo']=='N'){
                                                                                            if(floatval(date('w',strtotime($periodo[1].'-'.$periodo[0].'-'.$dia)))==floatval($row_turno['dia'])){
                                                                                                    $hora_i2 = $row_turno['horas']['ini'];
                                                                                                    $hora_f2 = $row_turno['horas']['fin'];
                                                                                            }
                                                                                            $registrar_2 = true;
                                                                                    }
                                                                            }
                                                                            break;
                                                                    case 'T':
                                                                            foreach($turn['dias'] as $row_turno){
                                                                                    if(isset($row_turno['tipo']) && $row_turno['tipo']=='T'){
                                                                                            if(floatval(date('w',strtotime($periodo[1].'-'.$periodo[0].'-'.$dia)))==floatval($row_turno['dia'])){
                                                                                                    $hora_i = $row_turno['horas']['ini'];
                                                                                                    $hora_f = $row_turno['horas']['fin'];
                                                                                            }
                                                                                    }
                                                                            }
                                                                            break;
                                                                    case 'N':
                                                                            foreach($turn['dias'] as $row_turno){
                                                                                    if(isset($row_turno['tipo']) && $row_turno['tipo']=='N'){
                                                                                            if(floatval(date('w',strtotime($periodo[1].'-'.$periodo[0].'-'.$dia)))==floatval($row_turno['dia'])){
                                                                                                    $hora_i = $row_turno['horas']['ini'];
                                                                                                    $hora_f = $row_turno['horas']['fin'];
                                                                                            }
                                                                                    }
                                                                            }
                                                                            break;
                                                                    default:
                                                                            $registrar = false;
                                                                            break;
                                                            }
                                                            if($registrar){
                                                                    $eval_hora_i = explode(':', $hora_i);
                                                                    $eval_hora_i = floatval($eval_hora_i[0])+(floatval($eval_hora_i[1])/60);
                                                                    $eval_hora_f = explode(':', $hora_f);
                                                                    $eval_hora_f = floatval($eval_hora_f[0])+(floatval($eval_hora_f[1])/60);
                                                                    $dia_ini = $dia;
                                                                    $dia_fin = $dia;
                                                                    if($eval_hora_i>$eval_hora_f){
                                                                            $dia_fin++;
                                                                    }
                                                                    $dia_ini = str_pad($dia_ini, 2, "0", STR_PAD_LEFT);
                                                                    $dia_fin = str_pad($dia_fin, 2, "0", STR_PAD_LEFT);
                                                                    $asistencia['programado'] = array(
                                                                            'inicio'=>new MongoDate(strtotime($periodo[1].'-'.$periodo[0].'-'.$dia_ini.' '.$hora_i)),
                                                                            'fin'=>new MongoDate(strtotime($periodo[1].'-'.$periodo[0].'-'.$dia_fin.' '.$hora_f)),
                                                                            //'inicio_tmp'=>$periodo[1].'-'.$periodo[0].'-'.$dia_ini.' '.$hora_i,
                                                                            //'fin_tmp'=>$periodo[1].'-'.$periodo[0].'-'.$dia_fin.' '.$hora_f
                                                                    );
                                                                    $asistencia['import']=$import_date;
                                                                    $f->model('pe/asis')->params(array('data'=>$asistencia))->save('insert');
                                                            }
                                                            if($registrar_2){
                                                                    $eval_hora_i = explode(':', $hora_i2);
                                                                    $eval_hora_i = floatval($eval_hora_i[0])+(floatval($eval_hora_i[1])/60);
                                                                    $eval_hora_f = explode(':', $hora_f2);
                                                                    $eval_hora_f = floatval($eval_hora_f[0])+(floatval($eval_hora_f[1])/60);
                                                                    $dia_ini = $dia;
                                                                    $dia_fin = $dia;
                                                                    if($eval_hora_i>$eval_hora_f){
                                                                            $dia_fin++;
                                                                    }
                                                                    $dia_ini = str_pad($dia_ini, 2, "0", STR_PAD_LEFT);
                                                                    $dia_fin = str_pad($dia_fin, 2, "0", STR_PAD_LEFT);
                                                                    $asistencia_2 = array(
                                                                            'fec'=>new MongoDate(strtotime($periodo[1].'-'.$periodo[0].'-'.$dia)),
                                                                            'trabajador'=>array(
                                                                                    '_id'=>$trab['_id'],
                                                                                    'tipo_enti'=>$trab['tipo_enti'],
                                                                                    'nomb'=>$trab['nomb'],
                                                                                    'appat'=>$trab['appat'],
                                                                                    'apmat'=>$trab['apmat']
                                                                            )
                                                                    );
                                                                    $asistencia_2['programado'] = array(
                                                                            'inicio'=>new MongoDate(strtotime($periodo[1].'-'.$periodo[0].'-'.$dia_ini.' '.$hora_i2)),
                                                                            'fin'=>new MongoDate(strtotime($periodo[1].'-'.$periodo[0].'-'.$dia_fin.' '.$hora_f2)),
                                                                            //'inicio_tmp'=>$periodo[1].'-'.$periodo[0].'-'.$dia_ini.' '.$hora_i2,
                                                                            //'fin_tmp'=>$periodo[1].'-'.$periodo[0].'-'.$dia_fin.' '.$hora_f2
                                                                    );
                                                                    $asistencia_2['import']=$import_date;
                                                                    $f->model('pe/asis')->params(array('data'=>$asistencia_2))->save('insert');
                                                            }
                                                    }
                                            }
                                    }
                            }

                    }
                    $aux++;
                    $i++;
            }
            unlink(IndexPath.'/tmp/'.$f->request->data['file']);
            $f->response->print('true');
    }
    function execute_download_format(){
            global $f;
            $data = $f->request->data;
            $params = array(
                    'programa'=>new MongoId($data['programa']),
                    'mes'=>$data['mes'],
                    'ano'=>$data['ano'],
                    'estado'=>'H'
            );
            $model = $f->model('mg/entidad')->params($params)->get('all_trab');
            $items = array();
            if($model->items!=null){
                foreach($model->items as $i=>$item){
                    $turn = $f->model('pe/turn')->params(array('_id'=>$item['roles']['trabajador']['turno']['_id']))->get('one')->items;
                    if($turn!=null){
                        if($turn['tipo']=='R'){
                            array_push($items, $item);
                        }
                    }
                }
            }
            //$model = $f->model('mg/entidad')->params(array('roles'=>'trabajador'))->get('all_trab');
            $params = array(
                    'mes'=>$data['mes'],
                    'ano'=>$data['ano'],
                    'items'=>$items
            );
            $f->response->view("pe/repo.imas.export",$params );
    }
}
?>