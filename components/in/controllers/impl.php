<?php
class Controller_in_impl extends Controller {
	function execute_index(){
		global $f;
		$f->response->view("in/impl.main");
	}
	function execute_import(){
		global $f;
		//  Include PHPExcel_IOFactory
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
		
		$init_import = false;
		$t = 0;
		
		//  Loop through each row of the worksheet in turn
		$playas = $f->model('in/play')->get('all')->items;
		$igv = $f->model('mg/vari')->params(array('cod'=>'IGV'))->get('by_cod')->items['valor'];
		function excelDateToDate($readDate){
		    $phpexcepDate = $readDate-25569; //to offset to Unix epoch
		    return strtotime("+$phpexcepDate days", mktime(0,0,0,1,1,1970));
		}
		for ($row = 1; $row <= $highestRow; $row++){
		    //  Read a row of data into an array
		    $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
		                                    NULL,
		                                    TRUE,
		                                    FALSE);
			$rowData = $rowData[0];
			/*if($rowData[0]=='FECHA'){
				$init_import = true;
				$t = 0;
			}
			if($rowData[1]==''){
				$init_import = false;
			}
		    if($init_import==true){
		    	if($t>=1){*/
		    if(true){
		    	if(true){
		    		$lock = false;
		    		$date = $rowData[0];
			    	$date = excelDateToDate($date);
			    	$comp = array(
			    		'modulo'=>'IN',
			    		'estado'=>'R',
						'tipo'=>$rowData[1],
						'serie'=>$rowData[2],
						'num'=>intval($rowData[3]),
						'doc_cliente'=>$rowData[4],
						'cliente'=>$rowData[5],
						//'playa'=>$rowData[7],
						'moneda'=>'S',
						'total'=>floatval($rowData[6]),
						'igv'=>0,
						'subtotal'=>0,
						'fecreg'=>new MongoDate($date),
						'fecmod'=>new MongoDate(),
						'autor'=>$f->session->userDB
					);
		    		if(strtoupper(trim($rowData[5]))=='ANULADO'){
		    			$comp['estado'] = 'X';
					}else{
						$comp['subtotal'] = $comp['total']/((floatval($igv)/100)+1);
						if($comp['total']!=10&&$comp['total']!=2&&$comp['total']!=297){
							$comp['subtotal'] = number_format($comp['subtotal'], 4, '.', '');
							$comp['subtotal'] = number_format($comp['subtotal'], 3, '.', '');
						}elseif($comp['total']!=393.5){
							$comp['subtotal'] = number_format($comp['subtotal'], 3, '.', '');
						}elseif($comp['total']==6){
							$comp['subtotal'] = number_format($comp['subtotal'], 2, '.', '');
						}else{
							$comp['subtotal'] = substr($comp['subtotal'], 0, strpos($comp['subtotal'],'.')+4);
						}
						$comp['subtotal'] = number_format($comp['subtotal'], 2, '.', '');
						$comp['igv'] = $comp['total'] - $comp['subtotal'];
					}
					if(isset($rowData[7])){
						if($rowData[7]!=''){
							$comp['subtotal'] = floatval($rowData[7]);
							$comp['igv'] = floatval($rowData[8]);
						}
					}
					switch ($comp['tipo']) {
						case '01':
							$comp['tipo'] = 'F';
							break;
						case '03':
							$comp['tipo'] = 'B';
							break;
					}
					$tipo = '';
					if(isset($rowData[9])){
						$tipo = $rowData[9];
					}
					if($tipo == ''){
						foreach ($playas as $key => $item) {
							/*if($item['nomb']==$comp['playa']){
								$comp['playa'] = array(
									'_id'=>$item['_id'],
									'nomb'=>$item['nomb'],
									'cuenta'=>$item['cuenta']
								);
								$lock = true;
								break;
							}*/
							if($comp['tipo']=='B'){
								if(intval($item['talonario_bol']['serie'])==intval($comp['serie'])){
									$comp['playa'] = array(
										'_id'=>$item['_id'],
										'nomb'=>$item['nomb'],
										'cuenta'=>$item['cuenta']
									);
									$lock = true;
									break;
								}
							}else if($comp['tipo']=='F'){
								if(intval($item['talonario_fac']['serie'])==intval($comp['serie'])){
									$comp['playa'] = array(
										'_id'=>$item['_id'],
										'nomb'=>$item['nomb'],
										'cuenta'=>$item['cuenta']
									);
									$lock = true;
									break;
								}
							}
						}
					}elseif($tipo=='C'){
						$comp['playa'] = array(
							'_id'=>new MongoId('586d1e143e6037a76e8b4567'),
							'nomb'=>'CANCHA DEPORTIVA LA PAZ',
							'cuenta'=>array(
								'_id'=>new MongoId('586e5d073e6037ae3c8b4568'),
								'cod'=>'1201.0303.47.20',
								'descr'=>'CANCHA DEPORTIVA LA PAZ'
							)
						);
						$lock = true;
					}elseif($tipo=='S'){
						$comp['playa'] = array(
							'_id'=>new MongoId('556dd423cc1e90c40900013c'),
							'nomb'=>'Coch/Sotano',
							'cuenta'=>array(
								'_id'=>new MongoId('51c20abd4d4a13740b00000e'),
								'cod'=>'1201.0303.47.11',
								'descr'=>'PLAYA EL SOTANO-HOTEL'
							)
						);
						$lock = true;
					}
					if($lock==true){
						$tmp_comp = $f->model('cj/comp')->params(array(
							'tipo'=>$comp['tipo'],
							'serie'=>$comp['serie'],
							'num'=>$comp['num']
						))->get('verify')->items;
						if(gettype($tmp_comp)=='array'){
							echo "aaaa";
							if(isset($tmp_comp['_id'])){
								$f->model('cj/comp')->params(array('_id'=>$tmp_comp['_id'],'data'=>$comp))->save('update');
							}
							/*$f->response->json(array('error'=>'1'));
							break;*/
						}else{
							$f->model('cj/comp')->params(array('data'=>$comp))->save('insert');
						}
					}
				}else
					$t++;
		    }
		}
		unlink(IndexPath.'/tmp/'.$f->request->data['file']);
	}
}
?>