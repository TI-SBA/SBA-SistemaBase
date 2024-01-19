<?php
global $f;
$f->library('pdf');

class diliprog extends FPDF
{
	function Header(){
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',10);
		$this->setXY(15,15);$this->Cell(0,10,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",0,0,'L');
		$this->setXY(15,15);$this->Cell(220,10,"IDENTIFICACIÓN:",0,0,'R');	
		$this->setXY(15,25);$this->Cell(180,10,"COMPROMISOS DE ALQUILERES INMUEBLES MES DE",0,0,'L');		
		$this->setXY(15,30);$this->Cell(100,10,"NOMBRES Y APELLIDOS",0,0,'C');
		$this->setXY(115,30);$this->Cell(50,10,"UBICACIÓN",0,0,'C');
		$this->setXY(165,25);$this->Cell(60,10,"ALQUILERES",0,0,'C');
		$this->setXY(165,30);$this->Cell(30,10,"DOLARES",0,0,'C');
		$this->setXY(195,30);$this->Cell(30,10,"N.SOLES",0,0,'C');
		$this->setXY(225,25);$this->Cell(60,10,"SIN CONTRATO",0,0,'C');
		$this->setXY(225,30);$this->Cell(30,10,"DEL",0,0,'C');
		$this->setXY(255,30);$this->Cell(30,10,"AL",0,0,'C');
		$this->Line(15, 40, 280, 40);
	}		
	function Publicar($items){
		$usos = array(
			"TI"=>"Tiendas",
			"OF"=>"Oficina",
			"HO"=>"Hotel",
			"ST"=>"Stand",
			"CI"=>"Cine",
			"ES"=>"Espacio",
			"CO"=>"Cochera",
			"VI"=>"Casa Habitación",
			"OT"=>"Otros"
		);				
		foreach($items as $key=>$uso){
			$this->AddPage();
			$t_d = 0;
			$t_s = 0;
			$this->SetFont('Arial','B',10);
			$this->setXY(235,15);$this->Cell(45,10,$usos[$key],0,0,'L');
			$x=0;
			$y=40;
			$y_ini = $y;
			$page_b = 190;
			$index=0;
			foreach($uso as $local){
				if($y>$page_b){
					$this->AddPage();
					$y = $y_ini;
				}
				$this->SetFont('Arial','B',10);
				$this->setXY(15,$y);$this->Cell(100,5,$local["nomb"],0,0,'L');
				$y=$y+5;			
				foreach($local["items"] as $item){
					if($y>$page_b){
						$this->AddPage();
						$y = $y_ini;
					}
					$this->SetFont('Arial','',10);
					$enti = $item["arrendatario"]["nomb"];
					if($item["arrendatario"]["tipo_enti"]=="P")$enti .=" ".$item["arrendatario"]["appat"]." ".$item["arrendatario"]["apmat"];
					$this->setXY(15,$y);$this->Cell(100,5,$enti,0,0,'L');
					$this->setXY(115,$y);$this->Cell(50,5,$item["espacio"]["descr"],0,0,'L');
					$this->setXY(225,$y);$this->Cell(30,5,Date::format($item["arrendamiento"]["feccon"]->sec, 'd/m/Y'),0,0,'C');
					$this->setXY(255,$y);$this->Cell(30,5,Date::format($item["arrendamiento"]["fecven"]->sec, 'd/m/Y'),0,0,'C');
					foreach($item["arrendamiento"]["rentas"] as $renta){
						if($renta["moneda"]=="D"){
							$this->setXY(165,$y);$this->Cell(30,5,number_format($renta["importe"],2),0,0,'R');
							$t_d += $renta["importe"];
						}else{
							$this->setXY(195,$y);$this->Cell(30,5,number_format($renta["importe"],2),0,0,'R');
							$t_s += $renta["importe"];
						}
						$y=$y+5;
					}
				}			
			}
			$this->SetFont('Arial','B',10);
			$this->setXY(15,$y);$this->Cell(100,5,"TOTAL",0,0,'L');
			$this->setXY(165,$y);$this->Cell(30,5,number_format($t_d,2),0,0,'R');
			$this->setXY(195,$y);$this->Cell(30,5,number_format($t_s,2),0,0,'R');
			$this->Line(15, $y, 280, $y);
			$this->Line(15, $y+5, 280, $y+5);
			
		}		
		//$this->SetY($y);
		//$this->Rect(185, $y, 30, $alto);$this->SetXY(185,$y);$this->MultiCell(30,5,Date::format($row["fecha"]->sec, 'd/m/Y'),'0','C');	
		//$this->Rect(215, $y, 25, $alto);$this->SetXY(215,$y);$this->MultiCell(25,5,$row["lugar"],'0','L');
		//$this->Rect(240, $y, 50, $alto);$this->SetXY(240,$y);$this->MultiCell(50,5,$row["observ"],'0','L');
	}
	function Footer()
	{
    	//Footer de la pagina
    	$this->SetXY(170,-10);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,"Página: ".$this->PageNo(),0,0,'C');
    	
    	$this->SetXY(28,-10);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,"Fecha de Impresión: ".date("d-m-Y"),0,0,'L');
	} 
	 
}

$pdf=new diliprog('L','mm','A4');
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>