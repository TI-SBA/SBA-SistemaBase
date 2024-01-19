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
		$this->SetXY(230,5);$this->MultiCell(44,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','C');
		$this->SetFont('Arial','',9);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(10,20);$this->MultiCell(60,5,"'Moises Heresi'",'0','C');
		$this->SetFont('Arial','B',16);
		$this->SetXY(170,25);$this->MultiCell(80,5,"FICHA DE SALUD PUBLICA",'0','C');
		$y=$y+10;
		
	
		$y=$y+5;
		
		$y=$y+5;
	}
	function Publicar($paciente){
			

		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$this->SetFont('Arial','B',10);
		$this->SetXY(20,43);$this->MultiCell(20,8,"FECHA",'1','C');
		$this->SetXY(40,43);$this->MultiCell(20,8,"OBJETIVO",'1','C');
		$this->SetXY(60,43);$this->MultiCell(85,8,"INFORME",'1','C');
		$this->SetXY(20,43);$this->MultiCell(20,140,"",'1','C'); 
		$this->SetXY(40,43);$this->MultiCell(20,140,"",'1','C'); 
		$this->SetXY(60,43);$this->MultiCell(85,140,"",'1','C'); 
		$this->SetXY(20,190);$this->MultiCell(20,8,"NOMBRE: ",'0','L'); 
		$this->SetFont('Arial','',10);
		$this->SetXY(40,190);$this->MultiCell(95,8,"".$paciente["paciente"]["appat"].' '.$paciente["paciente"]["apmat"].' '.$paciente["paciente"]["nomb"],'0','L'); 
		$this->SetFont('Arial','B',10);
		$this->SetXY(135,190);$this->MultiCell(10,8,"HC: ",'0','L'); 
		$this->SetFont('Arial','',10);
		$this->SetXY(145,190);$this->MultiCell(20,8,"".$paciente["his_cli"],'0','L'); 

		$this->SetFont('Arial','B',10);
		$this->SetXY(150,43);$this->MultiCell(20,8,"NOMBRE: ",'0','L'); 
		$this->SetFont('Arial','',10);
		$this->SetXY(170,43);$this->MultiCell(94,8,"".$paciente["paciente"]["appat"].' '.$paciente["paciente"]["apmat"].' '.$paciente["paciente"]["nomb"],'0','L'); 
		$this->SetFont('Arial','B',10);
		$this->SetXY(264,43);$this->MultiCell(10,8,"HC: ",'0','L'); 
		$this->SetFont('Arial','',10);
		$this->SetXY(274,43);$this->MultiCell(20,8,"".$paciente["his_cli"],'0','L'); 
		$this->SetFont('Arial','B',10);
		$this->SetXY(150,51);$this->MultiCell(24,8,"DIRECCION: ",'0','L'); 	
		$this->SetFont('Arial','',10);
		$this->SetXY(174,51);$this->MultiCell(120,8,"".$paciente["paciente"]['domicilios'][0]['direccion'],'0','L'); 	
		$this->SetFont('Arial','B',10);
		$this->SetXY(150,62);$this->MultiCell(27,8,"APODERADO:",'0','L'); 	
		$this->SetFont('Arial','',10);
		$this->SetXY(177,62);$this->MultiCell(114,8,"".$paciente["apoderado"]["appat"].' '.$paciente["apoderado"]["apmat"].' '.$paciente["apoderado"]["nomb"],'0','L'); 
		$this->SetFont('Arial','B',10);
		$this->SetXY(150,72);$this->MultiCell(30,8,"DIAGNOSTICO: ",'0','L'); 
		$this->SetFont('Arial','B',8);
		$this->SetXY(145,82);$this->MultiCell(14,8,"FECHA",'1','C'); 
		$this->SetXY(159,82);$this->MultiCell(20,8,"OBJETIVO",'1','C'); 
		$this->SetXY(179,82);$this->MultiCell(100,8,"INFORME",'1','C'); 
		$this->SetXY(145,90);$this->MultiCell(14,93,"",'1','C'); 
		$this->SetXY(159,90);$this->MultiCell(20,93,"",'1','C'); 
		$this->SetXY(179,90);$this->MultiCell(100,93,"",'1','C'); 
		



		
	
	}
}
$pdf=new repo('L','mm','A4');
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