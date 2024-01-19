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
		$this->SetXY(230,5);$this->MultiCell(60,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','C');
		$this->SetFont('Arial','',9);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Centro de Salud Mental",'0','C');
		$this->SetXY(10,20);$this->MultiCell(60,5,"'Moises Heresi'",'0','C');
		$this->SetFont('Arial','B',12);
		$this->SetXY(110,30);$this->MultiCell(80,5,	"HOSPITALIZACIONES COMPLETAS",'1','C');
	
	}	
	function Publicar($completa){

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
		$this->SetFont('Arial','B',10);
		$yini = $y;
		$y= $y+20;
		$this->SetY($y);
		if(count($completa)>0){
			foreach($completa as $i=>$item){
				if($this->GetY()>255){
					$this->AddPage();
					$y=56;
				}
				$this->SetXY(15,$y);$this->MultiCell(10,5,$categorias[$item['categoria']],'0','C');
			}
		}
				

		

}

	
	 
}

$pdf=new repo('L','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("completa");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($completa);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>