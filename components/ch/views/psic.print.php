<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $his_Cli;
	var $fe_regi;
	
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
		$this->SetXY(5,$y);$this->MultiCell(200,5,	"INFORME PSICOLOGICO",'0','C');
		$y=$y+10;
		
	
		$y=$y+5;
		
		$y=$y+5;
	}		
	function Publicar($psicologica){
		
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$this->SetFont('Arial','B',11);
		$this->SetXY(120,$y);$this->MultiCell(60,15,"Historia Clinica: ".$psicologica["paciente"]["his_cli"],'0','L');
		$y=$y+5;
		
		$this->SetXY(20,$y);$this->MultiCell(200,15,"Nombre(s): ".$psicologica["paciente"]["paciente"]["appat"].' '.$psicologica["paciente"]["paciente"]["apmat"].','.$psicologica["paciente"]["paciente"]["nomb"],'0','L');
		$y=$y+20;
		$this->SetFont('Arial','B',13);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Motivo de la consulta:",'0','L');
		$this->SetFont('Arial','',11);
		$y=$y+10;
		$this->SetXY(20,$y);$this->MultiCell(150,5,"".$psicologica["moti"],'0','L');
		$y=$y+20;
		$this->SetFont('Arial','B',13);
		$this->SetXY(20,$y);$this->MultiCell(150,5,"Resultados:",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',13);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Organicidad: ",'0','L');
		$this->SetFont('Arial','',11);
		$y=$y+5;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psicologica["orga"],'0','L');
		$y=$y+24;
		$this->SetFont('Arial','B',13);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Inteligencia: ",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',11);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psicologica["inte"],'0','L');
		$y=$y+12;
		$this->SetFont('Arial','B',13);		
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Personalidad: ",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',11);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psicologica["perso"],'0','L');
		$this->SetFont('Arial','B',13);
		$y=$y+60;
		$this->SetXY(30,$y);$this->MultiCell(150,5,"Conclusiones: ",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',11);
		$this->SetXY(30,$y);$this->MultiCell(150,5,"".$psicologica["conclu"],'0','L');
		$y=$y+30;		
	}
	
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($psicologica);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>