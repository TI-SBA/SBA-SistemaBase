<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $ano;
	function Filter($filtros){
		$this->ano = $filtros["ano"];
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',12);
		$y=10;
		$this->setXY(15,$y);$this->MultiCell(265,5,"INFORMACION DE LA DAOT CEMENTERIO GENERAL, EJERCICIO ".$this->ano,'1','C');
		$y=$y+5;
		$this->SetFont('Arial','B',11);
		$this->Rect(15, $y, 20, 10);$this->setXY(15,$y);$this->MultiCell(20,5,"Tipo de Persona",'0','C');
		$this->Rect(35, $y, 20, 10);$this->setXY(35,$y);$this->MultiCell(20,10,"Docum",'0','C');
		$this->Rect(55, $y, 35, 10);$this->setXY(55,$y);$this->MultiCell(35,10,"Nro. Documento",'0','C');
		$this->Rect(90, $y, 80, 10);$this->setXY(90,$y);$this->MultiCell(80,10,"Nombre o Razón Social",'0','C');
		$this->Rect(170, $y, 80, 10);$this->setXY(170,$y);$this->MultiCell(80,10,"Dirección",'0','C');
		$this->Rect(250, $y, 30, 10);$this->setXY(250,$y);$this->MultiCell(30,10,"Importe Total",'0','C');
	}		
	function Publicar($items){
		$monedas = array(
			"S"=>array("simb"=>"S/.","nomb"=>"NUEVO SOL","plu"=>"NUEVOS SOLES"),
			"D"=>array("simb"=>"$.","nomb"=>"DOLAR","plu"=>"DOLARES")
		);
		$x=0;
		$y=25;
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
			$nomb = $item["cliente"]["nomb"];
			if($item["cliente"]["tipo_enti"]=="E"){				
				$tipo = "Juridica";
				$docum = "RUC";
				foreach($item["cliente"]["docident"] as $docs){
					if($docs["tipo"]=="RUC") $num = $docs["num"];
				}
			}elseif($item["cliente"]["tipo_enti"]=="P"){
				$nomb .= " ".$item["cliente"]["appat"]." ".$item["cliente"]["apmat"];
				$tipo = "Natural";
				$docum = "DNI";
				foreach($item["cliente"]["docident"] as $docs){
					if($docs["tipo"]=="DNI") $num = $docs["num"];
				}
			}
			$this->Rect(15, $y, 20, 5);$this->setXY(15,$y);$this->MultiCell(20,5,$tipo,'0','C');
			$this->Rect(35, $y, 20, 5);$this->setXY(35,$y);$this->MultiCell(20,5,$docum,'0','C');
			$this->Rect(55, $y, 35, 5);$this->setXY(55,$y);$this->MultiCell(35,5,$num,'0','C');
			$this->Rect(90, $y, 80, 5);$this->setXY(90,$y);$this->MultiCell(80,5,$nomb,'0','L');
			$this->Rect(170, $y, 80, 5);$this->setXY(170,$y);$this->MultiCell(80,5,$item["cliente"]["domicilios"][0]["direccion"],'0','L');
			$this->Rect(250, $y, 30, 5);$this->setXY(250,$y);$this->MultiCell(30,5,$monedas[$item["moneda"]]["simb"].number_format($item["total"],2,".", ","),'0','R');
			$y=$y+5;
		}
	}
	function Footer()
	{
		$this->SetXY(260,-10);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(30,10,'Página: '.$this->PageNo().'/{nb}',0,0,'R');
    	
    	$this->SetXY(15,-10);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,'Fecha de Impresión: '.date("d-m-Y"),0,0,'L');
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
