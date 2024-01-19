<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante.gif',10,10,190,250);	
		$y=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');		
		$this->SetXY(5,5);$this->MultiCell(0,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Modulo de Personal",'0','C');
		$this->SetFont('Arial','B',12);
		$this->SetXY(0,30);$this->MultiCell(0,5,"LISTA DE TURNOS DE TRABAJADORES",'0','C');
		
	}	
	function Publicar($reporte){
		$y=50;
		$yini = $y;	
		$this->SetFont('Arial','B',6);
		$this->SetXY(5,$y-5);$this->MultiCell(50,5,"NOMBRE DE TURNO",'1','C');
		$this->SetXY(55,$y-5);$this->MultiCell(25,5,"LUNES",'1','C');
		$this->SetXY(80,$y-5);$this->MultiCell(25,5,"MARTES",'1','C');
		$this->SetXY(105,$y-5);$this->MultiCell(25,5,"MIERCOLES",'1','C');
		$this->SetXY(130,$y-5);$this->MultiCell(25,5,"JUEVES",'1','C');
		$this->SetXY(155,$y-5);$this->MultiCell(25,5,"VIERNES",'1','C');
		$this->SetXY(180,$y-5);$this->MultiCell(25,5,"SABADO",'1','C');
		$this->SetXY(205,$y-5);$this->MultiCell(25,5,"DOMINGO",'1','C');
		$this->SetFont('Arial','',6);
		for($i = 0;$i<count($reporte);$i++){
			
			if($y>205){
				$this->AddPage();
				$y = $yini;
				$this->SetFont('Arial','B',6);
				$this->SetXY(5,$y-5);$this->MultiCell(50,5,"NOMBRE DE TURNO",'1','C');
				$this->SetXY(55,$y-5);$this->MultiCell(25,5,"LUNES",'1','C');
				$this->SetXY(80,$y-5);$this->MultiCell(25,5,"MARTES",'1','C');
				$this->SetXY(105,$y-5);$this->MultiCell(25,5,"MIERCOLES",'1','C');
				$this->SetXY(130,$y-5);$this->MultiCell(25,5,"JUEVES",'1','C');
				$this->SetXY(155,$y-5);$this->MultiCell(25,5,"VIERNES",'1','C');
				$this->SetXY(180,$y-5);$this->MultiCell(25,5,"SABADO",'1','C');
				$this->SetXY(205,$y-5);$this->MultiCell(25,5,"DOMINGO",'1','C');
				$this->SetFont('Arial','',6);
				////NOMBRE DEL TURNO
				$this->SetXY(5,$y);$this->MultiCell(50,6,$reporte[$i]["nomb"],'1','C');
				//DIAS DEL TURNO
				//
				$this->SetXY(55,$y);$this->MultiCell(25,6,$reporte[$i]['dias'][0]['horas']['ini'].'-'.$reporte[$i]['dias'][0]['horas']['fin'],'1','C');	
				$this->SetXY(80,$y);$this->MultiCell(25,6,$reporte[$i]['dias'][1]['horas']['ini'].'-'.$reporte[$i]['dias'][1]['horas']['fin'],'1','C');	
				$this->SetXY(105,$y);$this->MultiCell(25,6,$reporte[$i]['dias'][2]['horas']['ini'].'-'.$reporte[$i]['dias'][2]['horas']['fin'],'1','C');	
				$this->SetXY(130,$y);$this->MultiCell(25,6,$reporte[$i]['dias'][3]['horas']['ini'].'-'.$reporte[$i]['dias'][3]['horas']['fin'],'1','C');	
				$this->SetXY(155,$y);$this->MultiCell(25,6,$reporte[$i]['dias'][4]['horas']['ini'].'-'.$reporte[$i]['dias'][4]['horas']['fin'],'1','C');	
				$this->SetXY(180,$y);$this->MultiCell(25,6,$reporte[$i]['dias'][5]['horas']['ini'].'-'.$reporte[$i]['dias'][5]['horas']['fin'],'1','C');	
				$this->SetXY(205,$y);$this->MultiCell(25,6,$reporte[$i]['dias'][6]['horas']['ini'].'-'.$reporte[$i]['dias'][6]['horas']['fin'],'1','C');	
				
				$y+=6;
			}else{
				$this->SetXY(5,$y);$this->MultiCell(50,6,$reporte[$i]["nomb"],'1','C');
				$this->SetXY(55,$y);$this->MultiCell(25,6,$reporte[$i]['dias'][0]['horas']['ini'].'-'.$reporte[$i]['dias'][0]['horas']['fin'],'1','C');	
				$this->SetXY(80,$y);$this->MultiCell(25,6,$reporte[$i]['dias'][1]['horas']['ini'].'-'.$reporte[$i]['dias'][1]['horas']['fin'],'1','C');	
				$this->SetXY(105,$y);$this->MultiCell(25,6,$reporte[$i]['dias'][2]['horas']['ini'].'-'.$reporte[$i]['dias'][2]['horas']['fin'],'1','C');	
				$this->SetXY(130,$y);$this->MultiCell(25,6,$reporte[$i]['dias'][3]['horas']['ini'].'-'.$reporte[$i]['dias'][3]['horas']['fin'],'1','C');	
				$this->SetXY(155,$y);$this->MultiCell(25,6,$reporte[$i]['dias'][4]['horas']['ini'].'-'.$reporte[$i]['dias'][4]['horas']['fin'],'1','C');	
				$this->SetXY(180,$y);$this->MultiCell(25,6,$reporte[$i]['dias'][5]['horas']['ini'].'-'.$reporte[$i]['dias'][5]['horas']['fin'],'1','C');	
				$this->SetXY(205,$y);$this->MultiCell(25,6,$reporte[$i]['dias'][6]['horas']['ini'].'-'.$reporte[$i]['dias'][6]['horas']['fin'],'1','C');	
				$y+=6;
			}
			
		}

	} 
}

$pdf=new repo('L','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($reporte);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>
