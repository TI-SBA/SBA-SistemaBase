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
		$this->setY(5);$this->Cell(0,10,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",0,0,'C');
		$this->SetFont('Arial','I',13);
		$this->setY(10);$this->Cell(0,10,"ESTADO DE SITUACION ".strtoupper($meses[$this->mes])." - ".$this->ano,0,0,'C');
	}		
	function Publicar($items){
		$x=0;
		$y=20;
		$y_marg = 4.8;
		$this->SetY($y);
		/** Primera Columna */
		$this->SetFont('Arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(120,5,"ACTIVO",'0','L');
		$y=$y+10;
		$this->SetXY(15,$y);$this->MultiCell(60,5,"ACTIVO CORRIENTE",'0','L');
		$this->SetXY(75,$y);$this->MultiCell(20,5,"NOTAS Nº",'0','L');
		
		$y=$y+5;
		$this->SetFont('Arial','',9);
		$total_a_c = 0;
		foreach($items["activo_co"] as $item){
			$total_a_c = $total_a_c + $item["monto"];
			$this->SetXY(15,$y);$this->MultiCell(60,5,$item["nomb"],'0','L');
			$this->SetXY(75,$y);$this->MultiCell(20,5,$item["num"],'0','R');
			$this->Rect(95, $y, 40, 5);$this->SetXY(95,$y);$this->MultiCell(40,5,number_format($item["monto"],2,".", ","),'0','R');	
			$y=$y+5;
		}
		$this->SetFont('Arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(80,5,"TOTAL ACTIVO CORRIENTE",'0','L');
		$this->Rect(95, $y, 40, 5);$this->SetXY(95,$y);$this->MultiCell(40,5,number_format($total_a_c,2,".", ","),'0','R');
		$y=$y+10;
		$this->SetXY(15,$y);$this->MultiCell(60,5,"ACTIVO NO CORRIENTE",'0','L');		
		$y=$y+5;
		$this->SetFont('Arial','',9);
		$total_a_n = 0;
		foreach($items["activo_no"] as $item){
			$total_a_n = $total_a_n + $item["monto"];
			$this->SetXY(15,$y);$this->MultiCell(60,5,$item["nomb"],'0','L');
			$this->SetXY(75,$y);$this->MultiCell(20,5,$item["num"],'0','R');
			$this->Rect(95, $y, 40, 5);$this->SetXY(95,$y);$this->MultiCell(40,5,number_format($item["monto"],2,".", ","),'0','R');	
			$y=$y+5;
		}
		$this->SetFont('Arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(80,5,"TOTAL ACTIVO NO CORRIENTE",'0','L');
		$this->Rect(95, $y, 40, 5);$this->SetXY(95,$y);$this->MultiCell(40,5,number_format($total_a_n,2,".", ","),'0','R');
		$y=$y+10;
		$this->SetXY(15,$y);$this->MultiCell(80,5,"TOTAL ACTIVO",'0','L');
		$this->Rect(95, $y, 40, 5);$this->SetXY(95,$y);$this->MultiCell(40,5,number_format($total_a_c + $total_a_n,2,".", ","),'0','R');
		/** Segunda Columna */
		$y = 20;
		$this->SetFont('Arial','B',9);
		$this->SetXY(160,$y);$this->MultiCell(120,5,"PASIVO Y PATRIMONIO",'0','L');
		$y=$y+10;
		$this->SetXY(160,$y);$this->MultiCell(60,5,"PASIVO CORRIENTE",'0','L');
		$this->SetXY(220,$y);$this->MultiCell(20,5,"NOTAS Nº",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',9);
		$total_p_c = 0;
		foreach($items["pasivo_co"] as $item){
			$total_p_c = $total_p_c + $item["monto"];
			$this->SetXY(160,$y);$this->MultiCell(60,5,$item["nomb"],'0','L');
			$this->SetXY(220,$y);$this->MultiCell(20,5,$item["num"],'0','R');
			$this->Rect(240, $y, 40, 5);$this->SetXY(240,$y);$this->MultiCell(40,5,number_format($item["monto"],2,".", ","),'0','R');	
			$y=$y+5;
		}
		$this->SetFont('Arial','B',9);
		$this->SetXY(160,$y);$this->MultiCell(80,5,"TOTAL PASIVO CORRIENTE",'0','L');
		$this->Rect(240, $y, 40, 5);$this->SetXY(240,$y);$this->MultiCell(40,5,number_format($total_p_c,2,".", ","),'0','R');
		$y=$y+10;		
		$this->SetXY(160,$y);$this->MultiCell(60,5,"PASIVO NO CORRIENTE",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',9);
		$total_p_n = 0;
		foreach($items["pasivo_no"] as $item){
			$total_p_n = $total_p_n + $item["monto"];
			$this->SetXY(160,$y);$this->MultiCell(60,5,$item["nomb"],'0','L');
			$this->SetXY(220,$y);$this->MultiCell(20,5,$item["num"],'0','R');
			$this->Rect(240, $y, 40, 5);$this->SetXY(240,$y);$this->MultiCell(40,5,number_format($item["monto"],2,".", ","),'0','R');	
			$y=$y+5;
		}
		$this->SetFont('Arial','B',9);
		$this->SetXY(160,$y);$this->MultiCell(80,5,"TOTAL PASIVO NO CORRIENTE",'0','L');
		$this->Rect(240, $y, 40, 5);$this->SetXY(240,$y);$this->MultiCell(40,5,number_format($total_p_n,2,".", ","),'0','R');
		$y=$y+10;
		$this->SetXY(160,$y);$this->MultiCell(80,5,"TOTAL PASIVO",'0','L');
		$this->Rect(240, $y, 40, 5);$this->SetXY(240,$y);$this->MultiCell(40,5,number_format($total_p_n+$total_p_c,2,".", ","),'0','R');
		$y=$y+10;
		$this->SetXY(160,$y);$this->MultiCell(60,5,"PATRIMONIO",'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',9);
		$total_p_p = 0;
		foreach($items["pasivo_pa"] as $item){
			$total_p_p = $total_p_p + $item["monto"];
			$this->SetXY(160,$y);$this->MultiCell(60,5,$item["nomb"],'0','L');
			$this->SetXY(220,$y);$this->MultiCell(20,5,$item["num"],'0','R');
			$this->Rect(240, $y, 40, 5);$this->SetXY(240,$y);$this->MultiCell(40,5,number_format($item["monto"],2,".", ","),'0','R');	
			$y=$y+5;
		}
		$this->SetFont('Arial','B',9);
		$this->SetXY(160,$y);$this->MultiCell(80,5,"TOTAL PATRIMONIO",'0','L');
		$this->Rect(240, $y, 40, 5);$this->SetXY(240,$y);$this->MultiCell(40,5,number_format($total_p_p,2,".", ","),'0','R');
		$y=$y+10;
		$this->SetXY(160,$y);$this->MultiCell(80,5,"TOTAL PASIVO Y PATRIMONIO",'0','L');
		$this->Rect(240, $y, 40, 5);$this->SetXY(240,$y);$this->MultiCell(40,5,number_format($total_p_n+$total_p_c+$total_p_p,2,".", ","),'0','R');
	}
	function Footer()
	{
    	//Footer de la pagina
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