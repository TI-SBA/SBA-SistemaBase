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
		$this->SetXY(10,15);$this->MultiCell(60,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(10,20);$this->MultiCell(60,5,"'Moises Heresi'",'0','C');
		$this->SetFont('Arial','B',10);
		//$this->SetXY(5,30);$this->MultiCell(200,5,	"LISTA DE DIAGNOSTICOS MEDICOS AÑO 2016",'0','C');
		
		
	}	




	function Publicar($diario){
		
		$sexos= array(
			"1"=>"MASCULINO",
			"0"=>"FEMENINO"
		);
		
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		//$y=$y+25;
		$this->SetFont('Arial','B',8);

		
		//$this->SetXY(65,$y);$this->MultiCell(70,9,"LISTA DE DIAGNOSTICOS MEDICOS AÑO 2016",'1','C');
		$y = $y+9;
		//$this->SetXY(62,$y);$this->MultiCell(50,9,"DIAGOSTICO MEDICO",'1','C');
		$y = $y+9;
			$this->SetFont('Arial','B',4);
			for($i = 0;$i<count($diario);$i++){
				for($j = 0;$j<count($diario[$i]['consulta']);$j++){
					if($y>200){
						$this->AddPage();
						$y = $yini;
						$this->SetFont('Arial','B',4);
						$y= $y+4;
						//$this->SetXY(62,$y);$this->MultiCell(60,9,"PACIENTE",'1','C');
						$y= $y+4;
						
					}
						$this->SetXY(62,$y);$this->MultiCell(50,4,"".$diario[$i]["consulta"][$j]["cie10"],'1','C');
						$this->SetXY(112,$y);$this->MultiCell(50,4,"".$diario[$i]["consulta"][$j]["his_cli"],'1','C');
						$y=$y+4;				

				}

			}
			$y+=4;
	
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
