<?php
global $f;
$f->library('pdf');
class repo extends FPDF{
	var $recibo;
	function Filter($filtros){
		$this->recibo = $filtros;
	}
	function Header(){
		if($this->recibo['organizacion']['_id']->{'$id'}=='51a50f0f4d4a13c409000013'){
			//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);
			$this->SetFont('Arial','B',14);
			//$this->setXY(170,10);$this->MultiCell(20,5,$this->recibo['cod'],'0','C');
			$this->SetFont('Arial','',10);
			$y=27;
			$this->setXY(166,$y);$this->MultiCell(10,5,date('d', $this->recibo['fec']->sec),'0','C');
			$this->setXY(176,$y);$this->MultiCell(10,5,date('m', $this->recibo['fec']->sec),'0','C');
			$this->setXY(186,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fec']->sec),'0','C');
			if(isset($this->recibo['fecfin'])){
				if($this->recibo['fec']!=$this->recibo['fecfin']){
					$y=34;
					$this->setXY(166,$y);$this->MultiCell(10,5,date('d', $this->recibo['fecfin']->sec),'0','C');
					$this->setXY(176,$y);$this->MultiCell(10,5,date('m', $this->recibo['fecfin']->sec),'0','C');
					$this->setXY(186,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fecfin']->sec),'0','C');
				}
			}
		}else if($this->recibo['organizacion']['_id']->{'$id'}=='51a50edc4d4a13441100000e'){
			//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);
			$this->SetFont('Arial','B',14);
			//$this->setXY(170,10);$this->MultiCell(20,5,$this->recibo['cod'],'0','C');
			$this->SetFont('Arial','',10);
			$y=27;
			$this->setXY(30,$y);$this->MultiCell(10,5,date('d', $this->recibo['fec']->sec),'0','C');
			$this->setXY(40,$y);$this->MultiCell(10,5,date('m', $this->recibo['fec']->sec),'0','C');
			$this->setXY(50,$y);$this->MultiCell(10,5,date('Y', $this->recibo['fec']->sec),'0','C');
			$fecfin = strtotime(date("Y-m-d",$this->recibo['fec']->sec));
			$y=$this->GetY();
			if(isset($this->recibo['fecfin'])){
				if($this->recibo['fec']!=$this->recibo['fecfin']){
					$lock = false;
					while ($lock==false) {
						//echo date('Y-m-d', $this->recibo['fecfin']->sec).' <-> '.date('Y-m-d', $fecfin).'<br />';
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
			/*****************************************************************************
			 * 
			 *****************************************************************************/
			//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);
			$this->SetFont('Arial','B',14);
			//$this->setXY(170,10);$this->MultiCell(20,5,$this->recibo['cod'],'0','C');
			$this->SetFont('Arial','',10);
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
						//echo date('Y-m-d', $this->recibo['fecfin']->sec).' <-> '.date('Y-m-d', $fecfin).'<br />';
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
		}
	}
	function Publicar($items){
		$y = 50;
		$height = 3;
		$cta = '';
		$cta_y = 0;
		$cta_tot = 0;
		$total = 0;
		/*
		 * DETALLE PARA RECIBO DE INGRESOS DE CEMENTERIO
		 */
		if($this->recibo['organizacion']['_id']->{'$id'}=='51a50f0f4d4a13c409000013'){
			$this->SetFont('Arial','B',8);
			$this->setXY(10,$y);$this->MultiCell(20,$height,'4303.09','0','L');
			$this->setXY(30,$y);$this->MultiCell(105,$height,'OTROS INGRESOS POR PRESTACION DE SERVICIOS','0','L');
			$y=$this->GetY();
			$this->SetFont('Arial','',8);
			$this->setXY(30,$y);$this->MultiCell(105,$height,'Recibido en ventanilla de Recaudacion Cementerio','0','L');
			$y=$this->GetY();
			$this->SetFont('Arial','B',8);
			$this->setXY(10,$y);$this->MultiCell(20,$height,'1201','0','L');
			$this->setXY(30,$y);$this->MultiCell(105,$height,'CUENTAS POR COBRAR','0','L');
			$y=$this->GetY();
			$this->setXY(10,$y);$this->MultiCell(20,$height,'1201.03','0','L');
			$this->setXY(30,$y);$this->MultiCell(105,$height,'VENTA DE BIENES Y SERVICIOS Y DERECHOS ADMINISTRATIVOS','0','L');
			$y=$this->GetY();
		/*
		 * DETALLE PARA RECIBO DE INGRESOS DE INMUEBLES
		 */
		}else if($this->recibo['organizacion']['_id']->{'$id'}=='51a50edc4d4a13441100000e'){
			$this->SetFont('Arial','',8);
			if(isset($this->recibo['planilla'])){
				$this->setXY(60,$y);$this->MultiCell(105,$height,'RECAUDADO HOY SEGUN PLANILLA '.$this->recibo['planilla'],'0','L');
			}else{
				$this->setXY(60,$y);$this->MultiCell(105,$height,'RECAUDADO HOY SEGUN XXXXXXX','0','L');
			}
			$y=$this->GetY();
			$this->SetFont('Arial','B',8);
			$this->setXY(30,$y);$this->MultiCell(20,$height,'1201','0','L');
			$this->setXY(60,$y);$this->MultiCell(105,$height,'CUENTAS POR COBRAR','0','L');
			$y=$this->GetY();
			$this->setXY(30,$y);$this->MultiCell(20,$height,'1201.03','0','L');
			$this->setXY(60,$y);$this->MultiCell(105,$height,'VENTA DE BIENES Y SERVICIOS Y DERECHOS ADMINISTRATIVOS','0','L');
			$y=$this->GetY();
			$this->setXY(30,$y);$this->MultiCell(20,$height,'1201.03.03','0','L');
			$this->setXY(60,$y);$this->MultiCell(105,$height,'VENTA DE SERVICIOS','0','L');
			$y=$this->GetY();
		}
		$tmp_comp = '';
		foreach ($items as $key => $item) {
			if($item['monto']!=0||$item['cuenta']['cod']=='1201.0303.42.01'){
				if($cta!=$item['cuenta']['cod']){
					if($cta_y!=0){
						$this->SetFont('Arial','',8);
						$this->setXY(168,$cta_y);$this->MultiCell(20,$height,number_format($cta_tot,2),'0','L');
						$cta_tot = 0;
					}
					if($this->recibo['organizacion']['_id']->{'$id'}=='51a50edc4d4a13441100000e'){
						$this->SetFont('Arial','BU',8);
					}else{
						$this->SetFont('Arial','B',8);
					}
					$cta = $item['cuenta']['cod'];
					$this->setXY(30,$y);$this->MultiCell(40,$height,$item['cuenta']['cod'],'0','L');
					$this->setXY(60,$y);$this->MultiCell(65,$height,$item['cuenta']['descr'],'0','L');
					$fy=$this->GetY();
					$cta_y = $y;
					$y=$this->GetY();
					if($item['cuenta']['cod']=='2101.0105'){
						$this->setXY(30,$y);$this->MultiCell(40,$height,'2101.0105.03','0','L');
						$this->setXY(60,$y);$this->MultiCell(65,$height,'IGV Retenciones por Pagar','0','L');
						$y=$this->GetY();
						$this->setXY(30,$y);$this->MultiCell(40,$height,'2101.010503.47','0','L');
						$this->setXY(60,$y);$this->MultiCell(65,$height,'Inmuebles','0','L');
						$y=$this->GetY();
					}
				}
				//$fy=$this->GetY();
				if($item['cuenta']['cod']=='1201.0303.42.06'){
					$fy=$this->GetY();
				}elseif($item['cuenta']['cod']=='1201.0302.42.02'){
					$fy=$this->GetY();
				}elseif($item['cuenta']['cod']=='1201.0302.42.03'){
					$fy=$this->GetY();
				}elseif($item['cuenta']['cod']=='1201.0303.42.04'){
					$fy=$this->GetY();
				/*}elseif($item['cuenta']['cod']=='1205.0502.41'){
					$fy=$this->GetY();*/
				}elseif($item['cuenta']['cod']=='1101.03'){
					$fy=$this->GetY();
				}elseif($item['cuenta']['cod']=='1201.0303.42.11'){
					$fy=$this->GetY();
				}elseif($item['cuenta']['cod']=='1202.0901'){
					$fy=$this->GetY();
				}elseif($item['cuenta']['cod']=='2101.0105'){
					$fy=$this->GetY();
				}else if(($tmp_comp==($item['comprobante']['serie'].'-'.$item['comprobante']['num']))&&(floatval($item['monto'])==0)){
					//$fy=$this->GetY();
				}else if(($item['concepto']!='Inhumación a Nicho - Sociedad de Beneficencia Pública de Arequipa')&&$item['monto']==0&&$item['cuenta']['cod']=='1201.0303.42.01'){
					//$fy=$this->GetY();
				}else if(($item['concepto']=='Inhumación a Nicho - Sociedad de Beneficencia Pública de Arequipa')&&$item['monto']==0&&$item['cuenta']['cod']=='1201.0303.42.01'){
					$this->SetFont('Arial','',8);
					$this->setXY(10,$y);$this->MultiCell(20,$height,$item['comprobante']['tipo'].' '.$item['comprobante']['serie'].'-'.$item['comprobante']['num'],'0','L');
					$this->setXY(30,$y);$this->MultiCell(40,$height,$item['cuenta']['cod'],'0','L');
					$this->setXY(60,$y);$this->MultiCell(65,$height,'Concesion Temporal - Sociedad de Beneficencia Publica de Arequipa','0','L');
					$fy=$this->GetY();
					$this->setXY(130,$y);$this->MultiCell(20,$height,number_format($item["monto"],2),'0','L');
				}else if($this->recibo['organizacion']['_id']->{'$id'}=='51a50f0f4d4a13c409000013'){
					$this->SetFont('Arial','',8);
					if(isset($item['comprobante'])){
						$this->setXY(10,$y);$this->MultiCell(20,$height,$item['comprobante']['tipo'].' '.$item['comprobante']['serie'].'-'.$item['comprobante']['num'],'0','L');
					}elseif(isset($item['recibo_definitivo'])){
						$this->setXY(10,$y);$this->MultiCell(20,$height,'RD - '.$item['recibo_definitivo']['num'],'0','L');
					}
					$this->setXY(30,$y);$this->MultiCell(40,$height,$item['cuenta']['cod'],'0','L');
					$this->setXY(60,$y);$this->MultiCell(65,$height,$item['concepto'],'0','L');
					$fy=$this->GetY();
					$this->setXY(130,$y);$this->MultiCell(20,$height,number_format($item["monto"],2),'0','L');
				}else if($this->recibo['organizacion']['_id']->{'$id'}=='51a50edc4d4a13441100000e'){
					/*
					 * FORMATEO DE SERIES
					 */
					if(intval($item['comprobante']['serie'])==22){
						if(strlen($item['comprobante']['serie'])!=4){
							if($item['comprobante']['tipo']=='F')
								$item['comprobante']['serie'] = '0022';
							else
								$item['comprobante']['serie'] = '022';
						}
					}else{
						if(strlen($item['comprobante']['serie'])!=3)
							$item['comprobante']['serie'] = '0'.$item['comprobante']['serie'];
					}
					$this->SetFont('Arial','',8);
					$this->setXY(10,$y);$this->MultiCell(20,$height,$item['comprobante']['tipo'].' '.$item['comprobante']['serie'].'-'.$item['comprobante']['num'],'0','L');
					$this->setXY(30,$y);$this->MultiCell(100,$height,$item['concepto'],'0','L');
					$fy=$this->GetY();
					$this->setXY(125,$y);$this->MultiCell(15,$height,number_format($item["monto"],2),'0','R');
				}
				$cta_tot += floatval($item['monto']);
				$y=$fy;
				$tmp_comp = $item['comprobante']['serie'].'-'.$item['comprobante']['num'];
			}
			$total += floatval($item['monto']);
			if($y>170&&!($item['cuenta']['cod']=='2101.0105')){
				if($this->recibo['organizacion']['_id']->{'$id'}=='51a50f0f4d4a13c409000013'){
					$this->SetFont('Arial','B',8);
					$this->setXY(60,$y);$this->MultiCell(65,$height,'VAN ...','0','L');
					$this->setXY(168,$y);$this->MultiCell(20,$height,number_format($total,2),'0','L');
					if($cta_y!=0){
						$this->SetFont('Arial','',8);
						$this->setXY(168,$cta_y);$this->MultiCell(20,$height,number_format($cta_tot,2),'0','L');
						$cta_y = 0;
						$cta_tot = 0;
					}
					$this->AddPage();
					$y = 57;
					$this->SetFont('Arial','B',8);
					$this->setXY(60,$y);$this->MultiCell(65,$height,'VIENEN ...','0','L');
					$this->setXY(168,$y);$this->MultiCell(20,$height,number_format($total,2),'0','L');
					$fy=$this->GetY();
					$y=$fy;
				}else if($this->recibo['organizacion']['_id']->{'$id'}=='51a50edc4d4a13441100000e'){
					//$this->setXY(168,$cta_y);$this->MultiCell(20,$height,number_format($cta_tot,2),'0','L');
					$this->SetFont('Arial','B',8);
					$this->setXY(60,$y);$this->MultiCell(65,$height,'VAN ...','0','L');
					$this->setXY(125,$y);$this->MultiCell(15,$height,number_format($cta_tot,2),'0','R');
					$fy=$this->GetY();
					$y=$fy;
					$this->setXY(60,$y);$this->MultiCell(65,$height,'VAN ...','0','L');
					$this->setXY(168,$y);$this->MultiCell(20,$height,number_format($total,2),'0','L');
					$this->AddPage();
					$y = 57;
					$this->SetFont('Arial','B',8);
					$this->setXY(60,$y);$this->MultiCell(65,$height,'VIENEN ...','0','L');
					$this->setXY(168,$y);$this->MultiCell(20,$height,number_format($total,2),'0','L');
					$fy=$this->GetY();
					$y=$fy;
					if($cta_y!=0){
						$this->SetFont('Arial','B',8);
						$this->setXY(30,$y);$this->MultiCell(40,$height,$item['cuenta']['cod'],'0','L');
						$this->setXY(60,$y);$this->MultiCell(65,$height,$item['cuenta']['descr'],'0','L');
						$fy=$this->GetY();
						$y=$fy;
						$this->setXY(60,$y);$this->MultiCell(65,$height,'VIENEN ...','0','L');
						$this->setXY(125,$y);$this->MultiCell(15,$height,number_format($cta_tot,2),'0','R');
						//$this->setXY(168,$y);$this->MultiCell(20,$height,number_format($cta_tot,2),'0','L');
						$cta_y = $y;
						$fy=$this->GetY();
						$y=$fy;
					}
					//$this->setXY(168,$y);$this->MultiCell(20,$height,number_format($total,2),'0','L');
					$fy=$this->GetY();
					$y=$fy;
				}
			}
		}
		if($cta_y!=0){
			$this->SetFont('Arial','',8);
			$this->setXY(168,$cta_y);$this->MultiCell(20,$height,number_format($cta_tot,2),'0','L');
			if($item['cuenta']['cod']=='2101.0105'){
				$this->setXY(125,$cta_y+8);$this->MultiCell(15,$height,number_format($cta_tot,2),'0','R');
			}
		}
		$y_anul = 170;
		if($this->recibo['organizacion']['_id']->{'$id'}=='51a50edc4d4a13441100000e'){
			$y_anul = $cta_y+16;
		}
		if(isset($this->recibo['comprobantes_anulados'])){
			$this->recibo['comprobantes_anulados'] = array_reverse($this->recibo['comprobantes_anulados']);
			$tmp_anul = 'ANULADOS: ';
			foreach($this->recibo['comprobantes_anulados'] as $tmp_anul_i=>$item){
				if($tmp_anul_i!=0)
					$tmp_anul .= ', ';
				if($this->recibo['organizacion']['_id']->{'$id'}=='51a50edc4d4a13441100000e'){
					/*
					 * FORMATEO DE SERIES
					 */
					if(intval($item['serie'])==22){
						if(strlen($item['comprobante']['serie'])!=4&&$item['comprobante']['tipo']=='F')
							$item['serie'] = '0022';
					}else{
						if(strlen($item['comprobante']['serie'])!=3)
							$item['serie'] = '0'.intval($item['serie']);
					}
					$tmp_anul .= $item['tipo'].$item['serie'].'-'.$item['num'];
				}else
					$tmp_anul .= $item['serie'].'-'.$item['num'];
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
		$this->setXY(168,174);$this->MultiCell(30,$height,number_format($total,2),'0','L');
		$height = 4;
		$this->SetFont('Arial','',9);
		//CEMENTERIO
		if($this->recibo['organizacion']['_id']->{'$id'}=='51a50f0f4d4a13c409000013'){
			$this->setXY(8,200);$this->MultiCell(30,$height,'8501','0','L');
			$this->setXY(19,200);$this->MultiCell(30,$height,'8201','0','L');
			$this->setXY(30,200);$this->MultiCell(30,$height,'034','0','L');
			$this->setXY(39,200);$this->MultiCell(30,$height,'001','0','L');
			$this->setXY(50,200);$this->MultiCell(30,$height,'040','0','L');
			$this->setXY(72,200);$this->MultiCell(30,$height,'0108','0','L');
			$this->setXY(110,200);$this->MultiCell(30,$height,'100267','0','L');
			$this->setXY(125,200);$this->MultiCell(30,$height,'30062','0','L');
			$this->setXY(135,200);$this->MultiCell(30,$height,'09','0','L');
		//INMUEBLES
		}else if($this->recibo['organizacion']['_id']->{'$id'}=='51a50edc4d4a13441100000e'){
			$this->setXY(8,210);$this->MultiCell(30,$height,'85','0','L');
			$this->setXY(19,210);$this->MultiCell(30,$height,'82','0','L');
			$this->setXY(30,210);$this->MultiCell(30,$height,'034','0','L');
			$this->setXY(39,210);$this->MultiCell(30,$height,'001','0','L');
			$this->setXY(50,210);$this->MultiCell(30,$height,'040','0','L');
			$this->setXY(72,210);$this->MultiCell(30,$height,'0108','0','L');
			$this->setXY(110,210);$this->MultiCell(30,$height,'100267','0','L');
			$this->setXY(125,210);$this->MultiCell(30,$height,'30043','0','L');
			$this->setXY(135,210);$this->MultiCell(30,$height,'09','0','L');
		}
		$y=240;
		if($this->recibo['organizacion']['_id']->{'$id'}=='51a50edc4d4a13441100000e'){
			$this->recibo['cont_patrimonial'] = array_reverse($this->recibo['cont_patrimonial']);
			foreach ($this->recibo['cont_patrimonial'] as $item){
				switch(substr($item['cuenta']['cod'],0,9)){
					case '1101.0101':
						$y = 255;
						break;
					case '2101.0105':
						$y = 263;
						break;
					case '1201.0303':
						$item['cuenta']['cod'] = '1201.0303';
						$y = 259;
						break;
				}
				if($item['tipo']=='D'){
					$this->setXY(36,$y);$this->MultiCell(40,$height,$item['cuenta']['cod'],'0','L');
					$this->setXY(89,$y);$this->MultiCell(20,$height,number_format($item["monto"],2),'0','L');
				}else{
					$this->setXY(20,$y);$this->MultiCell(40,$height,$item['cuenta']['cod'],'0','L');
					$this->setXY(63,$y);$this->MultiCell(20,$height,number_format($item["monto"],2),'0','L');
				}
				$y=267;
			}
		}else{
			foreach ($this->recibo['cont_patrimonial'] as $item){
				switch(substr($item['cuenta']['cod'],0,9)){
					case '1201.0302':
						$item['cuenta']['cod'] = '1201.0302';
						break;
					case '1201.0303':
						$item['cuenta']['cod'] = '1201.0303';
						break;
				}
				if($item['tipo']=='D'){
					$this->setXY(20,$y);$this->MultiCell(40,$height,$item['cuenta']['cod'],'0','L');
					$this->setXY(63,$y);$this->MultiCell(20,$height,number_format($item["monto"],2),'0','L');
				}else{
					$this->setXY(36,$y);$this->MultiCell(40,$height,$item['cuenta']['cod'],'0','L');
					$this->setXY(89,$y);$this->MultiCell(20,$height,number_format($item["monto"],2),'0','L');
				}
				$y=$this->GetY();
			}
		}
	}
	function Footer(){
		//
	}
}
$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filter($recibo);
$pdf->AddPage();
$pdf->Publicar($recibo['detalle']);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();
?>