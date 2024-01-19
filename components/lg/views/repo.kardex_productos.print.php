<?php
global $f;
$f->library('pdf');
class repo extends FPDF
{
	var $producto;
	var $almacen;
	function Filtros($producto, $almacen){
		$this->producto = $producto;
		$this->almacen = $almacen;
	}
	function Header(){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		//$this->Image(IndexPath.DS.'templates/td/expd.gif',15,15,347,249);
		$this->SetFont('Arial','B',14);
		$this->SetXY(10,10);$this->MultiCell(190,5,"REPORTE KARDEX PRODUCTOS",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." - Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Logistica",'0','C');
		$this->SetFont('arial','B',10);
		$this->SetXY(10,25);$this->MultiCell(190,5,"Almacen: ".$this->almacen["nomb"],'0','L');
		$this->SetXY(10,30);$this->MultiCell(190,5,"Producto: ".$this->producto['nomb'],'0','L');
		$y=40;
		$this->SetXY(10,$y);$this->MultiCell(30,5,"Fecha",'1','C');
		$this->SetXY(40,$y);$this->MultiCell(60,5,"Glosa",'1','C');
		$this->SetXY(100,$y);$this->MultiCell(40,5,"Doc. Ref",'1','C');
		$this->SetXY(140,$y);$this->MultiCell(20,5,"Entrada",'1','C');
		$this->SetXY(160,$y);$this->MultiCell(20,5,"Salida",'1','C');
		$this->SetXY(180,$y);$this->MultiCell(20,5,"Saldo",'1','C');
	}
	function Publicar($items){
		global $f;
		$this->SetFont('arial','',8);
		$y=45;
		$y_ini = $y;
		$total_entradas = 0;
		$total_salidas = 0;
		foreach($items as $i=>$item){
			if($y>270){
				$this->AddPage();
				$y=$y_ini;
			}
			if($item['tipo']=='E') $total_entradas+=$item['cant'];
			if($item['tipo']=='S') $total_salidas+=$item['cant'];
			$this->SetXY(10,$y);$this->MultiCell(30,5,date('d/m/Y H:i', $item['fecreg']->sec),'','L');
			$this->SetXY(100,$y);$this->MultiCell(40,5,$item['documento']['cod'],'','L');
			//$this->SetXY(140,$y);$this->MultiCell(20,5,number_format($item['entrada_cant'],2),'','R');
			//$this->SetXY(160,$y);$this->MultiCell(20,5,number_format($item['salida_cant'],2),'','R');
			//$this->SetXY(180,$y);$this->MultiCell(20,5,number_format($item['saldo_cant'],2),'','R');
			//print_r($item);
			//print_r("LA");
			if($item['tipo']=='E') {
				//print_r($item['tipo']);
				$this->SetXY(140,$y);$this->MultiCell(20,5,$item['cant'],'','R');
				$this->SetXY(160,$y);$this->MultiCell(20,5,'-','','R');
				$this->SetXY(180,$y);$this->MultiCell(20,5,$item['saldo'],'','R');
			}
			if($item['tipo']=='S') {
				//var_dump($item['tipo']);
				$this->SetXY(140,$y);$this->MultiCell(20,5,'-','','R');
				$this->SetXY(160,$y);$this->MultiCell(20,5,$item['cant'],'','R');
				$this->SetXY(180,$y);$this->MultiCell(20,5,$item['saldo'],'','R');
			}
			$this->SetXY(40,$y);$this->MultiCell(60,5,$item['glosa'],'','L');
			$y=$this->getY();
			$this->Line(10, $y, 200, $y);
		}
		$this->SetXY(100,$y);$this->MultiCell(40,5,'TOTALES','','L');
		$this->SetXY(140,$y);$this->MultiCell(20,5,number_format($total_entradas,2),'','R');
		$this->SetXY(160,$y);$this->MultiCell(20,5,number_format($total_salidas,2),'','R');
		$y=$this->getY();
		$this->Line(10, $y, 200, $y);
	}
	function Footer()
	{
    	//Footer de la pagina
	} 
}
/*	function Publicar($items){
		global $f;
		$this->SetFont('arial','',8);
		$y=45;
		$y_ini = $y;
		$total_entradas = 0;
		$total_salidas = 0;
		foreach($items as $i=>$item){
			if($y>270){
				$this->AddPage();
				$y=$y_ini;
			}
			$total_entradas+=$item['entrada_cant'];
			$total_salidas+=$item['salida_cant'];
			$this->SetXY(10,$y);$this->MultiCell(30,5,date('d/m/Y H:i', $item['fecreg']->sec),'','L');
			$this->SetXY(100,$y);$this->MultiCell(40,5,$item['documento']['cod'],'','L');
			$this->SetXY(140,$y);$this->MultiCell(20,5,number_format($item['entrada_cant'],2),'','R');
			$this->SetXY(160,$y);$this->MultiCell(20,5,number_format($item['salida_cant'],2),'','R');
			$this->SetXY(180,$y);$this->MultiCell(20,5,number_format($item['saldo_cant'],2),'','R');
			$this->SetXY(40,$y);$this->MultiCell(60,5,$item['glosa'],'','L');
			$y=$this->getY();
			$this->Line(10, $y, 200, $y);
		}
		$this->SetXY(100,$y);$this->MultiCell(40,5,'TOTALES','','L');
		$this->SetXY(140,$y);$this->MultiCell(20,5,number_format($total_entradas,2),'','R');
		$this->SetXY(160,$y);$this->MultiCell(20,5,number_format($total_salidas,2),'','R');
		$y=$this->getY();
		$this->Line(10, $y, 200, $y);
	}
	function Footer()
	{
    	//Footer de la pagina
	} 
}*/
$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filtros($producto, $almacen);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>