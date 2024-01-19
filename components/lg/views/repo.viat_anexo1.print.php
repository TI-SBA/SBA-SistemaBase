<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	function Header(){
		
	}		
	function Publicar($items){
		$this->SetFont('Arial','B',14);
		$this->SetXY(10,10);$this->MultiCell(190,5,"ANEXO Nº 1",'0','C');
		$this->SetFont('Arial','B',11);
		$this->SetXY(10,20);$this->MultiCell(190,5,"SOLICITUD DE AUTORIZACIÓN DE VIAJE EN COMISIÓN DE SERVICIO",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." - Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Logistica",'0','C');
		$y = 35;
		$this->SetFont('Arial','B',10);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"DIRIGIDO A",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(55,$y);$this->MultiCell(140,5,$items["destino"]["nomb"]." ".$items["destino"]["appat"]." ".$items["destino"]["apmat"],'0','L');
		$y=$this->GetY()+5;
		$this->SetFont('Arial','B',10);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"DE",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(55,$y);$this->MultiCell(140,5,$items["origen"]["nomb"]." ".$items["origen"]["appat"]." ".$items["origen"]["apmat"],'0','L');
		$y=$this->GetY()+5;
		$this->SetFont('Arial','B',10);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"ASUNTO",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(55,$y);$this->MultiCell(140,5,$items["asunto"],'0','L');
		$y=$this->GetY()+5;
		$this->SetFont('Arial','B',10);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"FECHA",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(55,$y);$this->MultiCell(140,5,$items["fec"],'0','L');
		$y=$this->GetY()+10;
		$this->SetXY(15,$y);$this->MultiCell(180,5,$items["descr"],'0','L');
		$y=$this->GetY()+10;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"Atentamente,",'0','C');
		$y+=30;
		$this->SetXY(15,$y);$this->MultiCell(90,5,"----------------------------------------------\nFirma del Solicitante",'0','C');
		$this->SetXY(105,$y);$this->MultiCell(90,5,"----------------------------------------------\nFirma y Sello del Gerente General",'0','C');
	}
	function Footer()
	{
	} 
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>