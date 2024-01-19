<?php
global $f;
$f->library('pdf');
class repo extends FPDF{
	var $recibo;
	var $x_col_1;
	var $x_col_2;
	var $x_col_3;
	var $x_col_4;
	var $x_col_5;
	var $x_col_6;
	var $x_col_7;
	function Filter($filtros){
		$this->recibo = $filtros;
	}
	function Header(){
		$tipo_modulo = '';
		switch($this->recibo['modulo']){
			case "IN":
				$this->x_col_1 = 5;
				$this->x_col_2 = 30;
				$this->x_col_3 = 55;
				$this->x_col_4 = 75;
				$this->x_col_5 = 130;
				$this->x_col_6 = 140;
				$this->x_col_7 = 155;
				$this->x_col_8 = 170;

				//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);
				//$this->SetFont('Arial','B',14);
				////$this->setXY(170,10);$this->MultiCell(20,5,$this->recibo['cod'],'0','C');
				//$this->SetFont('Arial','',10);
				if($this->recibo['tipo_inm']=='P')
					$size_font = 9;
				else
					$size_font = 9;
				$this->SetFont('Arial','B',14);
				$this->SetFont('Arial','',$size_font);
				$y=27;
				$this->setXY(10,$y);$this->MultiCell(10,5,date('d', $this->recibo['fec']->sec),'0','C');
				$this->setXY(20,$y);$this->MultiCell(10,5,date('m', $this->recibo['fec']->sec),'0','C');
				$this->setXY(30,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fec']->sec),'0','C');
				$fecfin = strtotime(date("Y-m-d",$this->recibo['fec']->sec));
				$y=$this->GetY();
				if(isset($this->recibo['fecfin'])){
					if($this->recibo['fec']!=$this->recibo['fecfin']){
						$lock = false;
						while ($lock==false) {
							$fecfin = strtotime(date("Y-m-d", $fecfin) . " +1 days");
							if(date('Y-m-d', $this->recibo['fecfin']->sec)==date('Y-m-d', $fecfin)){
								$lock = true;
							}
							$this->setXY(30,$y);$this->MultiCell(10,5,date('d', $fecfin),'0','C');
							$this->setXY(40,$y);$this->MultiCell(10,5,date('m', $fecfin),'0','C');
							$this->setXY(50,$y);$this->MultiCell(10,5,date('Y', $fecfin),'0','C');
							$y=$this->GetY();
						}
					}
				}
				$this->SetFont('Arial','B',14);
				$this->SetFont('Arial','',$size_font);
				$y=27;
				$this->setXY(166,$y);$this->MultiCell(10,5,date('d', $this->recibo['fec']->sec),'0','C');
				$this->setXY(176,$y);$this->MultiCell(10,5,date('m', $this->recibo['fec']->sec),'0','C');
				$this->setXY(186,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fec']->sec),'0','C');
				$fecfin = strtotime(date("Y-m-d",$this->recibo['fec']->sec));
				$y=$this->GetY();
				if(isset($this->recibo['fecfin'])){
					if($this->recibo['fec']!=$this->recibo['fecfin']){
						$lock = false;
						while ($lock==false) {
							$fecfin = strtotime(date("Y-m-d", $fecfin) . " +1 days");
							if(date('Y-m-d', $this->recibo['fecfin']->sec)==date('Y-m-d', $fecfin)){
								$lock = true;
							}
							$this->setXY(166,$y);$this->MultiCell(10,5,date('d', $fecfin),'0','C');
							$this->setXY(176,$y);$this->MultiCell(10,5,date('m', $fecfin),'0','C');
							$this->setXY(186,$y);$this->MultiCell(10,5,date('Y', $fecfin),'0','C');
							$y=$this->GetY();
						}
					}
				}
				break;
			case "MH":
				$this->x_col_1 = 10;
				$this->x_col_2 = 35;
				$this->x_col_3 = 60;
				$this->x_col_4 = 94;
				$this->x_col_5 = 128;
				$this->x_col_6 = 149;
				$this->x_col_7 = 168;
				$this->x_col_8 = 175;
				//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);
				$this->SetFont('Arial','B',14);
				//$this->setXY(15,$y_anul);$this->MultiCell(150,$height,$tmp_anul,'0','L');
				//$this->setXY(15,$y_anul);$this->MultiCell(150,$height,$tmp_anul,'0','L');
				//$this->setXY(110,10);$this->MultiCell(20,5,$modulo[$this->recibo['modulo']],'0','C');
				$this->SetFont('Arial','',10);
				$y=27;
				$this->setXY(30,$y);$this->MultiCell(10,5,date('d', $this->recibo['fec']->sec),'0','C');
				$this->setXY(40,$y);$this->MultiCell(10,5,date('m', $this->recibo['fec']->sec),'0','C');
				$this->setXY(50,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fec']->sec),'0','C');
				if(isset($this->recibo['fecfin'])){
					if($this->recibo['fec']!=$this->recibo['fecfin']){
						$this->setXY(166,$y);$this->MultiCell(10,5,date('d', $this->recibo['fecfin']->sec),'0','C');
						$this->setXY(176,$y);$this->MultiCell(10,5,date('m', $this->recibo['fecfin']->sec),'0','C');
						$this->setXY(186,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fecfin']->sec),'0','C');
					}else{
						$this->setXY(166,$y);$this->MultiCell(10,5,date('d', $this->recibo['fec']->sec),'0','C');
						$this->setXY(176,$y);$this->MultiCell(10,5,date('m', $this->recibo['fec']->sec),'0','C');
						$this->setXY(186,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fec']->sec),'0','C');
					}
				}
				break;
			case "AD":
				$this->x_col_1 = 10;
				$this->x_col_2 = 35;
				$this->x_col_3 = 60;
				$this->x_col_4 = 94;
				$this->x_col_5 = 128;
				$this->x_col_6 = 149;
				$this->x_col_7 = 168;
				$this->x_col_8 = 175;
				//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);
				$this->SetFont('Arial','B',14);
				//$this->setXY(170,10);$this->MultiCell(20,5,$this->recibo['cod'],'0','C');
				if($this->recibo['modulo'] == 'AD'){
					$tipo_modulo = 'ADICCIONES';
					$this->SetFont('Arial','B',12);
					$this->setXY(95,27);$this->MultiCell(30,5,$tipo_modulo,'0','C');
				}
				$this->SetFont('Arial','',10);
				$y=27;
				$this->setXY(30,$y);$this->MultiCell(10,5,date('d', $this->recibo['fec']->sec),'0','C');
				$this->setXY(40,$y);$this->MultiCell(10,5,date('m', $this->recibo['fec']->sec),'0','C');
				$this->setXY(50,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fec']->sec),'0','C');
				if(isset($this->recibo['fecfin'])){
					if($this->recibo['fec']!=$this->recibo['fecfin']){
						$this->setXY(166,$y);$this->MultiCell(10,5,date('d', $this->recibo['fecfin']->sec),'0','C');
						$this->setXY(176,$y);$this->MultiCell(10,5,date('m', $this->recibo['fecfin']->sec),'0','C');
						$this->setXY(186,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fecfin']->sec),'0','C');
					}else{
						$this->setXY(166,$y);$this->MultiCell(10,5,date('d', $this->recibo['fec']->sec),'0','C');
						$this->setXY(176,$y);$this->MultiCell(10,5,date('m', $this->recibo['fec']->sec),'0','C');
						$this->setXY(186,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fec']->sec),'0','C');
					}
				}
				break;
			case "LM":
				$this->x_col_1 = 10;
				$this->x_col_2 = 35;
				$this->x_col_3 = 60;
				$this->x_col_4 = 94;
				$this->x_col_5 = 128;
				$this->x_col_6 = 149;
				$this->x_col_7 = 168;
				$this->x_col_8 = 175;
				//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);
				$this->SetFont('Arial','B',14);
				//$this->setXY(170,10);$this->MultiCell(20,5,$this->recibo['cod'],'0','C');
				if($this->recibo['modulo'] == 'LM'){
					$tipo_modulo = 'LABORATORIO';
					$this->SetFont('Arial','B',10);
					$this->setXY(95,27);$this->MultiCell(30,5,$tipo_modulo,'0','C');
				}
				$this->SetFont('Arial','',10);
				$y=27;
				$this->setXY(30,$y);$this->MultiCell(10,5,date('d', $this->recibo['fec']->sec),'0','C');
				$this->setXY(40,$y);$this->MultiCell(10,5,date('m', $this->recibo['fec']->sec),'0','C');
				$this->setXY(50,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fec']->sec),'0','C');
				if(isset($this->recibo['fecfin'])){
					if($this->recibo['fec']!=$this->recibo['fecfin']){
						$this->setXY(166,$y);$this->MultiCell(10,5,date('d', $this->recibo['fecfin']->sec),'0','C');
						$this->setXY(176,$y);$this->MultiCell(10,5,date('m', $this->recibo['fecfin']->sec),'0','C');
						$this->setXY(186,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fecfin']->sec),'0','C');
					}else{
						$this->setXY(166,$y);$this->MultiCell(10,5,date('d', $this->recibo['fec']->sec),'0','C');
						$this->setXY(176,$y);$this->MultiCell(10,5,date('m', $this->recibo['fec']->sec),'0','C');
						$this->setXY(186,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fec']->sec),'0','C');
					}
				}
				break;
				case "TD":
				$this->x_col_1 = 10;
				$this->x_col_2 = 35;
				$this->x_col_3 = 60;
				$this->x_col_4 = 94;
				$this->x_col_5 = 128;
				$this->x_col_6 = 149;
				$this->x_col_7 = 168;
				$this->x_col_8 = 175;
				//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);
				$this->SetFont('Arial','B',14);
				//$this->setXY(170,10);$this->MultiCell(20,5,$this->recibo['cod'],'0','C');
				if($this->recibo['modulo'] == 'TD'){
					$tipo_modulo = 'TURNO TARDE';
					$this->SetFont('Arial','B',10);
					$this->setXY(95,27);$this->MultiCell(30,5,$tipo_modulo,'0','C');
				}
				$this->SetFont('Arial','',10);
				$y=27;
				$this->setXY(30,$y);$this->MultiCell(10,5,date('d', $this->recibo['fec']->sec),'0','C');
				$this->setXY(40,$y);$this->MultiCell(10,5,date('m', $this->recibo['fec']->sec),'0','C');
				$this->setXY(50,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fec']->sec),'0','C');
				if(isset($this->recibo['fecfin'])){
					if($this->recibo['fec']!=$this->recibo['fecfin']){
						$this->setXY(166,$y);$this->MultiCell(10,5,date('d', $this->recibo['fecfin']->sec),'0','C');
						$this->setXY(176,$y);$this->MultiCell(10,5,date('m', $this->recibo['fecfin']->sec),'0','C');
						$this->setXY(186,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fecfin']->sec),'0','C');
					}else{
						$this->setXY(166,$y);$this->MultiCell(10,5,date('d', $this->recibo['fec']->sec),'0','C');
						$this->setXY(176,$y);$this->MultiCell(10,5,date('m', $this->recibo['fec']->sec),'0','C');
						$this->setXY(186,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fec']->sec),'0','C');
					}
				}
				break;
			case "FA":
			$this->x_col_1 = 10;
			$this->x_col_2 = 35;
			$this->x_col_3 = 60;
			$this->x_col_4 = 94;
			$this->x_col_5 = 128;
			$this->x_col_6 = 149;
			$this->x_col_7 = 168;
			$this->x_col_8 = 175;
			//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);
			$this->SetFont('Arial','B',14);
			//$this->setXY(15,$y_anul);$this->MultiCell(150,$height,$tmp_anul,'0','L');
			//$this->setXY(15,$y_anul);$this->MultiCell(150,$height,$tmp_anul,'0','L');
			//$this->setXY(110,10);$this->MultiCell(20,5,$modulo[$this->recibo['modulo']],'0','C');
			$this->SetFont('Arial','',10);
			$y=27;
			$this->setXY(30,$y);$this->MultiCell(10,5,date('d', $this->recibo['fec']->sec),'0','C');
			$this->setXY(40,$y);$this->MultiCell(10,5,date('m', $this->recibo['fec']->sec),'0','C');
			$this->setXY(50,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fec']->sec),'0','C');
			if(isset($this->recibo['fecfin'])){
				if($this->recibo['fec']!=$this->recibo['fecfin']){
					$this->setXY(166,$y);$this->MultiCell(10,5,date('d', $this->recibo['fecfin']->sec),'0','C');
					$this->setXY(176,$y);$this->MultiCell(10,5,date('m', $this->recibo['fecfin']->sec),'0','C');
					$this->setXY(186,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fecfin']->sec),'0','C');
				}else{
					$this->setXY(166,$y);$this->MultiCell(10,5,date('d', $this->recibo['fec']->sec),'0','C');
					$this->setXY(176,$y);$this->MultiCell(10,5,date('m', $this->recibo['fec']->sec),'0','C');
					$this->setXY(186,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fec']->sec),'0','C');
				}
			}
			break;
		}
	}
	function fillBody($detalle){

	}
	function Publicar($items){
		
		$y = 54;
		$y_ini = $y;
		$height = 3;
		$cta = '';
		$cta_y = 0;
		$cta_tot = 0;
		$total = $this->recibo['total'];
		/*
		 * DETALLE PARA RECIBO DE INGRESOS DE MOISES HERESI
		 */
		//print_r($items);
		$total_partial = 0;
		$total_mayor = 0;
		$last_cuenta = array();
		$last_mayor = array();
		$y_tmp_partial = 0;
		foreach ($items as $i=>$detalle) {
			if(isset($detalle['hidden']) && $detalle['hidden']==true){
				if(isset($detalle['partial']) && $detalle['partial']==true){
					$total_partial+= $detalle['partial_monto'];
				}
				continue;
			}
			if($y>175){
				$this->AddPage();
				$y=$y_ini;
			}
			$y_max_row = $y;
			$opt1 = $detalle['col_1']['opt'];
			$opt2 = $detalle['col_2']['opt'];
			$opt3 = $detalle['col_3']['opt'];
			$opt4 = $detalle['col_4']['opt'];
			$opt5 = $detalle['col_5']['opt'];
			$opt6 = $detalle['col_6']['opt'];
			$opt7 = $detalle['col_7']['opt'];
			$opt8 = $detalle['col_8']['opt'];

			$col_1_h = $y;
			$col_2_h = $y;
			$col_3_h = $y;
			$col_4_h = $y;
			$col_5_h = $y;
			$col_6_h = $y;
			$col_7_h = $y;
			$col_8_h = $y;

			if($detalle['col_1']['value']!=''){
				$this->SetFont('Arial',$detalle['col_1']['opt']['type'],$detalle['col_1']['opt']['size']);
				$col_1_h_=ceil($this->GetStringWidth($detalle['col_1']['value'])/$opt1['w']);
				$col_1_h+=$col_1_h_*4;
				$this->setXY($this->x_col_1,$y);$this->MultiCell($detalle['col_1']['opt']['w'],4,$detalle['col_1']['value'],'0',$opt1['align']);
			}
			if($detalle['col_2']['value']!=''){
				$this->SetFont('Arial',$detalle['col_2']['opt']['type'],$detalle['col_2']['opt']['size']);
				$col_2_h_=ceil($this->GetStringWidth($detalle['col_2']['value'])/$opt2['w']);
				$col_2_h+=$col_2_h_*4;
				$this->setXY($this->x_col_2,$y);$this->MultiCell($detalle['col_2']['opt']['w'],4,$detalle['col_2']['value'],'0',$opt2['align']);
			}
			if($detalle['col_3']['value']!=''){
				$this->SetFont('Arial',$detalle['col_3']['opt']['type'],$detalle['col_3']['opt']['size']);
				$col_3_h_=ceil($this->GetStringWidth($detalle['col_3']['value'])/$opt3['w']);
				$col_3_h+=$col_3_h_*4;
				$this->setXY($this->x_col_3,$y);$this->MultiCell($detalle['col_3']['opt']['w'],4,$detalle['col_3']['value'],'0',$opt3['align']);
			}
			/*En caso de COL_4 es un string y no un array*/
			if(!is_array($detalle['col_4']['value']) && $detalle['col_4']['value']!=''){
				$this->SetFont('Arial',$detalle['col_4']['opt']['type'],$detalle['col_4']['opt']['size']);
				$col_4_h_=ceil(($this->GetStringWidth($detalle['col_4']['value'])+2)/$opt4['w']);
				$col_4_h+=$col_4_h_*4;
				$this->setXY($this->x_col_4,$y);$this->MultiCell($detalle['col_4']['opt']['w'],4,$detalle['col_4']['value'],'0',$opt4['align']);
			}
			/*En caso de COL_4 es un array*/
			if(is_array($detalle['col_4']['value'])){
				$y_temp = $y;
				foreach ($detalle['col_4']['value'] as $s => $array_sunat) {
					if($s==0){
						$this->SetFont('Arial',$detalle['col_4']['opt']['type'],$detalle['col_4']['opt']['size']);
						$col_4_h_=ceil(($this->GetStringWidth($array_sunat)+2)/$opt4['w']);
						$col_4_h+=$col_4_h_*4;
						$this->setXY($this->x_col_4,$y_temp);$this->MultiCell($detalle['col_4']['opt']['w'],4,$array_sunat,'0',$opt4['align']);
						if($s<count($detalle['col_4']['value']))$y_temp = $col_4_h;
					}
					else{
						$this->SetFont('Arial',$detalle['col_4']['opt']['type'],$detalle['col_4']['opt']['size']);
						$col_4_h_=ceil(($this->GetStringWidth($array_sunat)+2)/$opt4['w']);
						$col_4_h+=$col_4_h_*4;
						$this->setXY($this->x_col_4,$y_temp);$this->MultiCell($detalle['col_4']['opt']['w'],4,$array_sunat,'0',$opt4['align']);
						if($s<count($detalle['col_4']['value']))$y_temp = $col_4_h;
						//if(($s+1)<count($detalle['col_4']['value']))$col_4_h+=$col_4_h_*4;
						//if($s<count($detalle['col_4']['value']))$col_4_h+=$col_4_h_*4;
						//$y_temp = $col_4_h;
					}
				}
				//$col_4_h+=$col_4_h_*4;
			}
			if($detalle['col_5']['value']!=''){
				$this->SetFont('Arial',$detalle['col_5']['opt']['type'],$detalle['col_5']['opt']['size']);
				$col_5_h_=ceil($this->GetStringWidth($detalle['col_5']['value'])/$opt5['w']);
				$col_5_h+=$col_5_h_*4;
				$this->setXY($this->x_col_5,$y);$this->MultiCell($detalle['col_5']['opt']['w'],4,$detalle['col_5']['value'],'0',$opt5['align']);
			}
			if($detalle['col_6']['value']!=''){
				$this->SetFont('Arial',$detalle['col_6']['opt']['type'],$detalle['col_6']['opt']['size']);
				$col_6_h_=ceil($this->GetStringWidth($detalle['col_6']['value'])/$opt6['w']);
				$col_6_h+=$col_6_h_*4;
				$this->setXY($this->x_col_6,$y);$this->MultiCell($detalle['col_6']['opt']['w'],4,$detalle['col_6']['value'],'0',$opt6['align']);
			}
			if($detalle['col_7']['value']!=''){
				//if(!isset($detalle['cuenta'])){
					$this->SetFont('Arial',$detalle['col_7']['opt']['type'],$detalle['col_7']['opt']['size']);
					$col_7_h_=ceil($this->GetStringWidth($detalle['col_7']['value'])/$opt7['w']);
					$col_7_h+=$col_7_h_*4;
					$this->setXY($this->x_col_7,$y);$this->MultiCell($detalle['col_7']['opt']['w'],4,$detalle['col_7']['value'],'0',$opt7['align']);
				//}
			}
			if($detalle['col_8']['value']!=''){
				$this->SetFont('Arial',$detalle['col_8']['opt']['type'],$detalle['col_8']['opt']['size']);
				$col_8_h_=ceil($this->GetStringWidth($detalle['col_8']['value'])/$opt8['w']);
				$col_8_h+=$col_8_h_*4;
				$this->setXY($this->x_col_8,$y);$this->MultiCell($detalle['col_8']['opt']['w'],4,$detalle['col_8']['value'],'0',$opt6['align']);
			}	
			if(isset($detalle['cuenta']) && $detalle['cuenta']==true){
				//print_r($detalle);die();
				/*if($last_mayor['cuenta_detalle']!=$detalle['cuenta_detalle']){
					//$y_tmp = $this->getY();
					//echo $total_partial.'<br />';
					if($y_tmp_partial>0&&isset($last_cuenta['hide_ammount'])){
						$this->setXY(168,$y_tmp_partial);$this->MultiCell($last_cuenta['col_7']['opt']['w'],4,number_format($total_partial,2),'0',$last_cuenta['col_7']['opt']['align']);
					}
				}*/
				$last_cuenta = $detalle;
				$total_partial = 0;
				$y_tmp_partial = $y;
				//echo $detalle['cuenta_detalle'].'<br />';

				if(strlen($detalle['cuenta_detalle'])==4){
					$last_mayor = $detalle;
					//$total_mayor = 0;
				}
			}

			$y = max($col_1_h, $col_2_h, $col_3_h, $col_4_h, $col_5_h, $col_6_h, $col_7_h, $col_8_h);

			/*echo $col_1_h.' 1</br>';
			echo $col_2_h.' 2</br>';
			echo $col_3_h.' 3</br>';
			echo $col_4_h.' 4</br>';
			echo $col_5_h.' 5</br>';
			echo $col_6_h.' 6</br>';
			echo $col_7_h.' 7</br>';
			echo $col_8_h.' 8</br>';*/

			if(isset($detalle['partial']) && $detalle['partial']==true){
				$total_partial+=$detalle['partial_monto'];
				$total_mayor+=$detalle['partial_monto'];
			}
			//$y=$this->getY();
		}
		

		$y_anul = 180;
		if(isset($this->recibo['comprobantes_anulados'])){
			$this->recibo['comprobantes_anulados'] = array_reverse($this->recibo['comprobantes_anulados']);
			$tmp_anul = 'ANULADOS: ';
			foreach($this->recibo['comprobantes_anulados'] as $tmp_anul_i=>$item){
				if($tmp_anul_i!=0)
					$tmp_anul .= ', ';
				if(isset($this->recibo['modulo']) && $this->recibo['modulo']=="MH"){
					$tmp_anul .= $item['num'];
				}else{
					$tmp_anul .= $item['tipo'].''.$item['serie'].'-'.$item['num'];
				}
			}
			$this->setXY(15,$y_anul);$this->MultiCell(150,$height,$tmp_anul,'0','L');
			$y_anul = $this->GetY();
		}
		if(isset($this->recibo['observ'])){
			$this->setXY(15,$y_anul);$this->MultiCell(150,$height,$this->recibo['observ'],'0','L');
			$y_anul = $this->GetY();
		}
		if(isset($this->recibo['glosa'])){
			if(isset($this->recibo['observ'])){
				if($this->recibo['glosa']!=$this->recibo['observ']){
					$this->setXY(15,$y_anul);$this->MultiCell(150,$height,$this->recibo['glosa'],'0','L');
					$y_anul = $this->GetY();
				}
			}else{
				$this->setXY(15,$y_anul);$this->MultiCell(150,$height,$this->recibo['glosa'],'0','L');
				$y_anul = $this->GetY();
			}
		}
		$this->SetFont('Arial','B',10);
		$this->setXY(15,$y_anul);$this->MultiCell(150,$height,$this->recibo['iniciales'],'0','L');
		$this->SetFont('Arial','B',12);
		$this->setXY(175,172);$this->MultiCell(27,$height,number_format($total,2),'0','L');
		$height = 4;
		$this->SetFont('Arial','',9);
		switch($this->recibo['modulo']){
			case "IN":
				$this->setXY(8,205);$this->MultiCell(30,$height,'85','0','L');
				$this->setXY(19,205);$this->MultiCell(30,$height,'82','0','L');
				$this->setXY(30,205);$this->MultiCell(30,$height,'034','0','L');
				$this->setXY(39,205);$this->MultiCell(30,$height,'001','0','L');
				$this->setXY(50,205);$this->MultiCell(30,$height,'040','0','L');
				$this->setXY(72,205);$this->MultiCell(30,$height,'0108','0','L');
				$this->setXY(110,205);$this->MultiCell(30,$height,'100267','0','L');
				$this->setXY(135,205);$this->MultiCell(30,$height,'09','0','L');
				$this->setXY(149,205);$this->MultiCell(30,$height,'30043','0','L');
				break;
			case "MH":
				$this->setXY(8,210);$this->MultiCell(30,$height,'8501','0','L');
				$this->setXY(19,210);$this->MultiCell(30,$height,'8201','0','L');
				$this->setXY(30,210);$this->MultiCell(30,$height,'034','0','L');
				$this->setXY(39,210);$this->MultiCell(30,$height,'001','0','L');
				$this->setXY(50,210);$this->MultiCell(30,$height,'040','0','L');
				$this->setXY(72,210);$this->MultiCell(30,$height,'0123','0','L');
				$this->setXY(110,210);$this->MultiCell(30,$height,'30205','0','L');
				$this->setXY(125,210);$this->MultiCell(30,$height,'1540','0','L');
				$this->setXY(135,210);$this->MultiCell(30,$height,'09','0','L');
				break;
			case "AD":
				$this->setXY(8,210);$this->MultiCell(30,$height,'8501','0','L');
				$this->setXY(19,210);$this->MultiCell(30,$height,'8201','0','L');
				$this->setXY(30,210);$this->MultiCell(30,$height,'034','0','L');
				$this->setXY(39,210);$this->MultiCell(30,$height,'001','0','L');
				$this->setXY(50,210);$this->MultiCell(30,$height,'040','0','L');
				$this->setXY(72,210);$this->MultiCell(30,$height,'0123','0','L');
				$this->setXY(110,210);$this->MultiCell(30,$height,'30205','0','L');
				$this->setXY(125,210);$this->MultiCell(30,$height,'1540','0','L');
				$this->setXY(135,210);$this->MultiCell(30,$height,'09','0','L');
				break;
			case "TD":
				$this->setXY(8,210);$this->MultiCell(30,$height,'8501','0','L');
				$this->setXY(19,210);$this->MultiCell(30,$height,'8201','0','L');
				$this->setXY(30,210);$this->MultiCell(30,$height,'034','0','L');
				$this->setXY(39,210);$this->MultiCell(30,$height,'001','0','L');
				$this->setXY(50,210);$this->MultiCell(30,$height,'040','0','L');
				$this->setXY(72,210);$this->MultiCell(30,$height,'0123','0','L');
				$this->setXY(110,210);$this->MultiCell(30,$height,'30205','0','L');
				$this->setXY(125,210);$this->MultiCell(30,$height,'1540','0','L');
				$this->setXY(135,210);$this->MultiCell(30,$height,'09','0','L');
				break;
			case "LM":
				$this->setXY(8,210);$this->MultiCell(30,$height,'8501','0','L');
				$this->setXY(19,210);$this->MultiCell(30,$height,'8201','0','L');
				$this->setXY(30,210);$this->MultiCell(30,$height,'034','0','L');
				$this->setXY(39,210);$this->MultiCell(30,$height,'001','0','L');
				$this->setXY(50,210);$this->MultiCell(30,$height,'040','0','L');
				$this->setXY(72,210);$this->MultiCell(30,$height,'0123','0','L');
				$this->setXY(110,210);$this->MultiCell(30,$height,'30205','0','L');
				$this->setXY(125,210);$this->MultiCell(30,$height,'1540','0','L');
				$this->setXY(135,210);$this->MultiCell(30,$height,'09','0','L');
				break;
			case "FA":
				$this->setXY(8,210);$this->MultiCell(30,$height,'8501','0','L');
				$this->setXY(19,210);$this->MultiCell(30,$height,'8201','0','L');
				$this->setXY(30,210);$this->MultiCell(30,$height,'034','0','L');
				$this->setXY(39,210);$this->MultiCell(30,$height,'001','0','L');
				$this->setXY(50,210);$this->MultiCell(30,$height,'040','0','L');
				$this->setXY(72,210);$this->MultiCell(30,$height,'0123','0','L');
				$this->setXY(110,210);$this->MultiCell(30,$height,'30205','0','L');
				$this->setXY(125,210);$this->MultiCell(30,$height,'1540','0','L');
				$this->setXY(135,210);$this->MultiCell(30,$height,'09','0','L');
				break;
		}
		if($this->recibo['modulo']=='IN'){
			//$y=245;
			$y=240;
			$this->recibo['cont_patrimonial'] = array_reverse($this->recibo['cont_patrimonial']);
			foreach ($this->recibo['cont_patrimonial'] as $item){
				if($item['tipo']=='D'){
					$this->setXY(36,$y);$this->MultiCell(40,$height,substr($item['cuenta']['cod'],0,9),'0','L');
					$this->setXY(84,$y);$this->MultiCell(20,$height,number_format($item["monto"],2),'0','R');
				}else{
					$this->setXY(20,$y);$this->MultiCell(40,$height,substr($item['cuenta']['cod'],0,9),'0','L');
					$this->setXY(63,$y);$this->MultiCell(20,$height,number_format($item["monto"],2),'0','R');
					//$this->setXY(58,$y);$this->MultiCell(20,$height,number_format($item["monto"],2),'0','R');
				}
				//$y=267;
				$y+=4;
			}
		}else{
			$y=255;
			//$y=255;
			//die();
			$this->recibo['cont_patrimonial'] = array_reverse($this->recibo['cont_patrimonial']);
			$tmp_cont = array();
			foreach ($this->recibo['cont_patrimonial'] as $ii=>$item){
				$ind = array_search(substr($item['cuenta']['cod'],0,9), $tmp_cont);
				if($ind===FALSE){
					$tmp_cont[] = substr($item['cuenta']['cod'],0,9);
				}else{
					$this->recibo['cont_patrimonial'][$ind]['monto'] = floatval($this->recibo['cont_patrimonial'][$ind]['monto']) + floatval($item['monto']);
					$this->recibo['cont_patrimonial'][$ii]['monto'] = 0;
				}
			}

			foreach ($this->recibo['cont_patrimonial'] as $item){
				if($item['monto']!=0){
					switch(substr($item['cuenta']['cod'],0,9)){
						case '1101.0101':
							$y = 255;
							break;
						case '1201.0302':
							$item['cuenta']['cod'] = '1201.0302';
							$y = 259;
							break;
						case '1201.0303':
							$item['cuenta']['cod'] = '1201.0303';
							$y = 263;
							break;
					}
					if($item['tipo']=='D'){
						$this->setXY(36,$y);$this->MultiCell(40,$height,substr($item['cuenta']['cod'],0,9),'0','L');
						$this->setXY(89,$y);$this->MultiCell(20,$height,number_format($item["monto"],2),'0','L');
					}else{
						$this->setXY(20,$y);$this->MultiCell(40,$height,substr($item['cuenta']['cod'],0,9),'0','L');
						$this->setXY(63,$y);$this->MultiCell(20,$height,number_format($item["monto"],2),'0','L');
					}
					//$y=267;
					$y+=5;
				}
			}
		}
	}
	function Footer(){
		//
	}
}

//print_r($recibo);

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filter($recibo);
$pdf->AddPage();
$pdf->Publicar($recibo['detalle2']);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();
?>