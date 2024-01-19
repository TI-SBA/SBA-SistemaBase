<?php
global $f;
$f->library('pdf');

class bole extends FPDF
{
	function Header(){
		$ancho = 205.9;
		$this->SetFont('courier','',12);
		$this->setXY(5,5);$this->MultiCell($ancho,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetFont('courier','',10);
		$this->setXY(5,10);$this->MultiCell($ancho,5,"Dirección: Calle Pierola #201, Cercado",'0','L');
		$this->setXY(5,10);$this->MultiCell($ancho,5,"RUC: 20120958136",'0','R');
		//$this->Rect(5, 25, $ancho, 15);
		$this->Line(5, 25, $ancho+5, 25);
		$this->SetFont('courier','B',11);
		$this->setXY(5,25);$this->MultiCell(40,5,"Nombre:",'0','L');
		$this->setXY(150,25);$this->MultiCell(40,5,"Fecha:",'0','L');
		$this->setXY(5,30);$this->MultiCell(40,5,"Cargo:",'0','L');
		$this->setXY(90,30);$this->MultiCell(40,5,"Categoría:",'0','L');
		$this->setXY(150,30);$this->MultiCell(40,5,"DNI:",'0','L');
		$this->setXY(5,35);$this->MultiCell(50,5,"Fecha de Ingreso:",'0','L');
		$this->setXY(90,35);$this->MultiCell(40,5,"CUI AFP:",'0','L');
		$this->setXY(150,35);$this->MultiCell(40,5,"Cod. ESSALUD:",'0','L');
		
		$this->setXY(5,40);$this->MultiCell($ancho/3,5,"Pagos",'1','C');
		$this->setXY(5+($ancho/3),40);$this->MultiCell($ancho/3,5,"Descuentos",'1','C');
		$this->setXY(5+(2*$ancho/3),40);$this->MultiCell($ancho/3,5,"Patronal",'1','C');
	}		
	function Publicar($items){
		$x=0;
		$y=50;//41
		$y_marg = 5;
		$meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
		$mes = $items['periodo']['mes']-1;
		$this->SetFont('courier','B',12);
		$this->setXY(5,15);$this->MultiCell(205.9,5,"BOLETA DE PAGO - NOMBRADOS".(($items["vacaciones"]==true)?" - VACACIONES":"")." - NORMALES",'0','C');
		$this->SetFont('courier','',10);
		$this->setXY(5,20);$this->MultiCell(205.9,3,"PERIODO:".$meses[$items["periodo"]["mes"]-1]." ".$items["periodo"]["ano"],'0','R');
		$this->setXY(23,25);$this->MultiCell(110,5,$items['trabajador']['appat']." ".$items['trabajador']['apmat']." ".$items['trabajador']['nomb'],'0','L');
		$this->setXY(170,25);$this->MultiCell(40,5,Date::format($items['fecreg']->sec, 'd/m/Y'),'0','L');
		$this->setXY(20,30);$this->MultiCell(70,3,$items['trabajador']['roles']['trabajador']['cargo']['nomb'],'0','L');
		$this->setXY(115,30);$this->MultiCell(40,5,$items['trabajador']['roles']['trabajador']['cargo_clasif']['cod'],'0','L');
		$this->setXY(165,30);$this->MultiCell(40,5,$items['trabajador']['docident'][0]['num'],'0','L');
		if(isset($items['trabajador']['roles']['trabajador']['ficha']['fec_ing_sbpa'])){
			$this->setXY(50,35);$this->MultiCell(50,5,Date::format($items['trabajador']['roles']['trabajador']['ficha']['fec_ing_sbpa']->sec,"d/m/Y"),'0','L');
		}
		$this->setXY(105,35);$this->MultiCell(40,5,$items['trabajador']['roles']['trabajador']['cod_aportante'],'0','L');
		$this->setXY(180,35);$this->MultiCell(40,5,$items['trabajador']['roles']['trabajador']['essalud'],'0','L');
		$ancho = 205.9/3;
		$ancho_monto = 20;
		$ancho_nomb = $ancho-$ancho_monto;
		$y_1 = 45;
		$alto = 2.7;
		for($i=0;$i<count($items['conceptos']);$i++){
			if($items['conceptos'][$i]['concepto']['tipo']=="P"&&$items['conceptos'][$i]['subtotal']!="0.00"){
				$this->setXY(5+$ancho_nomb,$y_1);$this->MultiCell($ancho_monto,$alto,number_format($items['conceptos'][$i]['subtotal'],2),'0','R');
				$this->setXY(5,$y_1);$this->MultiCell($ancho_nomb,$alto,$items['conceptos'][$i]['concepto']['nomb'],'0','L');
				$y_1 = $this->GetY();
			}
		}
		//$this->Line(5, 45, $x2, $y2);
		$y_2 = 45;
		for($i=0;$i<count($items['conceptos']);$i++){
			if($items['conceptos'][$i]['concepto']['tipo']=="D"&&$items['conceptos'][$i]['subtotal']!="0.00"){
				$this->setXY($ancho+5+$ancho_nomb,$y_2);$this->MultiCell($ancho_monto,$alto,number_format($items['conceptos'][$i]['subtotal'],2),'0','R');
				$this->setXY($ancho+5,$y_2);$this->MultiCell($ancho_nomb,$alto,$items['conceptos'][$i]['concepto']['nomb'],'0','L');
				$y_2 = $this->GetY();
			}
		}
		$y_3 = 45;
		for($i=0;$i<count($items['conceptos']);$i++){
			if($items['conceptos'][$i]['concepto']['tipo']=="A"&&$items['conceptos'][$i]['subtotal']!="0.00"){
				$this->setXY(2*$ancho+5+$ancho_nomb,$y_3);$this->MultiCell($ancho_monto,$alto,number_format($items['conceptos'][$i]['subtotal'],2),'0','R');
				$this->setXY(2*$ancho+5,$y_3);$this->MultiCell($ancho_nomb,$alto,$items['conceptos'][$i]['concepto']['nomb'],'0','L');
				$y_3 = $this->GetY();
			}
		}
		if($y_1>$y_2){
			$y_t = $y_1;
		}else {
			$y_t = $y_2;
		}
		if($y_t<75){
			$y_t = $y_3+25;
		}
		$this->setXY(5,$y_t);$this->MultiCell($ancho_nomb,$alto,"TOTAL PAGOS",'0','C');
		$this->setXY(5+$ancho_nomb,$y_t);$this->MultiCell($ancho_monto,$alto,$items['total_pago'],'0','R');
		$this->setXY($ancho+5,$y_t);$this->MultiCell($ancho_nomb,$alto,"TOTAL DESCUENTOS",'0','C');
		$this->setXY($ancho+5+$ancho_nomb,$y_t);$this->MultiCell($ancho_monto,$alto,$items['total_desc'],'0','R');
		$this->setXY(2*$ancho+5,$y_t-20);$this->MultiCell($ancho_nomb,$alto,"TOTAL PATRONAL",'0','C');
		$this->setXY(2*$ancho+5+$ancho_nomb,$y_t-20);$this->MultiCell($ancho_monto,$alto,$items['total_apor'],'0','R');
		
		$this->setXY(2*$ancho+5,$y_t-10);$this->MultiCell($ancho_nomb,$alto,"NETO             S/.",'0','L');
		$this->setXY(2*$ancho+5+$ancho_nomb,$y_t-10);$this->MultiCell($ancho_monto,$alto,$items['total'],'0','R');
		$this->setXY(2*$ancho+5,$y_t-5);$this->MultiCell($ancho_nomb,$alto,"REDONDEO         S/.",'0','L');
		$this->setXY(2*$ancho+5+$ancho_nomb,$y_t-5);$this->MultiCell($ancho_monto,$alto,$items['redondeo'],'0','R');
		$this->setXY(2*$ancho+5,$y_t);$this->MultiCell($ancho_nomb,$alto,"NETO A PAGAR     S/.",'0','L');
		$this->setXY(2*$ancho+5+$ancho_nomb,$y_t);$this->MultiCell($ancho_monto,$alto,$items['neto'],'0','R');
		//$this->Rect(161, $y_comp, 40, 8);$this->setXY(161,$y_comp);$this->MultiCell(40,2,$comp["cod"]." ".$comp["nomb"],'0','L');
	}
	function Footer()
	{
		//footer
	} 
	 
}

$pdf=new bole('P','mm',array(215.9,279.4));
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("boleta");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>