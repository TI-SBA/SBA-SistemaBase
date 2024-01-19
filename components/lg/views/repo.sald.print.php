<?php
global $f;
$f->library('pdf');
class expdientes extends FPDF
{
	var $filter;
	var $y_start;
	function Filtros($filtros){
		$this->filter = $filtros;
		//print_r($this->filter);
	}
	function Header(){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		//$this->Image(IndexPath.DS.'templates/td/expd.gif',15,15,347,249);
		$this->SetFont('Arial','B',12);
		$this->SetXY(10,10);$this->MultiCell(190,5,"LISTADO DE SALDOS",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." - Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Logistica",'0','C');
		$this->SetFont('arial','',10);
		$y = 25;
		$this->SetXY(10,$y);$this->MultiCell(190,5,"CUENTA: ".$this->filter["cuenta"]["cod"]." - ".$this->filter["cuenta"]["descr"],'0','L');
		$y+=5;
		$this->SetXY(10,$y);$this->MultiCell(190,5,"PARTIDA: ".$this->filter["clasif"]["cod"].' - '.$this->filter["clasif"]["descr"],'0','L');
		$y=$this->getY();
		$this->SetXY(10,$y);$this->MultiCell(190,5,"SALDO FISICO AL: ".$this->filter["fecfin"],'0','L');
		$y+=5;
		$this->SetXY(10,$y);$this->MultiCell(190,5,"ALMACEN: ".$this->filter["almacen"]["nomb"],'0','L');
		$y+=5;
		//$this->SetXY(10,$y);$this->MultiCell(190,5,"Periodo: ".$meses[$this->filter["mes"]]." - ".$this->filter["ano"],'0','R');
		$this->SetXY(10,$y);$this->MultiCell(30,10,"CODIGO",'1','L');
		$this->SetXY(40,$y);$this->MultiCell(90,10,"DESCRIPCION",'1','L');
		$this->SetXY(130,$y);$this->MultiCell(40,10,"UNIDAD",'1','L');
		$this->SetXY(170,$y);$this->MultiCell(30,10,"SALDO FISICO",'1','L');
		$this->y_start = $this->getY();
	}
	function Publicar($items){
		global $f;
		/*$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");*/
		$this->SetFont('arial','',8);
		$y=$this->y_start;
		$y_ini = $y;
		foreach($items as $i=>$item){
			if($y>270){
				$this->AddPage();
				$y=$y_ini;
			}
			$this->SetXY(10,$y);$this->MultiCell(30,5,$item['producto']['cod'],'','L');
			$this->SetXY(40,$y);$this->MultiCell(90,5,$item['producto']['nomb'],'','L');
			$this->SetXY(130,$y);$this->MultiCell(40,5,$item['producto']['unidad']['nomb'],'','L');
			$this->SetXY(170,$y);$this->MultiCell(30,5,number_format($item['saldo_cant'],2),'','R');
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