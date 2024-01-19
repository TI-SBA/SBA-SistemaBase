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
		$this->SetXY(80,35);$this->MultiCell(55,5,"LISTA DE RECIBOS DEL DIA:",'0','C');
		
	}	
	function Publicar($hospi){
		
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
		$this->SetXY(135,35);$this->MultiCell(22,5,"".date('d-m-Y',$hospi[0]["fecpag"]->sec),'0','C');
		$x=5;
		$y=25;
		$y_ini = $y;
		$y= $y+30;
		$total = 0;
		$suma = 0;
		$page_b = 275;
		$this->SetFont('Arial','B',8);
		
		$this->SetFont('Arial','B',8);
		$this->SetXY(30,$y);$this->MultiCell(25,9,"CATEGORIA",'1','C');
		$this->SetXY(55,$y);$this->MultiCell(25,9,"Nro. Serie",'1','C');
		$this->SetXY(80,$y);$this->MultiCell(35,9,"Nro. Correlativo",'1','C');		
		$this->SetXY(115,$y);$this->MultiCell(60,9,"APELLIDOS Y NOMBRES",'1','C');
		$this->SetXY(175,$y);$this->MultiCell(25,9,"IMPORTE",'1','C');		
		
		$this->SetFont('Arial','',7);

		for($i = 0; $i<count($hospi);$i++){

			$y= $y+9;	
			$this->SetXY(30,$y);$this->MultiCell(25,9,$categorias["".$hospi[$i]["categoria"]],'1','C');
			$this->SetXY(55,$y);$this->MultiCell(25,9,"".$hospi[$i]["recibo"]["serie"],'1','C');
			$this->SetXY(80,$y);$this->MultiCell(35,9,"".$hospi[$i]["recibo"]["num"],'1','C');
			$this->SetXY(115,$y);$this->MultiCell(60,9,"".$hospi[$i]["paciente"]["appat"].' '.$hospi[$i]["paciente"]["apmat"].' '.$hospi[$i]["paciente"]["nomb"],'1','C');	
			$this->SetXY(175,$y);$this->MultiCell(25,9,"".$hospi[$i]["importe"],'1','C');

				
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
$pdf->Publicar($hospi);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>
