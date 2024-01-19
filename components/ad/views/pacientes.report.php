<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante.gif',10,10,190,275);	
		$y=10;
		$this->SetFont('Arial','B',9);
		//$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');		
		//$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','R');
		//$this->SetFont('Arial','',9);
		//$this->SetXY(10,15);$this->MultiCell(60,5,"Centro de Salud Mental",'0','C');
		//$this->SetXY(10,20);$this->MultiCell(60,5,"'Moises Heresi'",'0','C');
		//$this->SetFont('Arial','B',10);
		$this->SetXY(5,15);$this->MultiCell(200,5,	"LISTA DE PACIENTES 2012",'0','C');
		
	}	
	function Publicar($diario){
		
		$sexos= array(
			"1"=>"MASCULINO",
			"0"=>"FEMENINO"
		);
		$categorias= array(
			"1"=>"S/E",
			"2"=>"PP",
			"3"=>"P",
			"4"=>"A",
			"5"=>"B",
			"6"=>"C",
			"7"=>"E",
			"8"=>"D"

		);
		
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		//$y=$y+25;
		$this->SetFont('Arial','B',8);


		$this->SetXY(20,$y);$this->MultiCell(42,9,"H.C",'1','C');
		$this->SetXY(62,$y);$this->MultiCell(60,9,"PACIENTE",'1','C');
		$this->SetFont('Arial','B',7);
		$this->SetXY(122,$y);$this->MultiCell(30,9,"PROCEDENCIA",'1','C');
		//$this->SetXY(152,$y);$this->MultiCell(30,9,"EDAD",'1','C');
		
		$masculino = 0;
		$femenino = 0;
		$totalpaciente = 0;
		$edad1_p = 0;
		$edad2_p = 0;
		$edad3_p = 0;
		$edad4_p = 0;
		$edad5_p = 0;
		$edad6_p = 0;
		$edad7_p = 0;
		$edad8_p = 0;



		$this->SetFont('Arial','',9);
		$y=$y+9;
		$yini= $y;
		for($i = 0;$i<count($diario);$i++){
			for($j = 0;$j<count($diario[$i]['consulta']);$j++){
				
					if($y>200){
						$this->AddPage();
						$y = $yini;
						$this->SetFont('Arial','B',7);
						
						
					}
				//if($diario[$i]["consulta"][$j]["cate"]== 8){
				$this->SetXY(20,$y);$this->MultiCell(42,9,"".$diario[$i]["consulta"][$j]["paciente"]["his_cli"],'1','C');

				$this->SetFont('Arial','',7);

				$this->SetXY(62,$y);$this->MultiCell(60,9,"".$diario[$i]["consulta"][$j]["paciente"]["paciente"]["appat"]. ' '.$diario[$i]["consulta"][$j]["paciente"]["paciente"]["apmat"].' '.$diario[$i]["consulta"][$j]["paciente"]["paciente"]["nomb"],'1','C');				

				$this->SetXY(122,$y);$this->MultiCell(60,9,"".$diario[$i]["consulta"][$j]["paciente"]["domicilios"][0]["direccion"],'1','C');
				$y=$y+9;
					//}
				}
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
