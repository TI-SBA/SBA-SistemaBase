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
		$this->SetFont('Arial','B',12);
		$this->SetXY(10,10);$this->MultiCell(190,5,"MOVIMIENTOS POR DEPENDENCIA",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." - Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Logistica",'0','C');
		$this->SetFont('arial','B',10);
		$this->SetXY(10,25);$this->MultiCell(190,5,"Organización: ".$this->filter["organizacion"]["nomb"],'0','L');
		$this->SetXY(10,25);$this->MultiCell(190,5,"Periodo: ".$meses[$this->filter["mes"]]." - ".$this->filter["ano"],'0','R');
		$this->SetXY(10,30);$this->MultiCell(20,10,"Fecha",'1','C');
		$this->SetXY(30,30);$this->MultiCell(30,10,"Comprobante",'1','C');
		$this->SetXY(60,30);$this->MultiCell(60,10,"Producto",'1','C');
		$this->SetXY(120,30);$this->MultiCell(20,10,"Entrada",'1','C');
		$this->SetXY(140,30);$this->MultiCell(20,10,"Salida",'1','C');
		$this->SetXY(160,30);$this->MultiCell(20,5,"Precio Unit.",'1','C');
		$this->SetXY(180,30);$this->MultiCell(20,5,"Precio Total",'1','C');
	}
	function Publicar($items){
		global $f;
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		$this->SetFont('arial','',8);
		$y=40;
		$y_ini = $y;
		foreach($items as $i=>$item){
			if($y>270){
				$this->AddPage();
				$y=$y_ini;
			}
			$this->SetXY(10,$y);$this->MultiCell(20,5,Date::format($item["fecha"]->sec,"d/m/Y"),'0','C');
			$this->SetXY(30,$y);$this->MultiCell(30,5,$item["documento"],'0','C');
			$this->SetXY(120,$y);$this->MultiCell(20,5,$item["entrada"],'0','R');
			$this->SetXY(140,$y);$this->MultiCell(20,5,$item["salida"],'0','R');
			$this->SetXY(160,$y);$this->MultiCell(20,5,$item["precio_unit"],'0','R');
			$this->SetXY(180,$y);$this->MultiCell(20,5,$item["total"],'0','R');
			$this->SetXY(60,$y);$this->MultiCell(60,5,$item["producto"],'0','L');
			$y=$this->getY();
		}	
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