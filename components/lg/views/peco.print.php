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
		$this->SetXY(50,$y);$this->MultiCell(200,5,"PEDIDO - COMPROBANTE DE SALIDA Nº ".$this->nro,'0','C');
		$y=$y+10;
		//$this->SetXY(160,$y);$this->MultiCell(15,8,Date::format($this->fecreg->sec, 'd'),'1','C');
	}		
	function Publicar($items){
		$monedas = array(
			"S"=>array("simb"=>"S/.","nomb"=>"NUEVO SOL","plu"=>"NUEVOS SOLES"),
			"D"=>array("simb"=>"USSD $.","nomb"=>"DOLAR","plu"=>"DOLARES")
		);
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		$x=5;
		$y=25;
		$y_ini = $y;
		$page_b = 275;
		$this->SetFont('Arial','',11);
		$this->SetXY(5,$y);$this->MultiCell(130,5,"DEPENDENCIA SOLICITANTE: ".$items["solicitante"]["cargo"]["organizacion"]["nomb"],'0','L');
		$this->SetXY(157,$y);$this->MultiCell(80,5,"Arequipa, ".Date::format($this->fecreg->sec, 'd')." de ".$meses[floatval(Date::format($this->fecreg->sec, 'm'))]." del".Date::format($this->fecreg->sec, 'Y'),'0','C');
		$responsable = $items["responsable"]["nomb"];
		if($items["responsable"]["tipo_enti"]=="P")$responsable = $items["responsable"]["nomb"]." ".$items["responsable"]["appat"]." ".$items["responsable"]["apmat"];
		$y=$y+5;
		$this->Rect(5, $y, 150, 15);
		$this->SetXY(5,$y);$this->MultiCell(130,7.5,"Solicito entregar a: ".$responsable,'0','L');
		$this->SetFont('Arial','B',11);
		$this->SetXY(157,$y);$this->MultiCell(80,5,"AFECTACION PRESUPUESTAL",'1','C');
		$this->SetFont('Arial','',7);
		$this->SetXY(157,$y+5);$this->MultiCell(30,5,"CTA.CONTABLE",'1','C');
		$this->SetXY(187,$y+5);$this->MultiCell(25,5,"UNID. EJECUTORA",'1','C');
		$this->SetXY(212,$y+5);$this->MultiCell(25,5,"OTROS",'1','C');
		$this->SetXY(157,$y+10);$this->MultiCell(30,5,"",'1','C');
		$this->SetXY(187,$y+10);$this->MultiCell(25,5,"",'1','C');
		$this->SetXY(212,$y+10);$this->MultiCell(25,5,"",'1','C');
		$this->SetFont('Arial','B',11);
		$this->SetXY(240,$y-5);$this->MultiCell(52,10,"PEDIDO Nº\nSALIDA Nº",'1','L');
		$y=$y+7.5;
		$this->SetFont('Arial','',11);
		$this->SetXY(5,$y);$this->MultiCell(130,7.5,"Con destino a: ".$items["destino"],'0','L');
		//$this->Rect(175, $y, 30, $alto);$this->SetXY(175,$y);$this->MultiCell(30,5,number_format($item["subtotal"],2,".", ","),'0','R');
		$y=$y+7.5;
		/** Header Grid */
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(10,5,"RENGLONES",'1','C');	
		$this->SetXY(15,$y);$this->MultiCell(135,5,"SOLICITADO",'1','C');
		$this->SetXY(15,$y+5);$this->MultiCell(135,5,"ARTICULOS",'1','C');	
		$this->SetXY(15,$y+10);$this->MultiCell(30,5,"CÓDIGO",'1','C');	
		$this->SetXY(45,$y+10);$this->MultiCell(85,5,"DESCRPCION",'1','C');	
		$this->SetXY(130,$y+10);$this->MultiCell(20,5,"CANTIDAD",'1','C');
		
		$this->SetXY(150,$y);$this->MultiCell(90,5,"DESPACHADO",'1','C');
		$this->SetXY(240,$y);$this->MultiCell(52,5,"VALOR",'1','C');
		$this->SetFont('Arial','B',7);
		$this->SetXY(150,$y+5);$this->MultiCell(30,5,"ESPECIFICACIONES",'1','C');	
		$this->SetXY(150,$y+10);$this->MultiCell(15,5,"MARCA",'1','C');	
		$this->SetXY(165,$y+10);$this->MultiCell(15,5,"Nº SERIE",'1','C');	
		$this->SetXY(180,$y+5);$this->MultiCell(30,10,"CLASIFICACIÓN",'1','C');
		$this->SetXY(210,$y+5);$this->MultiCell(15,10,"CANTIDAD",'1','C');
		$this->SetXY(225,$y+5);$this->MultiCell(15,5,"UNIDAD MEDIDA",'1','C');
		$this->SetXY(240,$y+5);$this->MultiCell(26,10,"UNITARIO",'1','C');
		$this->SetXY(266,$y+5);$this->MultiCell(26,10,"TOTAL",'1','C');
		$y=$y+15;
		/** Body Grid */
		$n = 1;
		$this->SetFont('Arial','',8);
		foreach($items["productos"] as $item){
			$alto = ceil(strlen($item["producto"]["nomb"])/80)*5;
			$this->Rect(5, $y, 10, $alto);$this->SetXY(5,$y);$this->MultiCell(10,5,$n,'0','C');	
			$this->Rect(15, $y, 30, $alto);$this->SetXY(15,$y);$this->MultiCell(30,5,$item["producto"]["clasif"]["cod"],'0','C');	
			$this->Rect(45, $y, 85, $alto);$this->SetXY(45,$y);$this->MultiCell(85,5,$item["producto"]["nomb"],'0','L');	
			$this->Rect(130, $y, 20, $alto);$this->SetXY(130,$y);$this->MultiCell(20,5,$item["solicitado"],'0','C');
			$this->Rect(150, $y, 15, $alto);//$this->SetXY(150,$y);$this->MultiCell(15,5,$item["producto"]["marca"]["nomb"],'0','C');		
			$this->Rect(165, $y, 15, $alto);//$this->SetXY(165,$y);$this->MultiCell(15,5,$item["num_serie"],'0','C');
			//$this->Rect(180, $y, 30, $alto);$this->SetXY(180,$y);$this->MultiCell(30,5,$item["producto"]["cuenta"]["cod"],'0','C');
			$this->Rect(180, $y, 30, $alto);$this->SetXY(180,$y);$this->MultiCell(30,5,"",'0','C');
			$this->Rect(210, $y, 15, $alto);$this->SetXY(210,$y);$this->MultiCell(15,5,$item["despachado"],'0','C');
			$this->Rect(225, $y, 15, $alto);$this->SetXY(225,$y);$this->MultiCell(15,5,$item["producto"]["unidad"]["nomb"],'0','C');
			$this->Rect(240, $y, 26, $alto);$this->SetXY(240,$y);$this->MultiCell(26,5,number_format($item["precio_unit"],4,".", ","),'0','R');
			$this->Rect(266, $y, 26, $alto);$this->SetXY(266,$y);$this->MultiCell(26,5,number_format($item["subtotal"],2,".", ","),'0','R');
			$y=$y+$alto;
			$n++;
		}
		if($items["ref"]!=""){
			$alto = ceil(strlen($items["ref"])/260)*5;
			$this->Rect(5, $y, 10, $alto);
			$this->Rect(15, $y, 30, $alto);
			$this->Rect(45, $y, 85, $alto);
			$this->Rect(130, $y, 20, $alto);
			$this->Rect(150, $y, 15, $alto);		
			$this->Rect(165, $y, 15, $alto);
			$this->Rect(180, $y, 30, $alto);
			$this->Rect(210, $y, 15, $alto);
			$this->Rect(225, $y, 15, $alto);
			$this->Rect(240, $y, 26, $alto);
			$this->Rect(266, $y, 26, $alto);
			$this->SetXY(45,$y);$this->MultiCell(260,5,$items["ref"],'0','L');
			$y=$y+5;
		}
		$this->SetFont('Arial','B',10);
		$this->SetXY(240,$y);$this->MultiCell(26,5,"TOTAL",'0','R');
		$this->SetXY(266,$y);$this->MultiCell(26,5,number_format($items["precio_total"],2,".", ","),'1','R');
		$y+=5;
		$this->SetFont('Arial','',9);
		$this->SetXY(5,$y);$this->MultiCell(200,5,"OBSERVACIONES.- ".$items["recibido"]["observ"],'0','L');
		$y+=15;
		$this->SetFont('Arial','B',10);
		$this->SetXY(5,$y);$this->MultiCell(75,5,"..............................................\nSOLICITANTE",'0','C');
		$this->SetXY(80,$y);$this->MultiCell(75,5,"..............................................\nDIRECTOR DE ABASTECIMIENTO",'0','C');
		$this->SetXY(155,$y);$this->MultiCell(75,5,"..............................................\nJEFE DE ALMACEN",'0','C');
		$this->SetXY(230,$y);$this->MultiCell(75,5,"..............................................\nRECIBI CONFORME",'0','C');
	}
	function Footer()
	{
		
	} 
	 
}

$pdf=new repo('L','mm','A4');
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