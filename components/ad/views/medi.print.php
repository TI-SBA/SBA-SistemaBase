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
		$this->SetFont('Arial','B',16);
		$this->SetXY(5,$y);$this->MultiCell(200,5,	"LISTADO DE FICHAS MEDICAS",'0','C');
		
	}		
	function Publicar($medica){
		
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		
//CABECERA
		$y=$y+3;
		$this->SetFont('Arial','B',11);
		$this->SetXY(5,$y);$this->MultiCell(60,9,"Paciente",'1','L');
		$this->SetXY(65,$y);$this->MultiCell(20,9,"Estado",'1','L');
		$this->SetXY(85,$y);$this->MultiCell(25,9,"Diagnostico",'1','L'); 
		$this->SetXY(110,$y);$this->MultiCell(47,9,"Responde a tratamiento",'1','L');
		$this->SetXY(157,$y);$this->MultiCell(25,9,"Agresividad",'1','L');
		$this->SetXY(182,$y);$this->MultiCell(15,9,"Visitas",'1','L');		
//CUERPO
		$y=$y+9;
		$yini= $y;
		$this->SetFont('Arial','',11);
			for($i = 0;$i<count($medica);$i++){
				
				$this->SetXY(65,$y);$this->MultiCell(20,9,$medica[$i]['peso'],'0','L');
				$this->SetXY(85,$y);$this->MultiCell(25,9,$medica[$i]['diag'],'0','L');
				$this->SetXY(110,$y);$this->MultiCell(47,9,$medica[$i]['tra'],'0','L');
				$this->SetXY(157,$y);$this->MultiCell(25,9,$medica[$i]['agres'],'0','L');
				$this->SetXY(182,$y);$this->MultiCell(15,9,$medica[$i]['visi'],'0','L');
				$this->SetXY(5,$y);$this->MultiCell(60,9,$medica[$i]['paci'],'0','L');
				$y=$this->getY();
				$this->Line(5, $y, 197,$y);

			}
			$this->Line(5, $yini, 5,$y);
			$this->Line(65, $yini, 65,$y);
			$this->Line(85, $yini, 85,$y);
			$this->Line(110, $yini, 110,$y);
			$this->Line(157, $yini, 157,$y);
			$this->Line(182, $yini, 182,$y);
			$this->Line(197, $yini, 197,$y);

			


}

	
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($medica);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>