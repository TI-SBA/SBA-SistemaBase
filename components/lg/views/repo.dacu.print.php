<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $mes;
	var $ano;
	function Filtros($filtros){
		$this->mes = $filtros["mes"];
		$this->ano = $filtros["ano"];
	}
	function Header(){
		$meses = array("Todos","ENE","FEB","MAR","ABR","MAY","JUN","JUL","AGO","SET","OCT","NOV","DIC");
		$x=5;
		$y=5;
		$this->SetFont('Arial','B',12);
		$this->setXY(10,$y);$this->MultiCell(390,5,"AJUSTES A LOS ESTADOS FINANCIEROS POR EFECTOS DE INFLACION DE INMUEBLES, MAQUINARIA Y EQUIPO - DEPRECIACION ACUMULADA ".$meses[$this->mes]."-".$this->ano,'0','C');
		$y=$y+5;
		$this->SetFont('Arial','B',9);
		$this->Rect(5, $y, 15, 10);$this->setXY(5,$y);$this->MultiCell(15,10,"Cta",'0','C');
		$this->Rect(20, $y, 10, 10);$this->setXY(20,$y);$this->MultiCell(10,10,"Cod",'0','C');
		$this->Rect(30, $y, 70, 10);$this->setXY(30,$y);$this->MultiCell(70,10,"DESCRIPCION",'0','C');
		$this->Rect(100, $y, 10, 10);$this->setXY(100,$y);$this->MultiCell(10,10,"Mes",'0','C');
		$this->Rect(110, $y, 13, 10);$this->setXY(110,$y);$this->MultiCell(13,10,"Año",'0','C');
		$this->Rect(123, $y, 27, 10);$this->setXY(123,$y);$this->MultiCell(27,10,"val_lib",'0','C');
		$this->Rect(150, $y, 15, 10);$this->setXY(150,$y);$this->MultiCell(15,10,"val_aju_",'0','C');//
		$this->Rect(165, $y, 15, 10);$this->setXY(165,$y);$this->MultiCell(15,10,"val_aju",'0','C');
		$this->Rect(180, $y, 25, 10);$this->setXY(180,$y);$this->MultiCell(25,10,"nva_lib",'0','C');
		$this->Rect(205, $y, 15, 10);$this->setXY(205,$y);$this->MultiCell(15,10,"dif_aju",'0','C');
		$this->Rect(220, $y, 15, 10);$this->setXY(220,$y);$this->MultiCell(15,10,"pro_flu",'0','C');
		$this->Rect(235, $y, 10, 10);$this->setXY(235,$y);$this->MultiCell(10,5,"ptdnme",'0','C');
		$this->Rect(245, $y, 25, 10);$this->setXY(245,$y);$this->MultiCell(25,10,"ptd_por",'0','C');
		$this->Rect(270, $y, 25, 10);$this->setXY(270,$y);$this->MultiCell(25,10,"dep_lib",'0','C');
		$this->Rect(295, $y, 20, 10);$this->setXY(295,$y);$this->MultiCell(20,10,"dep_por",'0','C');
		$this->Rect(315, $y, 25, 10);$this->setXY(315,$y);$this->MultiCell(25,10,"dep_acu",'0','C');
		$this->Rect(340, $y, 15, 10);$this->setXY(340,$y);$this->MultiCell(15,10,"dep_act",'0','C');
		$this->Rect(355, $y, 15, 10);$this->setXY(355,$y);$this->MultiCell(15,10,"dep_aj1",'0','C');
		$this->Rect(370, $y, 25, 10);$this->setXY(370,$y);$this->MultiCell(25,10,"val_net",'0','C');
	}		
	function Publicar($items){
		$y=20;
		$page_b = 270;
		$y_ini = $y;
		$this->SetFont('Arial','',9);
		foreach($items as $cuen){
			if($y>$page_b){
				$this->AddPage();
				$y=$y_ini;
			}
			$this->setXY(5,$y);$this->Cell(10,10,$cuen["cod"]);
			$this->setXY(30,$y);$this->MultiCell(70,10,$cuen["descr"],'0','L');
			$y=$y+5;
			foreach($cuen["items"] as $item){
				if($y>$page_b){
					$this->AddPage();
					$y=$y_ini;
				}
				$this->setXY(15,$y);$this->Cell(10,10,$item["producto"]["cod"]);
				$this->setXY(30,$y);$this->MultiCell(70,10,$item["producto"]["nomb"],'0','L');
				$this->setXY(100,$y);$this->Cell(10,10,Date::format($item["entrada"]["fec"]->sec, 'm'));
				$this->setXY(110,$y);$this->Cell(13,10,Date::format($item["entrada"]["fec"]->sec, 'y'));
				$this->setXY(123,$y);$this->MultiCell(27,10,number_format($item["valor_inicial"],2),'0','R');
				$this->setXY(150,$y);$this->MultiCell(15,10,"0.00",'0','R');
				$this->setXY(165,$y);$this->MultiCell(15,10,"0.00",'0','R');
				$this->setXY(180,$y);$this->MultiCell(25,10,number_format($item["valor_inicial"],2),'0','R');
				$this->setXY(205,$y);$this->MultiCell(15,10,"0.00",'0','R');
				$this->setXY(220,$y);$this->MultiCell(15,10,"0.00",'0','R');	
				if(isset($item["depreciacion"])){
					foreach($item["depreciacion"] as $dep){
						if((Date::format($dep["fecreg"]->sec, 'm')==$this->mes)&&(Date::format($dep["fecreg"]->sec, 'y')==$this->ano)){
							$this->setXY(245,$y);$this->MultiCell(25,10,$dep["porc"],'0','R');
							$this->setXY(270,$y);$this->MultiCell(25,10,$dep["total"],'0','C');
							$this->setXY(295,$y);$this->MultiCell(20,10,"0.00",'0','R');
							$this->setXY(315,$y);$this->MultiCell(25,10,$dep["acumulado"],'0','C');					
						}
					}
				}
				$this->setXY(340,$y);$this->MultiCell(15,10,"0.00",'0','R');
				$this->setXY(355,$y);$this->MultiCell(15,10,"0.00",'0','R');
				$this->setXY(370,$y);$this->MultiCell(25,10,number_format($item["valor_actual"],2),'0','R');
				$y=$y+5;
			}			
		}
		//$this->Rect(10, $y, 15, 5);$this->setXY(10,$y);$this->MultiCell(15,5,Date::format($item["fecreg"]->sec, 'd'),'0','L');
	}
	function Footer()
	{
		$this->SetXY(360,-10);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(30,10,'Página: '.$this->PageNo().'/{nb}',0,0,'L');
    	
    	$this->SetXY(15,-10);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,'Fecha de Impresión: '.date("d-m-Y"),0,0,'L');
	}  
}
$pdf=new repo('L','mm',array(280,400));
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filtros($filtros);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
$pdf->Output();
?>