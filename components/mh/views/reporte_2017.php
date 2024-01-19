
<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante.gif',10,10,190,275);	
		$y=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');		
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(10,20);$this->MultiCell(60,5,"'Moises Heresi'",'0','C');

		
		
	}	




	function Publicar($diario){
	/*	
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		

		$y=$y+25;
		$this->SetFont('Arial','B',10);
		$this->SetXY(5,30);$this->MultiCell(200,5,	"FICHA DE REPORTE MENSUAL DE SERVICIOS - SOCIEDAD DE BENEFICIENCIA DE AREQUIPA - REPORTE QUINCENAL DE SERVICIOS - SALUD MENTAL",'0','C');
		$this->SetFont('Arial','B',8);
		
	*/		


	}

	
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($diario);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>