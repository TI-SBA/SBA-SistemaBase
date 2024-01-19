<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $nro;
	var $fecreg;
	function filtros($filtros){
		$this->nro = $filtros["nro"];
		$this->fecreg = $filtros["fecreg"];
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante.gif',10,10,190,275);	
		$y=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');		
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Logistica",'0','C');
		$this->SetFont('Arial','B',16);
		$this->SetXY(5,$y);$this->MultiCell(200,5,"PEDIDO INTERNO",'0','C');
		$y=$y+10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(140,$y);$this->MultiCell(20,5,"Nº",'1','C');
		$this->SetXY(160,$y);$this->MultiCell(15,5,"DÍA",'1','C');
		$this->SetXY(175,$y);$this->MultiCell(15,5,"MES",'1','C');
		$this->SetXY(190,$y);$this->MultiCell(15,5,"AÑO",'1','C');
		$y=$y+5;
		$this->SetXY(140,$y);$this->MultiCell(20,8,$this->nro,'1','C');
		$this->SetXY(160,$y);$this->MultiCell(15,8,Date::format($this->fecreg->sec, 'd'),'1','C');
		$this->SetXY(175,$y);$this->MultiCell(15,8,Date::format($this->fecreg->sec, 'm'),'1','C');
		$this->SetXY(190,$y);$this->MultiCell(15,8,Date::format($this->fecreg->sec, 'Y'),'1','C');
		$y=$y+5;
	}		
	function Publicar($items){
		$monedas = array(
			"S"=>array("simb"=>"S/.","nomb"=>"NUEVO SOL","plu"=>"NUEVOS SOLES"),
			"D"=>array("simb"=>"USSD $.","nomb"=>"DOLAR","plu"=>"DOLARES")
		);
		$estados = array(
			"P"=>"PEDIENTE",
			"A"=>"APROBADO",
			"X"=>"ANULADO"
		);
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$this->SetFont('Arial','',11);
		$this->SetXY(5,$y);$this->MultiCell(200,5,"Programa: ".$items["trabajador"]["cargo"]["organizacion"]["nomb"],'0','L');
		$y=$y+5;
		$this->SetXY(5,$y);$this->MultiCell(200,5,"Señor Gerente:\nSirvase Ud. autorizar la compra de lo siguiente:\nEn los casos que se considere necesario, deben acompañarse a este Pedido Interno tres propuestas o cotizaciones de precios",'0','L');
		$y=$y+20;
		/** Header Grid */
		$this->SetFont('Arial','B',11);
		$this->SetXY(5,$y);$this->MultiCell(40,10,"Cantidad",'1','C');
		$this->SetXY(45,$y);$this->MultiCell(100,10,"DESCRIPCIÓN",'1','C');
		$this->SetXY(145,$y);$this->MultiCell(30,10,"UNIDAD",'1','C');
		$this->SetXY(175,$y);$this->MultiCell(30,10,"TOTAL",'1','C');
		$y=$y+10;
		/** Body Grid */
		$this->SetFont('Arial','',11);
		foreach($items["productos"] as $item){
			$alto = ceil(strlen($item["producto"]["nomb"])/90)*5;
			$this->Rect(5, $y, 40, $alto);$this->SetXY(5,$y);$this->MultiCell(40,5,$item["cant"],'0','C');
			$this->Rect(45, $y, 100, $alto);$this->SetXY(45,$y);$this->MultiCell(100,5,$item["producto"],'0','L');
			$this->Rect(145, $y, 30, $alto);$this->SetXY(145,$y);$this->MultiCell(30,5,$item["unidad"]["nomb"],'0','L');
			$this->Rect(175, $y, 30, $alto);
			$y=$y+$alto;
		}
		$y=$y+20;
		$this->SetFont('Arial','B',11);
		$this->SetXY(5,$y);$this->MultiCell(100,5,$estados[$items["estado"]],'0','C');
		$y=$y+10;
		$this->SetXY(5,$y);$this->MultiCell(100,5,"..............................................\nGERENTE",'0','C');
		$this->SetXY(105,$y);$this->MultiCell(100,5,"..............................................\nJEFE DE PROGRAMA",'0','C');
		//$this->Rect(175, $y, 30, $alto);$this->SetXY(175,$y);$this->MultiCell(30,5,number_format($item["subtotal"],2,".", ","),'0','R');
			
	}
	function Footer()
	{
		
	} 
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->filtros($filtros);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>