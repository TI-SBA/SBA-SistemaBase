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
		$this->SetXY(10,15);$this->MultiCell(60,5,"Archivo Central",'0','C');
		//$this->SetXY(10,20);$this->MultiCell(60,5,"Central",'0','C');
		$this->SetFont('Arial','B',16);
		$this->SetXY(5,25);$this->MultiCell(200,5,"RECEPCION DOCUMENTARIA",'0','C');
		$y=$y+20;
		
	
		$y=$y+5;
		
		$y=$y+5;
	}

	



	function Publicar($redo){
		
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$this->SetFont('Arial','B',10);
		$this->SetXY(145,43);$this->MultiCell(60,8,"Nro. de Entrega: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(178,43);$this->MultiCell(60,8,"".$redo["nro"],'0','L');
		$y=$y+30;
		$this->SetFont('Arial','B',10);
		$this->SetXY(1,$y);$this->MultiCell(45,8,"Fecha: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(46,$y);$this->MultiCell(33,8,"".date('d/m/Y',$redo["fecreg"]->sec),'0','L');
		$y=$y+15;
		$this->SetFont('Arial','B',10);
		$this->SetXY(1,$y);$this->MultiCell(45,8,"Nombre del Remitente: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(46,$y);$this->MultiCell(60,8,"".$redo["remi"],'0','L');
		$y=$y+15;
		$this->SetFont('Arial','B',10);
		$this->SetXY(1,$y);$this->MultiCell(45,8,"Programa u Oficina: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(46,$y);$this->MultiCell(80,8,"".$redo["dire"],'0','L');
		$y=$y+15;
		$this->SetFont('Arial','B',10);
		$this->SetXY(1,$y);$this->MultiCell(45,8,"Documento Entregado: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(46,$y);$this->MultiCell(80,8,"".$redo["titu"],'0','L');
		$y=$y+15;
		$this->SetFont('Arial','B',10);
		$this->SetXY(1,$y);$this->MultiCell(45,8,"Observaciones: ",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(46,$y);$this->MultiCell(80,8,"".$redo["obse"],'0','L');
		$y=$y+100;
		$this->SetFont('Arial','B',11);	
		$this->SetXY(5,$y);$this->MultiCell(55,5,"----------------------------------------",'0','L');
		$y=$y+5;
		$this->SetXY(15,$y);$this->MultiCell(35,5,"FIRMA Y SELLO",'0','L');

		//--------------------------------------\\
		$this->SetFont('Arial','B',11);	
		$this->SetXY(117,215);$this->MultiCell(55,5,"----------------------------------------",'0','L');
		$this->SetXY(123,220);$this->MultiCell(70,5,"ARCHIVO CENTRAL",'0','L');
		$y=$y+40;
		$this->SetXY(1,$y);$this->MultiCell(209,5,"NOTA: La constancia de entrega y devolucion de documentos se encuentra en archivo central.",'0','L');
		
	}
}
$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($redo);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();
?>