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
		$this->SetXY(5,$y);$this->MultiCell(50,5,"SOCIEDAD DE BENEFICENCIA PÚBLICA DE AREQUIPA",'0','C');	
		$this->SetFont('Arial','B',16);
		$this->SetXY(5,$y);$this->MultiCell(200,5,"NOTA DE ENTRADA A ALMACEN",'0','C');
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
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$this->SetFont('Arial','',11);
		$procedencia = $items["procedencia"]["nomb"];
		if($items["procedencia"]["tipo_enti"]=="P")$procedencia = $items["procedencia"]["nomb"]." ".$items["procedencia"]["appat"]." ".$items["procedencia"]["apmat"];
		$this->SetXY(5,$y);$this->MultiCell(135,5,"PROCEDENCIA: ".strtoupper($procedencia),'0','L');
		$y=$this->getY();
		$destino = $items["destino_a"]["local"]["descr"];
		$this->SetXY(5,$y);$this->MultiCell(135,5,"CON DESTINO A: ".strtoupper($destino),'0','L');
		$y=$this->getY();
		$this->SetXY(5,$y);$this->MultiCell(200,5,"SEGÚN: ".$items["segun"],'0','L');
		$y=$y+10;
		$this->SetFont('Arial','B',10);
		$this->SetXY(5,$y);$this->MultiCell(150,10,"ARTICULOS",'1','C');
		$this->SetXY(155,$y);$this->MultiCell(50,10,"IMPORTE S/.",'1','C');
		$y=$y+10;
		$this->SetXY(5,$y);$this->MultiCell(25,3,"GRUPO GENERICO Y/O Nº DE INVENTARIO",'1','C');
		$this->SetXY(30,$y);$this->MultiCell(75,12,"DESCRIPCION",'1','C');
		$this->SetXY(105,$y);$this->MultiCell(25,6,"UNIDAD DE MEDIDA",'1','C');
		$this->SetXY(130,$y);$this->MultiCell(25,12,"CANTIDAD",'1','C');
		$this->SetXY(155,$y);$this->MultiCell(25,12,"UNITARIO",'1','C');
		$this->SetXY(180,$y);$this->MultiCell(25,12,"TOTAL",'1','C');
		$y=$y+12;
		$this->SetFont('Arial','',9);
		foreach($items["productos"] as $item){
			$alto = ceil(strlen($item["producto"]["nomb"])/70)*5;
			$this->Rect(5, $y, 25, $alto);$this->SetXY(5,$y);$this->MultiCell(25,5,$item["producto"]["clasif"]["cod"],'1','C');
			$this->Rect(30, $y, 75, $alto);$this->SetXY(30,$y);$this->MultiCell(75,5,$item["producto"]["nomb"],'1','L');
			$this->Rect(105, $y, 25, $alto);$this->SetXY(105,$y);$this->MultiCell(25,5,$item["producto"]["unidad"]["nomb"],'1','C');
			$this->Rect(130, $y, 25, $alto);$this->SetXY(130,$y);$this->MultiCell(25,5,$item["cant"],'1','C');
			$this->Rect(155, $y, 25, $alto);$this->SetXY(155,$y);$this->MultiCell(25,5,number_format($item["precio_unit"],2,".", ","),'1','R');
			$this->Rect(180, $y, 25, $alto);$this->SetXY(180,$y);$this->MultiCell(25,5,number_format($item["subtotal"],2,".", ","),'1','R');
			$y=$y+$alto;
		}
		$y=$y+20;
		$this->SetXY(5,$y);$this->MultiCell(100,5,"..............................................\nJEFE DE ALMACEN",'0','C');
		$this->SetXY(105,$y);$this->MultiCell(100,5,"..............................................\nDIRECTOR ABASTECIMIENTO",'0','C');
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