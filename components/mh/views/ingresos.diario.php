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
		$this->SetXY(0,30);$this->MultiCell(210,5,	"LISTA DE INGRESOS DEL CENTRO DE SALUD MENTAL 'MOISES HERESI' ",'0','C');
		
	}	
	function Publicar($ingreso){

		$x=5;
		$y=36;
		$y_ini = $y;
		$page_b = 275;
		$yini = $y;
		$y=$y+10;		
		$this->SetFont('Arial','B',12);
		$this->SetXY(0,$y);$this->MultiCell(50,5,"SALUD MENTAL",'0','C');
		$y=$y+5;
		$this->SetFont('Arial','',9);
		if($ingreso[0]['modulo'] == 'MH'){
			$this->SetXY(0,$y);$this->MultiCell(100,5,"Número de Recibo de Ingresos: ".$ingreso[0]['cod'],'0','C');	
			$y=$y+10;
			if($ingreso[0]['detalle'])
			$this->SetFont('Arial','B',9);
			$this->SetXY(0,$y);$this->MultiCell(100,5,"Certificado Medico: ".$ingreso[0]['modulo'],'0','C');	

		}
		$y=$y+10;


		/*
			for($i = 0; $i<count($ingreso);$i++){
				for($j = 0;$j<count($ingreso[$i]['detalle']);$j++){
				

				}
			
		}
		*/
		
		
	} 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($ingreso);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>
