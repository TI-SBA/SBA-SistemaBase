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
	
 	
	function Publicar($pedi){
		
		//$total_consultas = 0;
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$y=$y+12;
		
		//CABECERAS
		$yini = $y;
		$y = $y+20;
		$this->SetFont('Arial','B',9);
		$this->SetXY(120,$y);$this->MultiCell(40,5,"Numero de Pedido: ",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(160,$y);$this->MultiCell(10,5,"".$pedi['num'],'0','L');
		$y = $y+10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(40,5,"Fecha: ",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(45,$y);$this->MultiCell(100,5,"".date('d/m/Y',$pedi['fecreg']->sec),'0','L');
		$y = $y+15;
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(40,5,"Nombre de Solicitante: ",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(45,$y);$this->MultiCell(70,5,"".$pedi['autor']['appat'].' '.$pedi['autor']['apmat'].' '.$pedi['autor']['nomb'],'0','L');
		$y = $y+15;
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(40,5,"Documento Requerido: ",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(45,$y);$this->MultiCell(70,5,"".$pedi['nomb'],'0','L');
		$y = $y+15;
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(40,5,"Asunto: ",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(45,$y);$this->MultiCell(70,5,"".$pedi['asun'],'0','L');
		$y=$y+60;
		$this->SetFont('Arial','B',11);	
		$this->SetXY(15,$y);$this->MultiCell(70,5,"----------------------------------------",'0','C');
		$y=$y+5;
		$this->SetXY(15,$y);$this->MultiCell(70,5,"FIRMA Y SELLO",'0','C');
		$y=$y-5;
		$this->SetFont('Arial','B',11);	
		$this->SetXY(120,$y);$this->MultiCell(70,5,"----------------------------------------",'0','C');
		$y=$y+5;
		$this->SetXY(120,$y);$this->MultiCell(70,5,"ARCHIVO CENTRAL",'0','C');
		$this->SetFont('Arial','',9);
		$y=$y+10;
		$this->SetXY(5,$y);$this->MultiCell(200,5,"NOTA: La constancia de entrega y devolucion de documentos se encuentra en archivo central.",'0','L');

		
		


					


			

	}
	
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($pedi);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>


