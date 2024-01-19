<?php
global $f;
$f->library('pdf');

class bole extends FPDF
{
	function Header(){
		
	}		
	function Publicar($items){
		$count = 0;
		$x=0;
		$y=5;//41
		$y_ini = $y;
		$y_marg = 5;
		$meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
		if($items!=null){
			foreach ($items as $key => $item){
				$_y = $y;
				//$mes = $item['periodo']['mes']-1;
				$ancho = 205.9;
				$this->SetFont('courier','',12);
				$this->setXY(5,$y);$this->MultiCell($ancho,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
				$y+=5;
				$this->SetFont('courier','',10);
				$this->setXY(5,$y);$this->MultiCell($ancho,5,"Dirección: Calle Pierola #201, Cercado",'0','L');
				$this->setXY(5,$y);$this->MultiCell($ancho,5,"RUC: 20120958136",'0','R');
				$y+=5;
				$this->SetFont('courier','B',12);
				$this->setXY(5,$y);$this->MultiCell($ancho,5,"BOLETA DE PAGO - CAS",'0','C');
				$y+=5;
				//$this->Rect(5, 25, $ancho, 15);
				$this->setXY(5,$y);$this->MultiCell(205.9,3,"PERIODO:".$meses[$item["periodo"]["mes"]-1]." ".$item["periodo"]["ano"],'0','R');
				$y+=5;
				$this->Line(5, 25, $ancho+5, 25);
				$this->SetFont('courier','B',10);
				$this->setXY(5,$y);$this->MultiCell(30,5,"Nombre:",'1','R');
				$this->setXY(35,$y);$this->MultiCell(80,5,$item['trabajador']['appat']." ".$item['trabajador']['apmat']." ".$item['trabajador']['nomb'],'1','L');
				$this->setXY(150,$y);$this->MultiCell(40,5,"Fecha:",'1','R');
				$this->setXY(190,$y);$this->MultiCell(40,5,Date::format($item['fecreg']->sec, 'd/m/Y'),'0','L');
				$y+=5;
				$this->setXY(5,$y);$this->MultiCell(40,5,"DNI:",'0','L');
				$this->setXY(90,$y);$this->MultiCell(40,5,"CUI AFP:",'0','L');
				$this->setXY(15,$y);$this->MultiCell(70,5,$item['trabajador']['docident'][0]['num'],'0','L');
				$cod_aportante = '--';
				if(isset($item['trabajador']['roles']['trabajador']['cod_aportante'])){
					$cod_aportante = $item['trabajador']['roles']['trabajador']['cod_aportante'];
				}
				$this->setXY(110,$y);$this->MultiCell(40,5,$cod_aportante,'0','L');
				$y+=5;
				$this->SetLineWidth(0.5);
				$this->SetFillColor(255);
				$this->RoundedRect(5, $y, $ancho/3, 5, 1, 'DF');
				$this->setXY(5,$y);$this->MultiCell($ancho/3,5,"Pagos",'0','C');
				$this->setXY(5+($ancho/3),$y);$this->MultiCell($ancho/3,5,"Descuentos",'1','C');
				$this->setXY(5+(2*$ancho/3),$y);$this->MultiCell($ancho/3,5,"Patronal",'1','C');
				$y+=5;
				$ancho = 205.9/3;
				$ancho_monto = 20;
				$ancho_nomb = $ancho-$ancho_monto;
				$y_1 = $y;
				$alto = 2.7;
				for($i=0;$i<count($item['conceptos']);$i++){
					if(isset($item['conceptos'][$i]['concepto']['imprimir'])){
						if(floatval($item['conceptos'][$i]['concepto']['imprimir'])==1||(floatval($item['conceptos'][$i]['concepto']['imprimir']==2&&floatval($item['conceptos'][$i]['subtotal'])>0))){
							if($item['conceptos'][$i]['concepto']['tipo']=="P"/*&&$item['conceptos'][$i]['subtotal']!="0.00"*/){
								$this->setXY(5+$ancho_nomb,$y_1);$this->MultiCell($ancho_monto,$alto,number_format($item['conceptos'][$i]['subtotal'],2),'0','R');
								$this->setXY(5,$y_1);$this->MultiCell($ancho_nomb+10,$alto,$item['conceptos'][$i]['concepto']['nomb'],'0','L');
								$y_1 = $this->GetY();
							}
						}
					}
				}
				//$this->Line(5, 45, $x2, $y2);
				$y_2 = $_y+35;
				for($i=0;$i<count($item['conceptos']);$i++){
					if(floatval($item['conceptos'][$i]['concepto']['imprimir'])==1||(floatval($item['conceptos'][$i]['concepto']['imprimir']==2&&floatval($item['conceptos'][$i]['subtotal'])>0))){
						if($item['conceptos'][$i]['concepto']['tipo']=="D"/*&&$item['conceptos'][$i]['subtotal']!="0.00"*/){
							$this->setXY($ancho+5+$ancho_nomb,$y_2);$this->MultiCell($ancho_monto,$alto,number_format($item['conceptos'][$i]['subtotal'],2),'0','R');
							$this->setXY($ancho+5,$y_2);$this->MultiCell($ancho_nomb,$alto,$item['conceptos'][$i]['concepto']['nomb'],'0','L');
							$y_2 = $this->GetY();
						}
					}
				}
				$y_3 = $_y+35;
				for($i=0;$i<count($item['conceptos']);$i++){
					if(floatval($item['conceptos'][$i]['concepto']['imprimir'])==1||(floatval($item['conceptos'][$i]['concepto']['imprimir']==2&&floatval($item['conceptos'][$i]['subtotal'])>0))){
						if($item['conceptos'][$i]['concepto']['tipo']=="A"/*&&$item['conceptos'][$i]['subtotal']!="0.00"*/){
							$this->setXY(2*$ancho+5+$ancho_nomb,$y_3);$this->MultiCell($ancho_monto,$alto,number_format($item['conceptos'][$i]['subtotal'],2),'0','R');
							$this->setXY(2*$ancho+5,$y_3);$this->MultiCell($ancho_nomb,$alto,$item['conceptos'][$i]['concepto']['nomb'],'0','L');
							$y_3 = $this->GetY();
						}
					}
				}
				if($y_1>$y_2){
					$y_t = $y_1;
				}else {
					$y_t = $y_2;
				}
				if($y_t<75){
					$y_t = $_y+$y_3+25;
				}
				$this->setXY(5,$y_t);$this->MultiCell($ancho_nomb,$alto,"TOTAL PAGOS",'0','C');
				$this->setXY(5+$ancho_nomb,$y_t);$this->MultiCell($ancho_monto,$alto,$item['total_pago'],'0','R');
				$this->setXY($ancho+5,$y_t);$this->MultiCell($ancho_nomb,$alto,"TOTAL DESCUENTOS",'0','C');
				$this->setXY($ancho+5+$ancho_nomb,$y_t);$this->MultiCell($ancho_monto,$alto,$item['total_desc'],'0','R');
				$this->setXY(2*$ancho+5,$y_t-20);$this->MultiCell($ancho_nomb,$alto,"TOTAL PATRONAL",'0','C');
				$this->setXY(2*$ancho+5+$ancho_nomb,$y_t-20);$this->MultiCell($ancho_monto,$alto,$item['total_apor'],'0','R');

				$this->setXY(2*$ancho+5,$y_t-10);$this->MultiCell($ancho_nomb,$alto,"NETO             S/.",'0','L');
				$this->setXY(2*$ancho+5+$ancho_nomb,$y_t-10);$this->MultiCell($ancho_monto,$alto,$item['total'],'0','R');
				$this->setXY(2*$ancho+5,$y_t-5);$this->MultiCell($ancho_nomb,$alto,"REDONDEO         S/.",'0','L');
				$redondeo = $item['redondeo'];
				if(isset($item['redondeo_dif'])){
					if(floatval($item['redondeo_dif'])==0){
						$redondeo = "(".$item['redondeo'].")";
					}
				}
				$this->setXY(2*$ancho+5+$ancho_nomb,$y_t-5);$this->MultiCell($ancho_monto,$alto,$redondeo,'0','R');
				$this->setXY(2*$ancho+5,$y_t);$this->MultiCell($ancho_nomb,$alto,"NETO A PAGAR     S/.",'0','L');
				$this->setXY(2*$ancho+5+$ancho_nomb,$y_t);$this->MultiCell($ancho_monto,$alto,$item['neto'],'0','R');
				//$this->Rect(161, $y_comp, 40, 8);$this->setXY(161,$y_comp);$this->MultiCell(40,2,$comp["cod"]." ".$comp["nomb"],'0','L');
				$count++;
				if($count%2==0){
					$this->AddPage();
					$y = $y_ini;
				}else{
					$y = 140;
				}
			}
		}
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