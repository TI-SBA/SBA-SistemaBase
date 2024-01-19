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
		$this->SetFont('Arial','B',11);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Archivo Central",'0','C');
		$this->SetFont('Arial','B',14);
		$this->SetXY(5,30);$this->MultiCell(200,5,	"LISTA DE TIPOS DE SERIE DOCUMENTARIA",'0','C');
		
	}	
	
 		
	function Publicar($tise){

		//$total_consultas = 0;
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$y=$y+20;
		$this->SetFont('Arial','B',8);
		$yini = $y;

			$this->SetXY(50,$y);$this->MultiCell(45,9,"Tipos de Serie Documental",'1','C');
			$this->SetXY(95,$y);$this->MultiCell(35,9,"Descripcion",'1','C');
			
			
		$y=$y+9;
		for($i=0;$i<count($tise);$i++){
			$this->SetFont('Arial','',7);
			$this->SetXY(50,$y);$this->MultiCell(45,9,"".$tise[$i]["tipo"],'0','C');
			$this->SetXY(95,$y);$this->MultiCell(35,9,"".$tise[$i]["desc"],'0','C');
			
			
			$y=$y+9;
			$y=$this->getY();
			$this->Line(50, $y, 130,$y);
			$this->Line(50, $yini, 50,$y);
			$this->Line(95, $yini, 95,$y);
			$this->Line(130, $yini, 130,$y);
			
			
			
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
$pdf->Publicar($tise);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>


