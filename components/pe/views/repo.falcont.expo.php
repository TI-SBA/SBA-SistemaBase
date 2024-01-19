<?php
global $f;
$f->library('excel');
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load(IndexPath.DS.'templates/pe/falcont.xlsx');
//echo date('H:i:s') , " Add new data to the template" , EOL;
$items=$data;
$baseRow = 11;
$index=0;

//$cols = array("C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ");
$final=$baseRow;
$arrayrows = array('c','d','e');
$nada = false;
foreach($items as $r => $dataRow) {
	
	if($r=0){
		$base=$baseRow;
	}else{
		$base=count($items[$r-1]->trabajadores)+$baseRow;		
	}
	
	
		$row = $base + $r;
		$objPHPExcel->getActiveSheet()->insertNewRowBefore($row,1);
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $dataRow->nomb); 
		$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setBold(true);
		$nro=1;	
		
		for($i=0;$i<count($dataRow->trabajadores);$i++){
			if(is_null($dataRow->trabajadores[$i]['inci'])){
				$nada = false;
			}else{
				$nada = true;
				$row2 = $row + ($i+1);
				$objPHPExcel->getActiveSheet()->insertNewRowBefore($row2,1);
				$objPHPExcel->getActiveSheet()->setCellValue("A".$row2, $nro);	
				$objPHPExcel->getActiveSheet()->getStyle("B".$row2)->getFont()->setBold(false);
				$objPHPExcel->getActiveSheet()->setCellValue("B".$row2, $dataRow->trabajadores[$i]["appat"]." ".$dataRow->trabajadores[$i]["apmat"].", ".$dataRow->trabajadores[$i]["nomb"]);
				foreach ($dataRow->trabajadores[$i]['inci'] as $cont) {
				
					switch ($cont['tipo']['tipo']) {
						case 'IN':
							$a++;
							break;
						case 'PE':
							$b++;
							break;
					
						case 'TA':
							$c++;
							break;
					
				
					}
					$objPHPExcel->getActiveSheet()->setCellValue('C'.$row2, $a);
					$objPHPExcel->getActiveSheet()->setCellValue('D'.$row2, $b);
					$objPHPExcel->getActiveSheet()->setCellValue('E'.$row2, $c);
		
				}
				$arrayrows['c'][] = ($a);
				$arrayrows['d'][] = ($b);
				$arrayrows['e'][] = ($c);
				$a=0;
				$b=0;
				$c=0;
			
		
			}
			$nro++;
		
		}
		
		
		$objPHPExcel->getActiveSheet()->insertNewRowBefore($row2+1,1);
		$objPHPExcel->getActiveSheet()->getStyle("B".($row2+1))->getFont()->setBold(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.($row2+1),"SUB TOTAL");
		//$row2 = $row + ($i+1);
		
		if(isset($dataRow->trabajadores[0]['inci'])){
			foreach ($dataRow->trabajadores[0]['inci'] as $contador) {
				
				switch ($contador['tipo']['tipo']) {
					case 'IN':
						$d++;
						break;
					case 'PE':
						$e++;
						break;
					
					case 'TA':
						$f++;
						//$generalf=$generalf+("=SUM(".'E'.($base+1).":".'E'.$row2.")");
						break;
					
				
				}
				//$generalf="=SUM(".'E'.($base+1).":".'E'.$row2.")";
				//var_dump($imprif=$imprif+$generalf);die;
				$objPHPExcel->getActiveSheet()->setCellValue('C'.($row2+1),"=SUM(".'C'.($base+1).":".'C'.$row2.")");
				$objPHPExcel->getActiveSheet()->setCellValue('D'.($row2+1), "=SUM(".'D'.($base+1).":".'D'.$row2.")");
				$objPHPExcel->getActiveSheet()->setCellValue('E'.($row2+1), "=SUM(".'E'.($base+1).":".'E'.$row2.")");
				
			
			
			
			}
		}
		
			
		
		
		$d=0;
		$e=0;
		$f=0;
		
		$final++;
	
}
	
	
	$row2 = $row+$final+($i+1);
		if($nada==true){
			$objPHPExcel->getActiveSheet()->insertNewRowBefore($row2+1,1);
			$objPHPExcel->getActiveSheet()->getStyle("B".($row2+1))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($row2+1),"SUB GENERAL");
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($row2+1),"=SUM(". implode(",", $arrayrows['c']).")");
			$objPHPExcel->getActiveSheet()->setCellValue('D'.($row2+1),"=SUM(". implode(",", $arrayrows['d']).")");
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($row2+1),"=SUM(". implode(",", $arrayrows['e']).")");
			//"=SUM(". implode(",", $arrayrows['c']) .")";
		}else{
			//
			$objPHPExcel->getActiveSheet()->insertNewRowBefore($row2+1,1);
			$objPHPExcel->getActiveSheet()->getStyle("B".($row2+1))->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.($row2+1),"SUB GENERAL");
			$objPHPExcel->getActiveSheet()->setCellValue('C'.($row2+1),"0");
			$objPHPExcel->getActiveSheet()->setCellValue('D'.($row2+1),"0");
			$objPHPExcel->getActiveSheet()->setCellValue('E'.($row2+1),"0");
		}
	
			
		
		
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="falcont.xlsx"');
header('Cache-Control: max-age=0');
 
//Creamos el Archivo .xlsx
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
?>