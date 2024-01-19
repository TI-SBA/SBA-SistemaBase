<?php
global $f;
$f->library('pdf');
$f->library('helpers');
class repo extends FPDF{
	var $recibo;
	function Filter($filtros){
		$this->recibo = $filtros;
	}
	function Header(){
		if($this->recibo['tipo_inm']=='P')
			$size_font = 9;
		else
			$size_font = 9;
		$this->SetFont('Arial','B',14);
		$this->SetFont('Arial','',$size_font);
		$y=27;
		$yt_inm = 95;
		$yt_obs = 130;
		$yt_comp = 145;
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
	}
	function Publicar($items){
		$helper=new helper();
		if($this->recibo['tipo_inm']=='P'){
			$size_font = 9;
			$height = 4;
		}else{
			$size_font = 9;
			$height = 4;
		}
		$meses = array("Ene.","Feb.","Mar.","Abr.","May.","Jun.","Jul.","Ago.","Set.","Oct.","Nov.","Dic.");
		$y = 50;
		$yt_inm = 95;
		$yt_obs = 130;
		$yt_comp = 147;
		$cta = '';
		$cta_y = 0;
		$cta_tot = 0;
		$total = 0;
		$this->SetFont('Arial','',$size_font);
		if(isset($this->recibo['planilla'])){
			$this->setXY(60,$y);$this->MultiCell(105,$height,'RECAUDADO HOY SEGUN PLANILLA '.$this->recibo['planilla'],'0','L');
		}else{
			$this->setXY(60,$y);$this->MultiCell(105,$height,'RECAUDADO HOY SEGUN XXXXXXX','0','L');
		}
		$y=$this->GetY();
		$this->SetFont('Arial','B',$size_font);
		$this->setXY(30,$y);$this->MultiCell(20,$height,'1201','0','L');
		$this->setXY(60,$y);$this->MultiCell(105,$height,'CUENTAS POR COBRAR','0','L');
		$y=$this->GetY();
		$this->setXY(30,$y);$this->MultiCell(20,$height,'1201.03','0','L');
		$this->setXY(60,$y);$this->MultiCell(105,$height,'VENTA DE BIENES Y SERVICIOS Y DERECHOS ADMINISTRATIVOS','0','L');
		$y=$this->GetY();
		$this->setXY(30,$y);$this->MultiCell(20,$height,'1201.03.03','0','L');
		$this->setXY(60,$y);$this->MultiCell(105,$height,'VENTA DE SERVICIOS','0','L');
		$y=$this->GetY();
		$tmp_comp = '';





		$tmp_1202 = $cta_tot;
		$tmp_2101 = $cta_tot;




		$max_y_ = 0;
		$bases_tmp = array();
		foreach ($items as $key => $item) {
			if($y>$max_y_) $max_y_ = $y;
			
		}
		if($cta_y!=0){
			$this->SetFont('Arial','',$size_font);
			if(substr($item['cuenta']['cod'],0,9)=='2101.0105'&&$cta_tot!=0){
				$tmp_2101 = $cta_tot;
				//$this->setXY($yt_comp,$y-3);$this->MultiCell(15,$height,number_format($cta_tot, 2, '.', ''),'0','R');
			}elseif($cta_tot!=0){
				$this->setXY(168,$cta_y);$this->MultiCell(20,$height,number_format($cta_tot, 2, '.', ''),'0','L');
			}
		}









		//$y = $fy+$height;
		if($y>160){
			$tmp_y_p = $y;
			$tmp_cta_tot = $cta_tot;
			if(substr($cta,0,9)!='2101.0105'){
				if($items[$key+1]['cuenta']['cod']!=$cta){
					$this->SetFont('Arial','',$size_font);
					if($cta_tot!=0){
						$this->setXY(168,$cta_y);$this->MultiCell(20,$height,number_format($cta_tot, 2, '.', ''),'0','L');
					}
					$cta_tot = 0;
					$tmp_cta_tot = 0;
					$y = $tmp_y_p;
				}else{
					//$this->setXY(168,$cta_y);$this->MultiCell(20,$height,number_format($cta_tot,2),'0','L');
					$this->SetFont('Arial','B',$size_font);
					$this->setXY(60,$y);$this->MultiCell(65,$height,'VAN ...','0','L');
					if($cta_tot!=0){
						$this->setXY($yt_comp,$y);$this->MultiCell(15,$height,number_format($cta_tot, 2, '.', ''),'0','R');
					}
					$fy=$this->GetY();
					$y=$fy;
				}
			}
			$this->setXY(60,$y);$this->MultiCell(65,$height,'VAN ...','0','L');
			$this->setXY(168,$y);$this->MultiCell(20,$height,number_format($total-$tmp_cta_tot, 2, '.', ''),'0','L');
			$this->AddPage();
			$y = 57;
			$this->SetFont('Arial','B',$size_font);
			$this->setXY(60,$y);$this->MultiCell(65,$height,'VIENEN ...','0','L');
			$this->setXY(168,$y);$this->MultiCell(20,$height,number_format($total-$tmp_cta_tot, 2, '.', ''),'0','L');
			$fy=$this->GetY();
			$y=$fy;
			if($cta_y!=0&&$cta_tot!=0){
				$this->SetFont('Arial','B',$size_font);
				$this->setXY(30,$y);$this->MultiCell(40,$height,$item['cuenta']['cod'],'0','L');
				$this->setXY(60,$y);$this->MultiCell(65,$height,$item['cuenta']['descr'],'0','L');
				$fy=$this->GetY();
				$y=$fy;
				$this->setXY(60,$y);$this->MultiCell(65,$height,'VIENEN ...','0','L');
				$this->setXY($yt_comp,$y);$this->MultiCell(15,$height,number_format($cta_tot, 2, '.', ''),'0','R');
				//$this->setXY(168,$y);$this->MultiCell(20,$height,number_format($cta_tot,2),'0','L');
				$cta_y = $y;
				$fy=$this->GetY();
				$y=$fy;
			}
			//$this->setXY(168,$y);$this->MultiCell(20,$height,number_format($total,2),'0','L');
			$fy=$this->GetY();
			$y=$fy;
		}




		if($this->recibo['tipo_inm']!='P'){
			$fy=$this->GetY();
			$cta_y = $y;
			$this->setXY(60,$y);$this->MultiCell(65,$height,'CUENTAS POR COBRAR DIVERSAS','0','L');
			$y=$this->GetY();
			$this->setXY(60,$y);$this->MultiCell(65,$height,'MULTAS Y SANCIONES','0','L');
			$y=$this->GetY();
			$this->setXY(30,$y);$this->MultiCell(40,$height,'1202.0901','0','L');
			$this->setXY(60,$y);$this->MultiCell(65,$height,'MORAS','0','L');
			$y=$this->GetY();
			$this->setXY(168,$cta_y);$this->MultiCell(20,$height,number_format($tmp_1202, 2, '.', ''),'0','L');
			$this->setXY($yt_comp,$y-3);$this->MultiCell(15,$height,number_format($tmp_1202, 2, '.', ''),'0','R');
		}




		

		$fy=$this->GetY();
      	//$y += $height;
		$cta_y = $y;
		$this->setXY(60,$y);$this->MultiCell(65,$height,'IMPUESTOS, CONTRIBUCIONES Y OTROS','0','L');
		$y=$this->GetY();
		$this->setXY(30,$y);$this->MultiCell(40,$height,'2101.0105','0','L');
		$this->setXY(60,$y);$this->MultiCell(65,$height,'IMPUESTO GENERAL A LAS VENTAS','0','L');
		$y=$this->GetY();
		$this->setXY(30,$y);$this->MultiCell(40,$height,'2101.0105.03','0','L');
		$this->setXY(60,$y);$this->MultiCell(65,$height,'IGV RETENCIONES POR PAGAR','0','L');
		$y=$this->GetY();
		$this->setXY(30,$y);$this->MultiCell(40,$height,'2101.010503.47','0','L');
		$this->setXY(60,$y);$this->MultiCell(65,$height,'Inmuebles','0','L');
		$y=$this->GetY();

		if($cta_y!=0){
			$this->SetFont('Arial','',$size_font);
			if($tmp_2101!=0){
				$this->setXY(168,$cta_y);$this->MultiCell(20,$height,number_format($tmp_2101, 2, '.', ''),'0','L');
			}
			if(substr($item['cuenta']['cod'],0,9)=='2101.0105'&&$tmp_2101!=0){
				$this->setXY($yt_comp,$y-3);$this->MultiCell(15,$height,number_format($tmp_2101, 2, '.', ''),'0','R');
			}
		}
















































		if(isset($this->recibo['vouchers'])){
			$this->SetFont('Arial','BU',$size_font);
			//$y=$this->GetY();
			$this->setXY(30,$y);$this->MultiCell(40,$height,'2101.010503.47','0','L');
			$this->setXY(60,$y);$this->MultiCell(65,$height,'Inmuebles','0','L');
			$this->SetFont('Arial','',$size_font);
			$this->setXY(90,$y);$this->MultiCell(65,$height,'Detraccion','0','L');
			$tot_tmp_vou = 0;
			foreach ($this->recibo['vouchers'] as $voucher) {
				$tot_tmp_vou += floatval($voucher['monto']);
			}
			$this->setXY($yt_comp,$y);$this->MultiCell(15,$height,number_format($tot_tmp_vou, 2, '.', ''),'0','R');
		}
		$this->SetFont('Arial','',$size_font);








		$y_anul = 175+$height;
		if($this->recibo['organizacion']['_id']->{'$id'}=='51a50edc4d4a13441100000e'){
			$y_anul = $cta_y+20+$height;
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
						if(strlen($item['serie'])!=4&&$item['tipo']=='F')
							$item['serie'] = '0022';
					}else{
						if(isset($item['comprobante'])){
							if(strlen($item['comprobante']['serie'])!=3)
								$item['serie'] = '0'.intval($item['serie']);
						}else{
							if(strlen($item['serie'])!=3)
								$item['serie'] = '0'.intval($item['serie']);
						}
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
		$this->SetFont('Arial','B',$size_font);
		$this->setXY(15,$y_anul);$this->MultiCell(150,$height,$this->recibo['iniciales'],'0','L');
		$this->SetFont('Arial','B',12);
		$this->setXY(168,174);$this->MultiCell(30,$height,number_format($total, 2, '.', ''),'0','L');
		$height = 4;
		$this->SetFont('Arial','',$size_font);
		$this->setXY(8,210);$this->MultiCell(30,$height,'85','0','L');
		$this->setXY(19,210);$this->MultiCell(30,$height,'82','0','L');
		$this->setXY(30,210);$this->MultiCell(30,$height,'034','0','L');
		$this->setXY(39,210);$this->MultiCell(30,$height,'001','0','L');
		$this->setXY(50,210);$this->MultiCell(30,$height,'040','0','L');
		$this->setXY(72,210);$this->MultiCell(30,$height,'0108','0','L');
		$this->setXY(110,210);$this->MultiCell(30,$height,'100267','0','L');
		$this->setXY($yt_comp,210);$this->MultiCell(30,$height,'30043','0','L');
		$this->setXY(135,210);$this->MultiCell(30,$height,'09','0','L');
		$y=255;
		$this->recibo['cont_patrimonial'] = array_reverse($this->recibo['cont_patrimonial']);
		foreach ($this->recibo['cont_patrimonial'] as $item){
			/*switch(substr($item['cuenta']['cod'],0,9)){
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
			}*/
			if($item['tipo']=='D'){
				$this->setXY(36,$y);$this->MultiCell(40,$height,substr($item['cuenta']['cod'],0,9),'0','L');
				$this->setXY(89,$y);$this->MultiCell(20,$height,number_format($item["monto"], 2, '.', ''),'0','L');
			}else{
				$this->setXY(20,$y);$this->MultiCell(40,$height,substr($item['cuenta']['cod'],0,9),'0','L');
				$this->setXY(63,$y);$this->MultiCell(20,$height,number_format($item["monto"], 2, '.', ''),'0','L');
			}
			$y = $this->GetY();
			//$y=267;
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