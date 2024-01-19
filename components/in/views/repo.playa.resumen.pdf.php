<?php
global $f;
if(sizeof($data)==0) $flag = false;
else{
	$flag = true;
	$dataY = array();
	$dataX = array();
	foreach ($data as $item) {
		$dataX[] = $item['nomb'];
		$dataY[] = floatval($item['total']);
	}
}
if($flag==true){
	$f->library("helpers");
	$helper = new helper();
	$helper->printGraphBar(array(
		'angle'=>0,
		'dataY'=>$dataY,
		'labelY'=>'',
		'dataX'=>$dataX,
		'labelX'=>'Playas',
		'title'=>'Recaudacion por Playa '.date('M y',$ini->sec),
		'path'=>IndexPath."/temp/in_pl_res.png"
	));
	$f->library('pdf');
	$pdf=new FPDF('L');
	$pdf->AddPage();
	$pdf->SetFont('Arial','',15);
	$pdf->SetXY(40,10);
	$pdf->Cell(10,0);
	$pdf->Write(5,$title1);
	$pdf->SetXY(50,15);
	$pdf->Cell(20,0);
	$pdf->Write(5,$title2);
	$pdf->SetXY(0,25);
	$pdf->Image('temp/in_pl_res.png' , 10 ,30, 250 , 150,'PNG');
	$pdf->Output();
}else{
	$f->library('pdf');
	$pdf=new FPDF();
	$pdf->AddPage();
	$pdf->SetFont('Arial','',15);
	$pdf->SetXY(40,10);
	$pdf->Cell(10,0);
	$pdf->Write(5,$title1);
	$pdf->SetXY(50,15);
	$pdf->Cell(20,0);
	$pdf->Write(5,$title2);
	$pdf->SetXY(0,20);
	$pdf->SetFont('Arial','',12);
	$pdf->Cell(0,0);
	$pdf->Write(5,'No hay data disponible para el periodo seleccionado');
	
	$pdf->Output();
}
?>