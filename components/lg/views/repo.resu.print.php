<?php
global $f;
$f->library('pdf');
class expdientes extends FPDF
{
	var $filter;
	function Filtros($filtros){
		$this->filter = $filtros;
	}
	function Header(){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		//$this->Image(IndexPath.DS.'templates/td/expd.gif',15,15,347,249);
		$this->SetFont('Arial','B',14);
		$this->SetXY(10,20);$this->MultiCell(190,5,"RESUMEN POR PARTIDAS-MOVIMIENTO DEL MES DE ",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." - Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Logistica",'0','C');
		$this->SetFont('arial','B',10);
		$y=30;
		$this->SetXY(10,$y);$this->MultiCell(50,5,"PARTIDA",'1','C');
		$this->SetXY(60,$y);$this->MultiCell(40,5,$this->filter["anterior"],'1','C');
		$this->SetXY(100,$y);$this->MultiCell(30,5,"DEBE",'1','C');
		$this->SetXY(130,$y);$this->MultiCell(30,5,"HABER",'1','C');
		$this->SetXY(160,$y);$this->MultiCell(40,5,$this->filter["actual"],'1','C');
	}
	function Publicar($items){
		global $f;
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		$this->SetFont('arial','',9);
		$y=35;
		$tot_1 = 0;
		$tot_2 = 0;
		$tot_3 = 0;
		$tot_4 = 0;
		foreach($items as $item){
			if($y>275){
				$this->AddPage();
				$y=35;
			}
			$tot_1+=$item["saldo_ini"];
			$tot_2+=$item["debe"];
			$tot_3+=$item["haber"];
			$sald_fin = $item["saldo_ini"]+$item["debe"]-$item["haber"];
			$tot_4+=$sald_fin;
			$this->SetXY(10,$y);$this->MultiCell(50,5,$item["cod"],'1','L');
			$this->SetXY(60,$y);$this->MultiCell(40,5,number_format($item["saldo_ini"],2),'1','R');
			$this->SetXY(100,$y);$this->MultiCell(30,5,number_format($item["debe"],2),'1','R');
			$this->SetXY(130,$y);$this->MultiCell(30,5,number_format($item["haber"],2),'1','R');
			$this->SetXY(160,$y);$this->MultiCell(40,5,number_format($item["saldo_ini"]+$item["debe"]-$item["haber"],2),'1','R');
			$y+=5;
		}
		$this->SetFont('arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(50,5,"TOTAL",'1','C');
		$this->SetXY(60,$y);$this->MultiCell(40,5,number_format($tot_1,2),'1','R');
		$this->SetXY(100,$y);$this->MultiCell(30,5,number_format($tot_2,2),'1','R');
		$this->SetXY(130,$y);$this->MultiCell(30,5,number_format($tot_3,2),'1','R');
		$this->SetXY(160,$y);$this->MultiCell(40,5,number_format($tot_4,2),'1','R');
		$y+=5;
	}
	function Footer()
	{
    	//Footer de la pagina
	} 
}
$pdf=new expdientes('P','mm','A4');
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