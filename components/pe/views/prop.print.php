<?php
global $f;
$f->library('pdf');

class prop extends FPDF
{
	function Header(){
		$this->Image(IndexPath.DS.'templates/pe/propinas.gif',15,15,180,276);

	}		
	function Publicar($items,$filtros){
		$this->SetFont('courier','',9);
		$x=0;
		$y=49;//41
		$y_marg = 5;
		$this->ln();
		$this->SetFont('courier','',10);	
		$meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
		$this->SetXY(50,35);$this->Cell(113,$y_marg,$filtros["organomb"],'0',0,'L',0);
		$this->Cell(100,$y_marg,$meses[$filtros["mes"]]."-".$filtros["periodo"],'0',0,'L',0);
		$this->SetY($y);
		$index=1;
		foreach($items as $data){
			$this->SetX(15);$this->Cell(140,$y_marg,$index.".- ".$data["appat"]." ".$data["apmat"].", ".$data["nomb"],'0',0,'L',0);
			$this->Cell(140,$y_marg,$data["practicas"]["propina"],'0',0,'L',0);
			$this->ln();
			$index++;
		}		
	}
	function Footer()
	{
		//footer
	} 
	 
}

$pdf=new prop('P','mm','A4');
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("boleta");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($items,$filtros);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>