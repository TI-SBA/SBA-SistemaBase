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
		$this->SetFont('Arial','B',15);
		$this->setY(15);$this->Cell(0,10,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",0,0,'C');
		$this->SetFont('Arial','I',13);
		$this->setY(20);$this->Cell(0,10,"ESTADO DE GESTION ".strtoupper($meses[$this->mes])." - ".$this->ano,0,0,'C');
	}		
	function Publicar($items){
		$x=0;
		$y=40;
		$y_marg = 4.8;
		$this->SetY($y);
		/** Primera Columna */
		$this->SetFont('Arial','B',9);
		$this->SetXY(20,$y);$this->MultiCell(100,5,"INGRESOS",'0','L');
		$this->SetXY(120,$y);$this->MultiCell(20,5,"NOTAS Nº",'0','L');
		$y=$y+10;	
		$this->SetFont('Arial','',9);
		$total_i = 0;
		foreach($items["ingresos"] as $item){
			$total_i = $total_i + $item["monto"];
			$this->SetXY(20,$y);$this->MultiCell(80,5,$item["nomb"],'0','L');
			$this->SetXY(120,$y);$this->MultiCell(20,5,$item["num"],'0','R');
			$this->Rect(140, $y, 40, 5);$this->SetXY(140,$y);$this->MultiCell(40,5,number_format($item["monto"],2,".", ","),'0','R');	
			$y=$y+5;
		}
		$this->SetFont('Arial','B',9);
		$this->SetXY(20,$y);$this->MultiCell(80,5,"TOTAL INGRESOS",'0','L');
		$this->Rect(140, $y, 40, 5);$this->SetXY(140,$y);$this->MultiCell(40,5,number_format($total_i,2,".", ","),'0','R');
		$y=$y+10;
		$this->SetXY(20,$y);$this->MultiCell(80,5,"COSTOS Y GASTOS",'0','L');		
		$y=$y+5;
		$this->SetFont('Arial','',9);
		$total_g = 0;
		foreach($items["gastos"] as $item){
			$total_g = $total_g + $item["monto"];
			$this->SetXY(20,$y);$this->MultiCell(80,5,$item["nomb"],'0','L');
			$this->SetXY(120,$y);$this->MultiCell(20,5,$item["num"],'0','R');
			$this->Rect(140, $y, 40, 5);$this->SetXY(140,$y);$this->MultiCell(40,5,number_format($item["monto"],2,".", ","),'0','R');	
			$y=$y+5;
		}
		$this->SetFont('Arial','B',9);
		$this->SetXY(20,$y);$this->MultiCell(80,5,"TOTAL COSTOS Y GASTOS",'0','L');
		$this->Rect(140, $y, 40, 5);$this->SetXY(140,$y);$this->MultiCell(40,5,number_format($total_g,2,".", ","),'0','R');
		$y=$y+10;
		$this->SetXY(20,$y);$this->MultiCell(80,5,"RESULTADOS DE OPERACION",'0','L');
		$this->Rect(140, $y, 40, 5);$this->SetXY(140,$y);$this->MultiCell(40,5,number_format($total_i+$total_g,2,".", ","),'0','R');		
		$y=$y+10;
		$this->SetXY(20,$y);$this->MultiCell(80,5,"OTROS INGRESOS Y GASTOS",'0','L');		
		$y=$y+5;
		$this->SetFont('Arial','',9);
		$total_o = 0;
		foreach($items["gastos"] as $item){
			$total_o = $total_o + $item["monto"];
			$this->SetXY(20,$y);$this->MultiCell(80,5,$item["nomb"],'0','L');
			$this->SetXY(120,$y);$this->MultiCell(20,5,$item["num"],'0','R');
			$this->Rect(140, $y, 40, 5);$this->SetXY(140,$y);$this->MultiCell(40,5,number_format($item["monto"],2,".", ","),'0','R');	
			$y=$y+5;
		}
		$this->SetFont('Arial','B',9);
		$this->SetXY(20,$y);$this->MultiCell(80,5,"TOTAL OTROS INGRESOS Y GASTOS",'0','L');
		$this->Rect(140, $y, 40, 5);$this->SetXY(140,$y);$this->MultiCell(40,5,number_format($total_o,2,".", ","),'0','R');
		$y=$y+10;
		$this->SetXY(20,$y);$this->MultiCell(80,5,"RESULT.DEL EJERCICIO SUPERAVIT(DÉFICIT)",'0','L');
		$this->Rect(140, $y, 40, 5);$this->SetXY(140,$y);$this->MultiCell(40,5,number_format($total_i+$total_g+$total_o,2,".", ","),'0','R');
	}
	function Footer()
	{
    	//Footer de la pagina
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