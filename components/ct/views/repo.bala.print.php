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
		$this->SetFont('Arial','B',11);
		$this->SetXY(10,20);$this->MultiCell(190,5,"BALANCE DE INGRESOS Y GASTOS ".strtoupper($meses[$this->mes])." - ".$this->ano,'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');		
		$this->SetXY(10,5);$this->MultiCell(190,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Contabilidad",'0','C');
		$this->SetFont('Arial','B',7);
		
		
		
		/*$this->SetFont('Arial','B',15);
		$this->setY(15);$this->Cell(0,10,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",0,0,'C');
		$this->SetFont('Arial','I',13);
		$this->setY(20);$this->Cell(0,10,"BALANCE DE INGRESOS Y GASTOS ".strtoupper($meses[$this->mes])." - ".$this->ano,0,0,'C');*/
		$this->SetFont('Arial','B',8);
		$this->setXY(10,30);$this->MultiCell(85,10,"ORGANIZACIÓN",'1','C');
		$this->Rect(95, 30, 35, 10);
		$this->setXY(130,30);$this->MultiCell(35,10,"EJECUCIÓN DEL MES",'1','C');
		$this->setXY(165,30);$this->MultiCell(35,10,"ACUMULADO",'1','C');
	}		
	function Publicar($items){
		$x=0;
		$y=40;
		$y_marg = 4.8;
		$this->SetY($y);
		$this->SetFont('Arial','',9);
		$t_ingr_ejec = 0;
		$t_gast_ejec = 0;
		$t_ingr_acum = 0;
		$t_gast_acum = 0;
		foreach($items as $item){
			if($this->getY()>275){
				$this->AddPage();
				$y = 40;
			}
			$ingr_ejec = array_sum($item["ingr"]);
			$gast_ejec = array_sum($item["gast"]);
			$ingr_acum = array_sum($item["acum_ingr"]);
			$gast_acum = array_sum($item["acum_gast"]);
			$t_ingr_ejec = $t_ingr_ejec + $ingr_ejec;
			$t_gast_ejec = $t_gast_ejec + $gast_ejec;
			$t_ingr_acum = $t_ingr_acum + $ingr_acum;
			$t_gast_acum = $t_gast_acum + $gast_acum;
			$this->Rect(10, $y, 85, 15);$this->SetXY(10,$y);$this->MultiCell(85,5,$item["nomb"],'0','L');	
			$this->Rect(95, $y, 35, 5);$this->SetXY(95,$y);$this->MultiCell(35,5,"Ingresos",'0','L');	
			$this->Rect(95, $y+5, 35, 5);$this->SetXY(95,$y+5);$this->MultiCell(35,5,"Gastos",'0','L');	
			$this->Rect(95, $y+10, 35, 5);$this->SetXY(95,$y+10);$this->MultiCell(35,5,"Superavit o Deficit",'0','L');
			$this->Rect(130, $y, 35, 5);$this->SetXY(130,$y);$this->MultiCell(35,5,number_format($ingr_ejec,2),'0','R');	
			$this->Rect(130, $y+5, 35, 5);$this->SetXY(130,$y+5);$this->MultiCell(35,5,number_format($gast_ejec,2),'0','R');	
			$this->Rect(130, $y+10, 35, 5);$this->SetXY(130,$y+10);$this->MultiCell(35,5,number_format($ingr_ejec-$gast_ejec,2),'0','R');
			$this->Rect(165, $y, 35, 5);$this->SetXY(165,$y);$this->MultiCell(35,5,number_format($ingr_acum,2),'0','R');	
			$this->Rect(165, $y+5, 35, 5);$this->SetXY(165,$y+5);$this->MultiCell(35,5,number_format($gast_acum,2),'0','R');	
			$this->Rect(165, $y+10, 35, 5);$this->SetXY(165,$y+10);$this->MultiCell(35,5,number_format($ingr_acum-$gast_acum,2),'0','R');
			$y=$y+15;
		}
		$y = $y + 5;
		$this->Rect(95, $y, 35, 5);$this->SetXY(95,$y);$this->MultiCell(35,5,"Ingresos",'0','L');	
		$this->Rect(95, $y+5, 35, 5);$this->SetXY(95,$y+5);$this->MultiCell(35,5,"Gastos",'0','L');	
		$this->Rect(95, $y+10, 35, 5);$this->SetXY(95,$y+10);$this->MultiCell(35,5,"Superavit o Deficit",'0','L');
		$this->Rect(130, $y, 35, 5);$this->SetXY(130,$y);$this->MultiCell(35,5,number_format($t_ingr_ejec,2),'0','R');	
		$this->Rect(130, $y+5, 35, 5);$this->SetXY(130,$y+5);$this->MultiCell(35,5,number_format($t_gast_ejec,2),'0','R');	
		$this->Rect(130, $y+10, 35, 5);$this->SetXY(130,$y+10);$this->MultiCell(35,5,number_format($t_ingr_ejec-$t_gast_ejec,2),'0','R');
		$this->Rect(165, $y, 35, 5);$this->SetXY(165,$y);$this->MultiCell(35,5,number_format($t_ingr_acum,2),'0','R');	
		$this->Rect(165, $y+5, 35, 5);$this->SetXY(165,$y+5);$this->MultiCell(35,5,number_format($t_gast_acum,2),'0','R');	
		$this->Rect(165, $y+10, 35, 5);$this->SetXY(165,$y+10);$this->MultiCell(35,5,number_format($t_ingr_acum-$t_gast_acum,2),'0','R');
	}
	function Footer()
	{
    	/*//Footer de la pagina
    	$this->SetXY(170,-21);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,"Página: ".$this->PageNo()."/{nb}",0,0,'C');
    	
    	$this->SetXY(15,-21);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,"Fecha de Impresión: ".date("d-m-Y"),0,0,'L');*/
	}  
}

$pdf=new repo('P','mm','A4');
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