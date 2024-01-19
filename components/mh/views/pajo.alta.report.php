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
		$this->SetFont('Arial','B',10);
		$this->SetXY(5,30);$this->MultiCell(200,5,	"REPORTE DE PACIENTES HOSPITALIZADOS",'0','C');
		
		
	}	




	function Publicar($paciente){
		
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		
//CABECERA
		$y=$y+25;
		$this->SetFont('Arial','B',8);

		
		
		
		$this->SetXY(45,59);$this->MultiCell(15,9,"SEXO",'1','C');
		$this->SetXY(45,68);$this->MultiCell(15,9,"F",'1','C');
		$this->SetXY(45,77);$this->MultiCell(15,9,"M",'1','C');
		$this->SetXY(60,$y);$this->MultiCell(62,9,"PABELLON",'1','C');
		$this->SetXY(60,59);$this->MultiCell(20,9,"PARCIAL",'1','C');
		$this->SetXY(80,59);$this->MultiCell(22,9,"INTERMEDIOS",'1','C');
		$this->SetXY(102,59);$this->MultiCell(20,9,"INTENSIVOS",'1','C');
		

		$y=$y+9;
		$yini = $y;

		for($i = 0; $i<count($paciente);$i++){
			$this->SetXY(45,59);$this->MultiCell(15,9,"SEXO",'1','C');
			$this->SetXY(45,68);$this->MultiCell(15,9,"F",'1','C');
			$this->SetXY(45,77);$this->MultiCell(15,9,"M",'1','C');
			$this->SetXY(60,$y);$this->MultiCell(62,9,"PABELLON",'1','C');
			$this->SetXY(60,59);$this->MultiCell(20,9,"PARCIAL",'1','C');
			$this->SetXY(80,59);$this->MultiCell(22,9,"INTERMEDIOS",'1','C');
			$this->SetXY(102,59);$this->MultiCell(20,9,"INTENSIVOS",'1','C');
			$y+=9;
		}
			


}

	
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($paciente);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>