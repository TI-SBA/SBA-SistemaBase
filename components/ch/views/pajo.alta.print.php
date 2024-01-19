<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{

	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante.gif',10,10,190,275);	
		$y=10;
		$this->SetFont('Arial','B',7);
		$this->SetXY(2,4);$this->MultiCell(50,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');		
		$this->SetXY(2,4);$this->MultiCell(145,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(2,13);$this->MultiCell(50,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(2,18);$this->MultiCell(50,5,"'Moises Heresi'",'0','C');
		$this->SetFont('Arial','B',11);
		$this->SetXY(1,25);$this->MultiCell(146,5,"PAPELETA DE ALTA",'0','C');
		$y=$y+10;
		
	
		$y=$y+5;
		
		$y=$y+5;
	}

	function Publicar($paciente){
		
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$tipo= array(
			"C"=>"COMPLETA",
			"P"=>"PARCIAL"
			
		);
		/*-------------------------------------------*/
		$this->SetFont('Arial','B',7);
		$y=$y+20;
		$this->SetXY(10,$y);$this->MultiCell(15,7,"Paciente: ",'0','L');
		$this->SetFont('Arial','',7);
		$this->SetXY(25,$y);$this->MultiCell(121,7,"".$paciente["paciente"]['appat'] . ' '. $paciente["paciente"]['apmat'] .','. $paciente["paciente"]['nomb'],'0','L');
		$y=$y+10;
		$this->SetFont('Arial','B',7);
		$this->SetXY(10,$y);$this->MultiCell(35,7,"Historia Clinica: ",'0','L');
		$this->SetFont('Arial','',7);
		$this->SetXY(45,$y);$this->MultiCell(106,7,"".$paciente['hist_cli'],'0','L');
		$y=$y+10;
		$this->SetFont('Arial','B',7);
		$this->SetXY(10,$y);$this->MultiCell(35,7,"Tipo de Hospitalizacion: ",'0','L');
		$this->SetFont('Arial','',7);
		$this->SetXY(45,$y);$this->MultiCell(106,7,"".$tipo[$paciente['tipo_hosp']],'0','L');
		$y=$y+10;
		$this->SetFont('Arial','B',7);
		$this->SetXY(10,$y);$this->MultiCell(35,7,"Tipo de Alta: ",'0','L');
		$this->SetFont('Arial','',7);
		$this->SetXY(45,$y);$this->MultiCell(40,7,"".$paciente['talta'],'0','L');
		$y=$y+10;
		$this->SetFont('Arial','B',7);
		$this->SetXY(10,$y);$this->MultiCell(35,7,"Fecha de Ingreso: ",'0','L');
		$this->SetFont('Arial','',7);
		$this->SetXY(45,$y);$this->MultiCell(40,7,"".date('d-m-Y',$paciente["fec_inicio"]->sec),'0','L');
		$y=$y+10;
		$this->SetFont('Arial','B',7);
		$this->SetXY(10,$y);$this->MultiCell(35,7,"Fecha de Alta: ",'0','L');
		$this->SetFont('Arial','',7);
		$this->SetXY(45,$y);$this->MultiCell(40,7,"".date('d-m-Y',$paciente["fec_alta"]->sec),'0','L');
		$y=$y+10;
		$this->SetFont('Arial','B',7);
		$this->SetXY(10,$y);$this->MultiCell(35,7,"Dias de Hospitalizacion: ",'0','L');
		$this->SetFont('Arial','',7);
		$this->SetXY(45,$y);$this->MultiCell(40,7,"".$paciente['cant'],'0','L');
		$y=$y+10;
		$this->SetFont('Arial','B',7);
		$this->SetXY(10,$y);$this->MultiCell(35,7,"Diagnostico: ",'0','L');
		$this->SetFont('Arial','',7);
		$this->SetXY(45,$y);$this->MultiCell(100,3,"".$paciente['cie10'] . ' - '. $paciente['diag'],'0','L');
		$y=$y+10;
		$this->SetFont('Arial','B',7);
		$this->SetXY(10,$y);$this->MultiCell(35,7,"Medico Tratante: ",'0','L');
		$this->SetFont('Arial','',7);
		$this->SetXY(45,$y);$this->MultiCell(101,7,"".$paciente["autorizado"]['appat'] . ' '. $paciente["autorizado"]['apmat'] .','. $paciente["autorizado"]['nomb'],'0','L');
		$y=$y+10;
		$this->SetFont('Arial','B',7);
		$this->SetXY(10,$y);$this->MultiCell(35,7,"Hecho Por: ",'0','L');
		$this->SetFont('Arial','',7);
		$this->SetXY(45,$y);$this->MultiCell(101,7,"".$paciente["autor"]['appat'] . ' '. $paciente["autor"]['apmat'] .','. $paciente["autor"]['nomb'],'0','L');
		$y=$y+10;
		$this->SetFont('Arial','B',7);
		$this->SetXY(10,$y);$this->MultiCell(35,7,"Autorizado Por: ",'0','L');
		$this->SetFont('Arial','',7);
		$this->SetXY(45,$y);$this->MultiCell(101,7,"".$paciente["autorizado"]['appat'] . ' '. $paciente["autorizado"]['apmat'] .','. $paciente["autorizado"]['nomb'],'0','L');

		
		
	
	}
}
$pdf=new repo('P','mm','A5');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("PAPELETA DE ALTA");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($paciente);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();
?>