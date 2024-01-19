<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	function Header(){
		//$this->Image(IndexPath.DS.'templates/td/expd.gif',15,15,347,249);
		$this->SetFont('Arial','B',14);
		$this->SetXY(10,10);$this->MultiCell(357,5,"REPORTE ALTAS",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(357,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Hospitalizaciones",'0','C');
		$this->SetFont('arial','B',10);
		$this->SetXY(15,26);$this->MultiCell(360,10,"CATEGORIA                                                         N°H.C.                                                                                     Apellidos y Nombres                                                                       Hospitalización ",'1','L');//28
		$this->SetXY(15,36);$this->MultiCell(110,10,"",'1','L');//55
		$this->SetXY(125,36);$this->Cell(250,10,'           Del                 Al                        días/meses                          Fec.Alta                                                  TIPO',1,0,'L');
		
	}
	function Publicar($data){
		
		$tipo= array(
			"C"=>"COMPLETA",
			"P"=>"PARCIAL"
			
		);
		$modalidad= array(
			"D"=>"Dias",
			"M"=>"Meses"
			
		);
		$categoria= array(
			"10"=>"Nuevo",
			"11"=>"Continuador",
			"8"=>"Indigente",
			"9"=>"Privado"
			
		);

		$x=5;
		$y=36;
		$y_ini = $y;
		$page_b = 275;
				

		$y=$y+10;
		$this->SetFont('Arial','',10);
		$yini = $y;
		for($i = 0; $i<count($data);$i++){
			if($this->GetY()>255){
					$this->AddPage();
					$y=56;
				}
		$this->SetXY(15,$y);$this->MultiCell(55,10,"".$categoria[$data[$i]['categoria']],'1','L');
		$this->SetXY(70,$y);$this->MultiCell(55,10,"".$data[$i]['hist_cli'],'1','C');
		$this->SetXY(125,$y);$this->MultiCell(145,5,"".$data[$i]['paciente']['appat']. ' '.$data[$i]['paciente']['apmat']. ' '.$data[$i]['paciente']['nomb'],'1','L');
		$this->SetXY(210,$y+5);$this->MultiCell(60,5,Date::format($data[$i]['fec_alta']->sec, 'd/m/Y'),'0','C');
		$this->SetXY(305,$y);$this->MultiCell(70,10,$i,'1','C');
		$y=$y+10;
	}
		



}

	
	 
}

$pdf=new repo('P','mm',array(387,279));
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($data);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>