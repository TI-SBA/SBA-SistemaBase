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
		$this->SetFont('Arial','B',11);
		$this->SetXY(5,35);$this->MultiCell(200,5,	"Archivo Solicitado",'0','C');
		
	}	
	
 	
	function Publicar($pear){
		
		//$total_consultas = 0;
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$y=$y+12;
		
		//CABECERAS
		$yini = $y;
		$y = $y+15;
		$this->SetFont('Arial','B',11);
		$this->SetXY(5,$y);$this->MultiCell(100,5,"DATOS DEL DOCUMENTO SOLICITADO: ",'0','L');
		$y = $y+15;
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(40,5,"Numero de Documento: ",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(45,$y);$this->MultiCell(20,5,"".$pear['ndoc'],'0','L');
		$y = $y+15;
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(40,5,"Nombre del Documento: ",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(45,$y);$this->MultiCell(50,5,"".$pear['docu'],'0','L');
		$y = $y+15;
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(40,5,"Direccion: ",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(45,$y);$this->MultiCell(100,5,"".$pear['dire'],'0','L');
		$y = $y+15;
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(40,5,"Oficina: ",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(45,$y);$this->MultiCell(100,5,"".$pear['ofic'],'0','L');
		$y = $y+15;
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(40,5,"Tipo de Documento: ",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(45,$y);$this->MultiCell(100,5,"".$pear['tipo'],'0','L');	
		$y = $y+15;
		$this->SetFont('Arial','B',11);
		$this->SetXY(5,$y);$this->MultiCell(100,5,"DATOS DEL SOLICITANTE: ",'0','L


			');
		$y = $y+15;
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(40,5,"Numero de Pedido: ",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(45,$y);$this->MultiCell(100,5,"".$pear['nsol'],'0','L');	
		$y = $y+15;
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(40,5,"Direccion Solicitante: ",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(45,$y);$this->MultiCell(100,5,"".$pear['disol'],'0','L');
		$y = $y+15;
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(40,5,"Oficina Solicitante: ",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(45,$y);$this->MultiCell(100,5,"".$pear['ofsol'],'0','L');
		$y = $y+15;
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(40,5,"Fecha de Pedido: ",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(45,$y);$this->MultiCell(100,5,"".date('d/m/Y',$pear['fecreg']->sec),'0','L');


					


			

	}
	
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($pear);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>


