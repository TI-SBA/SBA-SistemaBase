<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	
	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante.gif',10,10,190,275);	
		$y=10;
		$this->SetFont('Arial','B',6);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');		
		$this->SetXY(133,5);$this->MultiCell(44,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','C');
		$this->SetFont('Arial','',6);
		$this->SetXY(10,8);$this->MultiCell(60,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(10,11);$this->MultiCell(60,5,"'Moises Heresi'",'0','C');
		$this->SetFont('Times','B',13);
		$this->SetXY(60,19);$this->MultiCell(58,5,"TARJETA DE ADMISION",'0','C');
		$y=$y+10;
		
	
		$y=$y+5;
		
		$y=$y+5;
	}
	function getAge($birthday) {
		  $birth = $birthday;
		  $now = strtotime('now');
		  $age = ($now - $birth) / 31536000;
		  return floor($age);
	}
	function Publicar($paciente){
			

		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$edad_actual =$this->getAge($paciente['fecha_na']->sec);
		$y=$y+15;
		$this->SetFont('Arial','B',7);
		$this->SetXY(40,$y);$this->MultiCell(20,8,"HC",'1','C'); 
		$this->SetXY(60,$y);$this->MultiCell(20,8,"NOMBRE",'1','C'); 
		$this->SetXY(80,$y);$this->MultiCell(20,8,"EDAD",'1','C'); 
		$this->SetXY(100,$y);$this->MultiCell(40,8,"PROCEDENCIA",'1','C'); 
		
		$this->SetFont('Arial','',7);
		
		for($i = 0;$i<count($diario["consulta"]);$i++){
				$this->SetXY(40,$y);$this->MultiCell(20,8,"HC",'1','C'); 
				$this->SetXY(60,$y);$this->MultiCell(20,8,"NOMBRE",'1','C'); 
				$this->SetXY(80,$y);$this->MultiCell(20,8,"EDAD",'1','C'); 
				$this->SetXY(100,$y);$this->MultiCell(40,8,"PROCEDENCIA",'1','C'); 

					if($y>200){
				$this->AddPage();
				$y = $yini;
			
				$y+=9;
			}
				
				$this->SetXY(24,$y);$this->MultiCell(95,8,"".$paciente["paciente"]["appat"].' '.$paciente["paciente"]["apmat"].' '.$paciente["paciente"]["nomb"],'0','L');
				$this->SetXY(24,$y);$this->MultiCell(95,8,"".$paciente["his_cli"],'0','L');  
				$this->SetXY(60,$y);$this->MultiCell(15,8,"".$edad_actual,'0','L');
		}


	
	}
}
//$pdf=new repo('L','mm','A4');
$pdf=new repo('P','mm','A4');
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