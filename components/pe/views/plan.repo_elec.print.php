<?php
global $f;
$f->library('pdf');

class bole extends FPDF{
		var $javascript;
	var $n_js;
	function IncludeJS($script){
		$this->javascript=$script;
	}
	function _putjavascript(){
		$this->_newobj();
		$this->n_js=$this->n;
		$this->_out('<<');
		$this->_out('/Names [(EmbeddedJS) '.($this->n+1).' 0 R]');
		$this->_out('>>');
		$this->_out('endobj');
		$this->_newobj();
		$this->_out('<<');
		$this->_out('/S /JavaScript');
		$this->_out('/JS '.$this->_textstring($this->javascript));
		$this->_out('>>');
		$this->_out('endobj');
	}
	function _putresources(){
		parent::_putresources();
		if (!empty($this->javascript)) {
			$this->_putjavascript();
		}
	}
	function _putcatalog(){
		parent::_putcatalog();
		if (!empty($this->javascript)) {
			$this->_out('/Names <</JavaScript '.($this->n_js).' 0 R>>');
		}
	}
	function AutoPrint($dialog=false){
	    //Open the print dialog or start printing immediately on the standard printer
	    $param=($dialog ? 'true' : 'false');
	    $script="print($param);";
	    $this->IncludeJS($script);
	}
	function AutoPrintToPrinter($server, $printer, $dialog=false){
	    //Print on a shared printer (requires at least Acrobat 6)
	    $script = "var pp = getPrintParams();";
	    if($dialog)
	        $script .= "pp.interactive = pp.constants.interactionLevel.full;";
	    else
	        $script .= "pp.interactive = pp.constants.interactionLevel.automatic;";
	    $script .= "pp.printerName = '\\\\\\\\".$server."\\\\".$printer."';";
	    $script .= "print(pp);";
	    $this->IncludeJS($script);
	}
	var $items;
	function Header(){
		
	}
	function setData($items, $header, $planilla){
		$this->items = $items;
	}
	function Publicar($items, $header, $planilla, $ancho){
		$ancho-=10;
		$count = 0;
		$x=0;
		$y=5;//41
		$y_ini = $y;
		$y_marg = 5;

		$widths = array(8,28,12,25,12,0,14,20);
		$width_acu = array();
		$tmp_acu = 0;
		foreach ($widths as $key => $value) {
			$width_acu[] = $tmp_acu;
			$tmp_acu += $value;
		}
		$faltante = 297 - $tmp_acu - 10;

		$meses = array("--","ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
		if($items!=null){
			foreach($items as $prog){
				$cantidad_conc = sizeof($header[$prog['programa']['_id']->{'$id'}]);
				$widths[5] = $faltante / $cantidad_conc;
				$this->AddPage();
				$y = 5;
				//$ancho = 205.9;
				$this->Image(IndexPath.DS.'images/logo.jpg',5,5,15,16);
				$this->SetFont('arial','B',8);
				$this->setXY(20,$y);$this->MultiCell($ancho/2,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','L');
				$y+=5;
				$this->SetFont('arial','',8);
				$this->setXY(20,$y);$this->MultiCell($ancho/2,5,"Dirección: Calle Pierola 201",'0','L');
				$this->setXY(5,$y-5);$this->MultiCell($ancho,5,"RUC: 20120",'0','R');
				$this->setXY(5,$y);$this->MultiCell($ancho,5,"REG. PATRONAL: 20120",'0','R');
				$y+=5;
				$this->SetFont('arial','B',12);
				$this->setXY(42,$y);$this->MultiCell($ancho-80,5,$planilla["nomb"]." / ".$prog['programa']['nomb'],'0','C');
				$y = $this->GetY();
				$this->SetFont('arial','',10);
				$this->setXY(5,$y);$this->MultiCell($ancho,5,"CORRESPONDIENTE AL MES DE ".$meses[intval($planilla["periodo"]["mes"])]." ".$planilla["periodo"]["ano"],'0','C');
				$y+=10;
				$this->SetFont('arial','B',6);

				$this->setXY($y_ini+$width_acu[0],$y);$this->MultiCell($widths[0],10,"Nro.",'0','C');
				$this->setXY($y_ini+$width_acu[1],$y);$this->MultiCell($widths[1],10,"Apellidos y Nombres",'0','C');
				$this->setXY($y_ini+$width_acu[2],$y);$this->MultiCell($widths[2],10,"DNI",'0','C');
				$this->setXY($y_ini+$width_acu[3],$y);$this->MultiCell($widths[3],10,"CARGO",'0','C');
				$this->setXY($y_ini+$width_acu[4],$y);$this->MultiCell($widths[4],10,"AFP",'0','C');
				$x_c = $width_acu[5]+5;
				$y_max_row = $y+10;
				foreach($header[$prog['programa']['_id']->{'$id'}] as $head){
					$this->setXY($x_c,$y);$this->MultiCell($widths[5],5,$head['concepto']['nomb'],'0','C');
					if($this->GetY()>$y_max_row) $y_max_row=$this->GetY();
					$x_c += $widths[5];
				}
				$this->setXY($x_c,$y);$this->MultiCell($widths[6],5,"NETO A PAGAR",'0','C');
				$x_c += $widths[6];
				$this->setXY($x_c,$y);$this->MultiCell($widths[7],5,"FIRMA",'0','C');
				$x_c += $widths[7];
				$x_final = $x_c;
				$this->Line(5,$y, $x_c, $y);
				$y_start = $y;
				$y = $y_max_row;
				$this->Line(5,$y, $x_c, $y);
				$count = 1;
				$this->SetFont('arial','',6);
				$totales = array();
				$neto_pagar = 0;
				$nueva_pagina = 0;
				foreach($prog['trabajadores'] as $item){
					if($y>190){
						$nueva_pagina = 1;
						$this->Line($y_ini+$width_acu[0],$y_start, $y_ini+$width_acu[0], $y);
						$this->Line($y_ini+$width_acu[1],$y_start, $y_ini+$width_acu[1], $y);
						$this->Line($y_ini+$width_acu[2],$y_start, $y_ini+$width_acu[2], $y);
						$this->Line($y_ini+$width_acu[3],$y_start, $y_ini+$width_acu[3], $y);
						$this->Line($y_ini+$width_acu[4],$y_start, $y_ini+$width_acu[4], $y);
						$x_c = $width_acu[5]+5;
						$k = 0;
						foreach($header[$prog['programa']['_id']->{'$id'}] as $head){
							$this->Line($x_c,$y_start, $x_c, $y);
							$x_c += $widths[5];
							$k++;
						}
						$this->Line($x_c,$y_start, $x_c, $y);
						$x_c += $widths[6];
						$this->Line($x_c,$y_start, $x_c, $y);
						$x_c += $widths[7];
						$this->Line($x_c,$y_start, $x_c, $y);
						$this->addPage();
						$y = 15;
					}
					$y_max_row = $y+10;
					$dni = $item['trabajador']['docident'][0]['num'];
					if(isset($item['trabajador']['cargo']['funcion']))
						$cargo = $item['trabajador']['cargo']['funcion'];
					else
						$cargo = $item['trabajador']['cargo']['nomb'];
					$sistema_pension = $item['pension']['nomb'];

					$comision = '';
					if(isset($item['pension'])){
						if($item['pension']['tipo']=='D.L. 25897'){
							$comision = ' ('.$item['comision'].')';
						}
					}
					$this->setXY($y_ini+$width_acu[0],$y);$this->MultiCell($widths[0],5,$count,'0','L');if($this->GetY()>$y_max_row) $y_max_row=$this->GetY();
					$this->setXY($y_ini+$width_acu[1],$y);$this->MultiCell($widths[1],5,$item['trabajador']['appat'].' '.$item['trabajador']['apmat'].' '.$item['trabajador']['nomb'],'0','L');if($this->GetY()>$y_max_row) $y_max_row=$this->GetY();
					$this->setXY($y_ini+$width_acu[2],$y);$this->MultiCell($widths[2],5,$dni,'0','L');if($this->GetY()>$y_max_row) $y_max_row=$this->GetY();
					$this->setXY($y_ini+$width_acu[3],$y);$this->MultiCell($widths[3],5,$cargo,'0','L');if($this->GetY()>$y_max_row) $y_max_row=$this->GetY();
					$this->setXY($y_ini+$width_acu[4],$y);$this->MultiCell($widths[4],5,$sistema_pension.$comision,'0','L');if($this->GetY()>$y_max_row) $y_max_row=$this->GetY();
					$x_c = $width_acu[5]+5;
					foreach($item['totales'] as $cod=>$tota){
						if(!isset($totales[$cod])){
							$totales[$cod] = 0;
						}
						$this->setXY($x_c,$y);$this->MultiCell($widths[5],10,number_format($tota['importe'],2),'0','R');
						$totales[$cod]+=floatval($tota['importe']);
						$x_c += $widths[5];
					}
					//$item['total'];
					$neto_pagar+=$item['neto'];
					$this->setXY($x_c,$y);$this->MultiCell($widths[6],10,number_format($item['neto'],2),'0','R');
					$x_c += 60;

					$count++;
					$y = $y_max_row;
					$this->Line(5,$y, $x_final, $y);
				}
				$totales = array_values($totales);
				//
				//$y+=10;
				if($nueva_pagina>0){
					$y_start=15;
					$this->Line(5,$y_start, $x_final, $y_start);
				}
				$this->setXY(5,$y);$this->MultiCell($widths[0]+$widths[1]+$widths[2]+$widths[3]+$widths[4],10,"TOTAL",'','C');
				$this->Line($y_ini+$width_acu[0],$y_start, $y_ini+$width_acu[0], $y+10);
				$this->Line($y_ini+$width_acu[1],$y_start, $y_ini+$width_acu[1], $y);
				$this->Line($y_ini+$width_acu[2],$y_start, $y_ini+$width_acu[2], $y);
				$this->Line($y_ini+$width_acu[3],$y_start, $y_ini+$width_acu[3], $y);
				$this->Line($y_ini+$width_acu[4],$y_start, $y_ini+$width_acu[4], $y);
				$x_c = $width_acu[5]+5;
				$k = 0;
				foreach($header[$prog['programa']['_id']->{'$id'}] as $head){
					$this->Line($x_c,$y_start, $x_c, $y+10);
					$this->setXY($x_c,$y);$this->MultiCell($widths[5],10,number_format($totales[$k],2),'0','R');
					$x_c += $widths[5];
					$k++;
				}
				$this->Line($x_c,$y_start, $x_c, $y+10);
				$this->setXY($x_c,$y);$this->MultiCell($widths[6],10,number_format($neto_pagar,2),'0','R');
				$x_c += $widths[6];
				$this->Line($x_c,$y_start, $x_c, $y+10);
				$x_c += $widths[7];
				$this->Line($x_c,$y_start, $x_c, $y+10);
				$y+=10;
				$this->Line(5,$y, $x_c, $y);
				$aux++;
			}
		}
	}
	function Footer(){
		//footer
	} 
	 
}

$ancho = 140;
$ancho+=count($header)*25+60;
//$pdf=new bole('P','mm',array($ancho,297.1));
$pdf=new bole('L','mm',array(210,297));
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("Planilla");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
//$pdf->AddPage();
$pdf->setData($items, $header, $planilla);
$ancho = 297;
$pdf->Publicar($items, $header, $planilla, $ancho);
$print = true;
if($print==true){
	$pdf->AutoPrint(true);
}
$pdf->SetLeftMargin(25);
$pdf->Output();
?>