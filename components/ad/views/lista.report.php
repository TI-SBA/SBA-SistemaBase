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
		$this->SetXY(5,5);$this->MultiCell(200,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',9);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(10,20);$this->MultiCell(60,5,"'Moises Heresi'",'0','C');
		$this->SetFont('Arial','B',12);
		$this->SetXY(0,30);$this->MultiCell(210,5,	"LISTA PACIENTES HOSPITALIZADOS - ADICCIONES",'0','C');
		
	}	
	function getAge($birthday) {
		  $birth = $birthday;
		  $now = strtotime('now');
		  $age = ($now - $birth) / 31536000;
		  return floor($age);
	}
 	
	function Publicar($diario){
		$tipo= array(
			"C"=>"COMPLETA",
			"P"=>"PARCIAL"
			
		);
		$pabe= array(
			"Intensivo"=>"INTENSIVO",
			"Intermedio"=>"INTERMEDIO"
			
		);
		$y=36;
		$this->SetFont('Arial','B',8);
		$this->SetXY(10,$y);$this->MultiCell(10,5,"NRO.",'1','C');
		$this->SetXY(20,$y);$this->MultiCell(70,5,"PACIENTE",'1','C');
		$this->SetXY(90,$y);$this->MultiCell(30,5,"HISTORIA CLINICA",'1','C');
		$this->SetXY(120,$y);$this->MultiCell(40,5,"TIPO DE HOSPITALIZACION",'1','C');
		$this->SetXY(160,$y);$this->MultiCell(30,5,"PABELLON",'1','C');
		$y=$y+5;	
		for($i = 0; $i<count($diario);$i++){
			if($this->GetY()>255){
					$this->AddPage();
					$y=56;
				}
		$this->SetFont('Arial','',8);
		$this->SetXY(10,$y);$this->MultiCell(10,5,"".$i+1,'1','C');
		$this->SetXY(20,$y);$this->MultiCell(70,5,"".$diario[$i]['paciente']['appat']. ' '.$diario[$i]['paciente']['apmat']. ' '.$diario[$i]['paciente']['nomb'],'1','L');
		$this->SetXY(90,$y);$this->MultiCell(30,5,"".$diario[$i]['hist_cli'],'1','C');
		$this->SetXY(120,$y);$this->MultiCell(40,5,"".$tipo[$diario[$i]['tipo_hosp']],'1','C');
		$this->SetXY(160,$y);$this->MultiCell(30,5,"".$pabe[$diario[$i]['pabellon']],'1','C');
		
		$y=$y+5;
		
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
$pdf->Publicar($diario);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>
