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
		$this->SetFont('Arial','B',13);
		$this->SetXY(5,$y);$this->MultiCell(200,5,	"LISTADO DE FICHAS PSICOLOGICA",'0','C');
		
	}		
	function Publicar($psicologica){
		
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		
//CABECERA
		$y=$y+3;
		$this->SetFont('Arial','B',11);
		$this->SetXY(5,$y);$this->MultiCell(70,9,"Paciente",'1','L');
		$this->SetXY(75,$y);$this->MultiCell(31,9,"Historia Clinica",'1','L');
		$this->SetXY(106,$y);$this->MultiCell(90,9,"Motivo de la Consulta",'1','L');
		
		
//CUERPO
		$y=$y+9;
		$yini= $y;
		$this->SetFont('Arial','',11);
			for($i = 0;$i<count($psicologica);$i++){
				
				
				$this->SetXY(75,$y);$this->MultiCell(31,9,$psicologica[$i]["clin"],'0','C');
				$this->SetXY(106,$y);$this->MultiCell(90,9,$psicologica[$i]['moti'],'0','L');
				$this->SetXY(5,$y);$this->MultiCell(70,9,$psicologica[$i]["paciente"]["paciente"]["appat"]." ".$psicologica[$i]["paciente"]["paciente"]["apmat"].','.$psicologica[$i]["paciente"]["paciente"]["nomb"],'0','L');				
				
				$y=$this->getY();
				$this->Line(5, $y, 196,$y);

			}
			$this->Line(5, $yini, 5,$y);
			$this->Line(75, $yini, 75,$y);
			$this->Line(106, $yini, 106,$y);
			$this->Line(196, $yini, 196,$y);
			
			
			


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