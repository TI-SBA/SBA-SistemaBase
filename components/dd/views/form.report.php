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
		$this->SetXY(5,30);$this->MultiCell(200,5,	"LISTA DE FORMATOS DE DOCUMENTOS",'0','C');
		
	}	
	
 		
	function Publicar($form){

		
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$y=$y+20;
		$this->SetFont('Arial','B',8);
		$yini = $y;

			$this->SetXY(60,$y);$this->MultiCell(35,9,"Formato",'1','C');
			$this->SetXY(95,$y);$this->MultiCell(35,9,"Descripcion",'1','C');
			$this->SetXY(130,$y);$this->MultiCell(33,9,"Fecha de Registro",'1','C');
			
		$y=$y+9;
		for($i=0;$i<count($form);$i++){
			$this->SetFont('Arial','',7);
			$this->SetXY(60,$y);$this->MultiCell(35,9,"".$form[$i]["nomb"],'1','C');
			$this->SetXY(95,$y);$this->MultiCell(35,9,"".$form[$i]["desc"],'1','C');
			$this->SetXY(130,$y);$this->MultiCell(33,9,"".date('d/m/Y',$form[$i]["fecreg"]->sec),'1','C');
			
			
			$y=$y+9;
			$y=$this->getY();
			$this->Line(60, $y, 163,$y);
			$this->Line(60, $y, 60,$y);
			$this->Line(95, $yini, 95,$y);
			$this->Line(130, $yini, 130,$y);
			$this->Line(163, $yini, 163,$y);
			
			
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
$pdf->Publicar($form);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>


