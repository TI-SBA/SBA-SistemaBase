<?php
global $f;
$meses = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre');
$cant = array('1'=>'Mensual','3'=>'Trimestral','6'=>'Semestral','12'=>'Anual');
$title1 = "Reporte ".$cant[$params['cant']]." para Trámite Documentario";
$title2 = " desde ".$meses[$params['mes']]." del ".$params['ano'];
if($flag==true){
	$f->library("helpers");
	$helper = new helper();
	$helper->printGraphBarOrga(array(
		'dataY'=>$ubic,
		'labelY'=>'Expedientes',
		'dataX'=>$orga,
		'labelX'=>'Organizaciones',
		'title'=>'Cantidad de Expedientes por Organización',
		'path'=>IndexPath."/temp/tdg1.png"
	));
	$helper->printGraphBar(array(
		'dataY'=>$flujos['cant'],
		'labelY'=>'Expedientes',
		'dataX'=>$flujos['tipo'],
		'labelX'=>'Flujo',
		'title'=>'Cantidad de Expedientes por Flujo',
		'path'=>IndexPath."/temp/tdg2.png"
	));
	$helper->printGraphPieNotPercent(array(
		'dataY'=>$estado['cant_tupa'],
		'labelY'=>'Expedientes',
		'legend'=>$estado['tipo'],
		'color'=>$estado['color'],
		'title'=>'Cantidad de Expedientes por Estado (TUPA)',
		'path'=>IndexPath."/temp/tdg3.png"
	));
	if($resolu['cant'][0]!=0||$resolu['cant'][1]!=0){
		$helper->printGraphPieNotPercent(array(
			'dataY'=>$resolu['cant'],
			'labelY'=>'Expedientes',
			'legend'=>$resolu['tipo'],
			'color'=>$resolu['color'],
			'title'=>'Cantidad de Expedientes por Resolución',
			'path'=>IndexPath."/temp/tdg4.png"
		));
	}
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
	$pdf->SetXY(0,25);
	$pdf->Image('temp/tdg1.png' , 10 ,30, 180 , 200,'PNG');
	$pdf->AddPage();
	$pdf->Image('temp/tdg2.png' , 10 ,30, 180 , 100,'PNG');
	$pdf->Image('temp/tdg3.png' , 10 ,140, 180 , 140,'PNG');
	if($resolu['cant'][0]!=0||$resolu['cant'][1]!=0){
		$pdf->AddPage();
		$pdf->Image('temp/tdg4.png' , 10 ,30, 180 , 140,'PNG');
	}
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