<?php
global $f;
$f->library('pdf');
setlocale(LC_ALL,"esp");
class repo extends FPDF
{
	var $filtros;
	function Filter($items){
		$this->filtros = $items;
	}
	function Header(){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(180,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','B',11);
		//$title = "";
		//if($this->filtros[""])
		$this->SetXY(10,25);$this->MultiCell(180,5,"REEMBOLSO SUBSIDIO - MATERMIDAD ".$this->filtros["contrato"]["nomb"],'0','C');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Personal",'0','C');
	}		
	function Publicar($items){
		$x=0;
		$y=40;
		$y_ini = 40;
		$this->SetFont('arial','BU',10);		
		$this->SetXY(15,$y);$this->MultiCell(60,5,"DATOS PERSONALES",'0','L');
		$y+=5;
		$this->SetFont('arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(60,5,"NOMBRES Y APELLIDOS",'0','L');
		$this->SetFont('arial','',9);
		$dni = "";
		foreach($items["trabajador"]["docident"] as $doc){
			if($doc["tipo"]=="DNI")$dni=$doc["num"];
		}
		$this->SetXY(75,$y);$this->MultiCell(120,5,$items["trabajador"]["nomb"]." ".$items["trabajador"]["appat"]." ".$items["trabajador"]["apmat"],'0','L');
		$y+=5;
		$this->SetFont('arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(60,5,"DNI",'0','L');
		$this->SetFont('arial','',9);
		$this->SetXY(75,$y);$this->MultiCell(120,5,$dni,'0','L');
		$y+=5;
		$this->SetFont('arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(60,5,"PROGRAMA",'0','L');
		$this->SetFont('arial','',9);
		$this->SetXY(75,$y);$this->MultiCell(120,5,"falta",'0','L');
		$y+=5;
		$this->Line(15, $y, 195, $y);
		$y+=5;
		$this->SetFont('arial','BU',9);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"CALCULO DEL SUBSIDIO",'0','L');
		$y+=5;
		$this->SetFont('arial','',9);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"12 Ultimas Remuneraciones Percibidas",'0','L');
		$y+=5;
		foreach($items["maternidad"]["meses"] as $mes){
			$this->SetXY(45,$y);$this->MultiCell(30,5,$mes["per"],'0','R');
			$this->SetXY(75,$y);$this->MultiCell(20,5,number_format($mes["total"],2),'0','R');
			$y+=5;
		}
		$this->Line(75, $y, 95, $y);
		$this->SetXY(75,$y);$this->MultiCell(20,5,number_format($items["maternidad"]["total_meses"],2),'0','R');
		$y+=10;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"PROMEDIO DIARIO = ",'0','L');
		$this->SetXY(75,$y);$this->MultiCell(55,5,number_format($items["maternidad"]["total_meses"],2),'0','C');
		
		$this->SetXY(135,$y);$this->MultiCell(25,5,number_format($items["maternidad"]["total_meses"]/12,2),'0','C');
		$this->SetXY(130,$y+2);$this->MultiCell(5,5,"=",'0','C');
		$this->SetXY(160,$y+2);$this->MultiCell(5,5,"=",'0','C');
		$this->SetXY(165,$y);$this->MultiCell(30,5,"S/. ".number_format($items["maternidad"]["promedio_diario"],2),'0','R');
		$y+=5;
		$this->Line(75, $y, 130, $y);
		$this->SetXY(75,$y);$this->MultiCell(55,5,"12          MESES X 30",'0','C');
		
		$this->Line(135, $y, 160, $y);
		$this->SetXY(135,$y);$this->MultiCell(25,5,"30",'0','C');
		$y+=10;
		$this->SetXY(105,$y);$this->MultiCell(30,5,"SUBSIDIADO DIARIO",'1','C');
		$this->SetXY(135,$y);$this->MultiCell(30,5,"DIAS SUBSIDIADOS",'1','C');
		$this->SetXY(165,$y);$this->MultiCell(30,5,"TOTAL\nSUBSIDIO",'1','C');
		$y+=10;
		$this->SetXY(105,$y);$this->MultiCell(30,5,number_format($items["maternidad"]["promedio_diario"],2),'0','C');
		$this->SetXY(135,$y);$this->MultiCell(30,5,number_format($items["maternidad"]["dias_subsidiados"],2),'0','C');
		$subtotal1 = $items["maternidad"]["dias_subsidiados"]*$items["maternidad"]["promedio_diario"];
		$this->SetXY(165,$y);$this->MultiCell(30,5,number_format($subtotal1,2),'0','R');
		$y+=5;
		$total1 = round($subtotal1);
		$this->SetXY(135,$y);$this->MultiCell(30,5,"REDONDEO",'0','C');
		$this->SetXY(165,$y);$this->MultiCell(30,5,number_format($total1-$subtotal1,2),'0','R');
		$y+=5;
		$this->SetXY(135,$y);$this->MultiCell(30,5,"POR REDONDEO",'0','C');
		$this->SetXY(165,$y);$this->MultiCell(30,5,number_format($total1,2),'0','R');
		$y+=5;
		$this->SetFont('arial','BU',9);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"DESCUENTOS",'0','L');
		$y+=5;
		$this->SetFont('arial','',9);
		foreach($items["maternidad"]["descuentos"] as $desc){
			$this->SetXY(15,$y);$this->MultiCell(60,5,$desc["descr"],'0','L');
			$this->SetXY(75,$y);$this->MultiCell(30,5,number_format($desc["num"],2),'0','R');
			$y+=5;
		}
		$this->SetXY(135,$y);$this->MultiCell(30,5,"TOTAL DSCTOS",'0','C');
		$this->SetXY(165,$y);$this->MultiCell(30,5,number_format($items["maternidad"]["total_descuentos"],2),'0','R');
		$y+=5;
		$this->SetXY(135,$y);$this->MultiCell(30,5,"REDONDEO",'0','C');
		$this->SetXY(165,$y);$this->MultiCell(30,5,number_format($items["maternidad"]["redondeo2"],2),'0','R');
		$y+=5;
		$this->SetXY(135,$y);$this->MultiCell(30,5,"TOTAL",'0','C');
		$this->SetXY(165,$y);$this->MultiCell(30,5,number_format($items["maternidad"]["total_pagar"],2),'0','R');
		$y+=10;
		$this->setXY(15,$y);$this->MultiCell(163,5,"SON".Number::lit($items["maternidad"]["total_pagar"]).' Y '.round((($items["maternidad"]["total_pagar"]-((int)$items["maternidad"]["total_pagar"]))*100),0).'/00 NUEVOS SOLES','0','L');
		$y+=10;
		$this->setXY(15,$y);$this->MultiCell(85,5,"Arequipa ".strftime("%d de %B del %Y")." \n Realizado por: ".$items["autor"]["nomb"]." ".$items["autor"]["appat"]." ".$items["autor"]["apmat"],'0','C');	
		$y+=10;
		$this->Line(110, $y, 195, $y);
		$this->setXY(110,$y);$this->MultiCell(85,5,"Recibí Conforme\n ".$items["trabajador"]["nomb"]." ".$items["trabajador"]["appat"]." ".$items["trabajador"]["apmat"]."\nDNI Nº ".$dni,'0','C');	
	}
	function Footer(){
  
	} 
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filter($items);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>