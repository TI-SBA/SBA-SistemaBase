<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $mes;
	var $ano;
	function Filter($filtros){
		$this->mes = $filtros["mes"];
		$this->ano = $filtros["ano"];
	}
	function Header(){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',14);
		$y=5;
		$this->setXY(15,$y);$this->MultiCell(270,5,"NICHOS EN VIDA, ".$meses[$this->mes]." ".$this->ano,'0','C');
		$y=$y+5;
		$this->SetFont('Arial','B',11);
		$this->Rect(15, $y, 20, 15);$this->setXY(15,$y);$this->MultiCell(20,7.5,"NICHO Nro.",'0','C');
		$this->Rect(35, $y, 20, 15);$this->setXY(35,$y);$this->MultiCell(20,15,"FILA",'0','C');
		$this->Rect(55, $y, 35, 15);$this->setXY(55,$y);$this->MultiCell(35,15,"PABELLON",'0','C');
		$this->Rect(90, $y, 90, 15);$this->setXY(90,$y);$this->MultiCell(90,15,"NOMBRES",'0','C');
		$this->Rect(180, $y, 45, 10);$this->setXY(180,$y);$this->MultiCell(45,10,"FECHA DE FIRO",'0','C');
		$this->Rect(180, $y+10, 15, 5);$this->setXY(180,$y+10);$this->MultiCell(15,5,"DIA",'0','C');
		$this->Rect(195, $y+10, 15, 5);$this->setXY(195,$y+10);$this->MultiCell(15,5,"MES",'0','C');
		$this->Rect(210, $y+10, 15, 5);$this->setXY(210,$y+10);$this->MultiCell(15,5,"AÑO",'0','C');
		$this->Rect(225, $y, 30, 15);$this->setXY(225,$y);$this->MultiCell(30,15,"RECIBO NRO",'0','C');
		$this->Rect(255, $y, 25, 15);$this->setXY(255,$y);$this->MultiCell(25,5,"PERMANENTE S/. VALOR",'0','C');
	}		
	function Publicar($items){
		$x=0;
		$y=30;
		$y_ini=$y;
		$page_b=180;
		$alto = 5;
		$this->SetFont('Arial','',9);
		$total = 0;
		foreach($items as $item){
			if($y>$page_b){
				$this->AddPage();
				$y = $y_ini;
			}
			$this->Rect(15, $y, 20, 10);$this->setXY(15,$y);$this->MultiCell(20,5,$item["nicho_num"],'0','C');
			$this->Rect(35, $y, 20, 10);$this->setXY(35,$y);$this->MultiCell(20,5,$item["fila"],'0','C');
			$this->Rect(55, $y, 35, 10);$this->setXY(55,$y);$this->MultiCell(35,5,$item["pabellon"],'0','C');
			$this->Rect(90, $y, 90, 10);$this->setXY(90,$y);$this->MultiCell(90,5,"Paga:".$item["paga"]."\nPara:".$item["para"],'0','L');
			$this->Rect(180, $y, 15, 10);$this->setXY(180,$y);$this->MultiCell(15,5,Date::format($item["fecha"]->sec, 'd'),'0','C');
			$this->Rect(195, $y, 15, 10);$this->setXY(195,$y);$this->MultiCell(15,5,Date::format($item["fecha"]->sec, 'm'),'0','C');
			$this->Rect(210, $y, 15, 10);$this->setXY(210,$y);$this->MultiCell(15,5,Date::format($item["fecha"]->sec, 'Y'),'0','C');
			$this->Rect(225, $y, 30, 10);$this->setXY(225,$y);$this->MultiCell(30,5,$item["serie"]."-".$item["num"],'0','C');
			$this->Rect(255, $y, 25, 10);$this->setXY(255,$y);$this->MultiCell(25,5,number_format($item["valor"],2,".", ","),'0','C');
			$y=$y+10;
		}
		$this->setXY(210,$y);$this->MultiCell(30,5,"TOTAL",'0','L');
		$this->Rect(255, $y, 25, 5);$this->setXY(255,$y);$this->MultiCell(25,5,number_format($total,2,".", ","),'0','C');
	}
	function Footer()
	{
		
	} 
	 
}

$pdf=new repo('L','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filter($filtros);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>
