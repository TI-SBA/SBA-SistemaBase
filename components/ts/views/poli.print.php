<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante.gif',10,10,190,275);	
	}		
	function Publicar($items){
		$monedas = array(
			"S"=>array("simb"=>"S/.","nomb"=>"NUEVO SOL","plu"=>"NUEVOS SOLES"),
			"D"=>array("simb"=>"USSD $.","nomb"=>"DOLAR","plu"=>"DOLARES")
		);
		$x=0;
		$y=15;
		$alto = 5;
		$this->SetFont('Arial','B',15);
		$this->setXY(15,$y);$this->MultiCell(180,5,"POLIZA CONTABLE DE FONDOS Nº  ".$items["cod"],'0','C');
		$this->SetFont('Arial','B',10);
		$y=$y+10;
		$this->SetXY(150,$y);$this->MultiCell(15,5,"DÍA",'1','C');
		$this->SetXY(165,$y);$this->MultiCell(15,5,"MES",'1','C');
		$this->SetXY(180,$y);$this->MultiCell(15,5,"AÑO",'1','C');
		$y=$y+5;
		$this->SetXY(150,$y);$this->MultiCell(15,5,Date::format($items["fecreg"]->sec, 'd'),'1','C');
		$this->SetXY(165,$y);$this->MultiCell(15,5,Date::format($items["fecreg"]->sec, 'm'),'1','C');
		$this->SetXY(180,$y);$this->MultiCell(15,5,Date::format($items["fecreg"]->sec, 'Y'),'1','C');
		$y=$y+10;
		$this->setXY(15,$y);$this->MultiCell(180,5,"NOMBRE: SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','L');
		$y=$y+10;
		$this->setXY(15,$y);$this->MultiCell(180,5,"CONCEPTO",'1','C');
		$y=$y+5;
		$this->Rect(15, $y, 180, 120);
		$y=$y+5;
		$this->SetFont('Arial','',10);
		$this->setXY(20,$y);$this->MultiCell(175,5,$items["descr"],'0','L');
		$y=175;
		$this->SetFont('Arial','B',10);
		$this->setXY(15,$y);$this->MultiCell(180,5,"PROGRAMA Nº ".$items["organizacion"]["cod"],'1','L');
		$y=$y+5;
		$this->setXY(15,$y);$this->MultiCell(180,5,"CONTABILIDAD PATRIMONIAL",'1','C');
		$y=$y+5;
		$this->setXY(15,$y);$this->MultiCell(90,5,"DEBE",'1','C');
		$this->setXY(105,$y);$this->MultiCell(90,5,"HABER",'1','C');
		$y=$y+5;
		$this->setXY(15,$y);$this->MultiCell(45,5,"CUENTA",'1','C');
		$this->setXY(60,$y);$this->MultiCell(45,5,"IMPORTE",'1','C');
		$this->setXY(105,$y);$this->MultiCell(45,5,"CUENTA",'1','C');
		$this->setXY(150,$y);$this->MultiCell(45,5,"IMPORTE",'1','C');
		$y=$y+5;
		$this->Rect(15, $y, 45, 45);
		$this->Rect(60, $y, 45, 45);
		$this->Rect(105, $y, 45, 45);
		$this->Rect(150, $y, 45, 45);
		$this->SetFont('Arial','',10);
		$y_d=195;
		$y_h=195;
		foreach($items["cont_patrimonial"] as $cont){
			if($cont["tipo"]=="D"){
				$this->setXY(15,$y_h);$this->MultiCell(45,5,$cont["cuenta"]["cod"],'0','C');
				$this->setXY(60,$y_h);$this->MultiCell(45,5,$monedas[$cont["moneda"]]["simb"]." ".number_format($cont["monto"],2,".", ","),'0','C');	
				$y_d+=5;
			}elseif($cont["tipo"]=="H"){
				$this->setXY(105,$y_h);$this->MultiCell(45,5,$cont["cuenta"]["cod"],'0','C');
				$this->setXY(150,$y_h);$this->MultiCell(45,5,$monedas[$cont["moneda"]]["simb"]." ".number_format($cont["monto"],2,".", ","),'0','C');
				$y_h+=5;
			}
		}
		$y=$y+45;
		$this->SetFont('Arial','B',10);
		$this->Rect(15, $y, 180, 5);
		$y=$y+5;
		$this->setXY(15,$y);$this->MultiCell(45,5,"CONTROL INTERNO",'1','C');
		$this->setXY(60,$y);$this->MultiCell(45,5,"HECHO POR",'1','C');
		$this->setXY(105,$y);$this->MultiCell(45,5,"VIZACION",'1','C');
		$this->setXY(150,$y);$this->MultiCell(45,5,"TESORERIA",'1','C');
		$y=$y+5;
		$this->Rect(15, $y, 45, 20);
		$this->Rect(60, $y, 45, 20);
		$this->Rect(105, $y, 45, 20);
		$this->Rect(150, $y, 45, 20);
		$y=$y+20;
		$this->Rect(15, $y, 90, 5);
		$this->setXY(105,$y);$this->MultiCell(45,5,"DPTO. CONTABLE",'1','C');
		$this->Rect(150, $y, 45, 5);
	}
	function Footer()
	{
		
	} 
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>