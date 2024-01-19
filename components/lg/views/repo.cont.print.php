<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $prod_nom;
	var $prod_cod;
	var $prod_umed;
	function Filtros($filtros){
		$this->prod_nom = $filtros[0]["producto"]["nomb"];
		$this->prod_cod = $filtros[0]["producto"]["cod"];
		$this->prod_umed = $filtros[0]["producto"]["unidad"]["nomb"];
	}
	function Header(){
		$meses = array("Todos","ENE","FEB","MAR","ABR","MAY","JUN","JUL","AGO","SET","OCT","NOV","DIC");
		$x=0;
		$y=10;
		//$this->Rect(255, $y, 25, 10);$this->setXY(60,$y);$this->MultiCell(30,5,"20120958136",'1','C');
		$this->Rect(10, $y, 130, 190);
		$this->SetFont('Arial','B',8);
		$this->setXY(10,$y);$this->MultiCell(80,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREUIPA",'0','C');
		$this->SetFont('Arial','B',6);
		$y=$y+5;
		$this->setXY(10,$y);$this->MultiCell(80,5,"PIEROLA 201 - TELEFONO 213371",'0','C');
		$y=$y+5;
		$this->SetFont('Arial','B',12);
		$this->setXY(10,$y);$this->MultiCell(130,5,"CONTROL VISIBLE DE ALMACEN Nº _______",'0','C');
		$y=$y+10;
		$this->SetFont('Arial','B',10);
		$this->setXY(10,$y);$this->MultiCell(130,5,"ARTICULO: ".$this->prod_nom,'0','L');
		$y=$y+5;
		$this->setXY(10,$y);$this->MultiCell(130,5,"CODIGO: ".$this->prod_cod,'0','L');
		$y=$y+5;
		$this->setXY(10,$y);$this->MultiCell(130,5,"UNIDAD DE MEDIDA: ".$this->prod_umed,'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',8);
		$this->Rect(10, $y, 15, 10);$this->setXY(10,$y);$this->MultiCell(15,5,"1\nFECHA",'0','C');
		$this->Rect(25, $y, 40, 5);$this->setXY(25,$y);$this->MultiCell(40,2.5,"2\nCOMPROBANTE",'0','C');
		$this->Rect(25, $y+5, 20, 5);$this->setXY(25,$y+5);$this->MultiCell(20,2.5,"a\nCLASE",'0','C');
		$this->Rect(45, $y+5, 20, 5);$this->setXY(45,$y+5);$this->MultiCell(20,2.5,"b\nNº",'0','C');
		$this->Rect(65, $y, 75, 5);$this->setXY(65,$y);$this->MultiCell(75,2.5,"3\nMOVIMIENTO",'0','C');
		$this->Rect(65, $y+5, 25, 5);$this->setXY(65,$y+5);$this->MultiCell(25,2.5,"a\nENTRADA",'0','C');
		$this->Rect(90, $y+5, 25, 5);$this->setXY(90,$y+5);$this->MultiCell(25,2.5,"b\nSALIDA",'0','C');
		$this->Rect(115, $y+5, 25, 5);$this->setXY(115,$y+5);$this->MultiCell(25,2.5,"c\nSALDO",'0','C');
	}		
	function Publicar($items,$params){
		$y=55;
		$this->SetFont('Arial','',10);
		foreach($items as $item){
			if($y>185){
				$this->AddPage();
				$y=55;
			}
			$this->Rect(10, $y, 15, 5);$this->setXY(10,$y);$this->MultiCell(15,5,Date::format($item["fecreg"]->sec, 'd'),'0','L');
			$this->Rect(25, $y, 20, 5);$this->setXY(25,$y);$this->MultiCell(20,5,$item["documento"]["tipo"],'0','L');
			$this->Rect(45, $y, 20, 5);$this->setXY(45,$y);$this->MultiCell(20,5,$item["documento"]["cod"],'0','L');
			$this->Rect(65, $y, 25, 5);$this->setXY(65,$y);$this->MultiCell(25,5,($item["tipo"]=="E")?$item["cant"]:"",'0','C');
			$this->Rect(90, $y, 25, 5);$this->setXY(90,$y);$this->MultiCell(25,5,($item["tipo"]=="S")?$item["cant"]:"",'0','C');
			$this->Rect(115, $y, 25, 5);
			$y=$y+5;
		}
		$this->Rect(10, $y, 130, 10);$this->setXY(10,$y);$this->MultiCell(130,5,"Inventario Fisico del Almacen \"".$items[0]["almacen"]["nomb"]."\" al periodo ".$params["ano"]." - ".$params["mes"],'0','C');
	}
	function Footer()
	{
		$this->SetXY(120,-10);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(30,10,'Página: '.$this->PageNo().'/{nb}',0,0,'L');
    	
    	$this->SetXY(15,-10);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,'Fecha de Impresión: '.date("d-m-Y"),0,0,'L');
	} 
	 
}
$pdf=new repo('P','mm','A5');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filtros($items);
$pdf->AddPage();
$pdf->Publicar($items,$params);
$pdf->SetLeftMargin(25);
$pdf->Output();
?>