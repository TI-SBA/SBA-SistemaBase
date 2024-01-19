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
		$this->SetXY(5,$y);$this->MultiCell(200,5,	"LISTADO DE FICHAS PSIQUITRICA",'0','C');
		
	}		
	function Publicar($psiquiatrica){
		
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		
//CABECERA
		$y=$y+3;
		$this->SetFont('Arial','B',11);
		$this->SetXY(5,$y);$this->MultiCell(60,9,"Paciente",'1','L');
		$this->SetXY(65,$y);$this->MultiCell(45,9,"Motivo Consulta",'1','L');
		$this->SetXY(110,$y);$this->MultiCell(45,9,"Diagnostico",'1','L'); 
		$this->SetXY(155,$y);$this->MultiCell(45,9,"Descripcion",'1','L');
		
		
//CUERPO
		$y=$y+9;
		$yini= $y;
		$this->SetFont('Arial','',11);
			for($i = 0;$i<count($psiquiatrica);$i++){
				
				
				$this->SetXY(65,$y);$this->MultiCell(45,9,$psiquiatrica[$i]['moti'],'0','L');
				$this->SetXY(110,$y);$this->MultiCell(30,9,$psiquiatrica[$i]['diag'],'0','L');
				$this->SetXY(155,$y);$this->MultiCell(45,9,$psiquiatrica[$i]['desc'],'0','L');
				$this->SetXY(5,$y);$this->MultiCell(60,9,$psiquiatrica[$i]["paciente"]["paciente"]["appat"]." ".$psiquiatrica[$i]["paciente"]["paciente"]["apmat"].','.$psiquiatrica[$i]["paciente"]["paciente"]["nomb"],'0','L');	
				
				$y=$this->getY();
				$this->Line(5, $y, 200,$y);

			}
			$this->Line(5, $yini, 5,$y);
			$this->Line(65, $yini, 65,$y);
			$this->Line(110, $yini, 110,$y);
			$this->Line(155, $yini, 155,$y);
			$this->Line(200, $yini, 200,$y);
			
			


}

	
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($psiquiatrica);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>