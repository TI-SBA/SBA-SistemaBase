<?php
global $f;
$f->library('pdf');
class repo extends FPDF{
	var $recibo;
	function Filter($filtros){
		$this->recibo = $filtros;
	}
	function Header(){
		
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);
		$this->SetFont('Arial','B',14);
		//$this->setXY(170,10);$this->MultiCell(20,5,$this->recibo['cod'],'0','C');
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
		
	}
	function Publicar($items){
		$y = 50;
		$height = 3;
		$cta = '';
		$cta_y = 0;
		$cta_tot = 0;
		$total = 0;
		/*
		 * DETALLE PARA RECIBO DE INGRESOS DE MOISES HERESI
		 */
		$this->SetFont('Arial','B',8);
		$this->setXY(7,$y);$this->MultiCell(20,$height,'4302.04','0','L');
		$this->setXY(32,$y);$this->MultiCell(105,$height,'DERECHOS ADMINISTRATIVOS DE SALUD','0','L');
		$y=$this->GetY();
		$this->SetFont('Arial','B',8);
		$this->setXY(7,$y);$this->MultiCell(20,$height,'1201','0','L');
		$this->setXY(32,$y);$this->MultiCell(105,$height,'CUENTAS POR COBRAR','0','L');
		$y=$this->GetY();
		$this->setXY(7,$y);$this->MultiCell(20,$height,'1201.0302','0','L');
		$this->setXY(32,$y);$this->MultiCell(105,$height,'DERECHOS Y TASAS ADMINISTRATIVAS','0','L');
		$y=$this->GetY();
		$y_max=$y;
		$tmp_comp = '';
		foreach ($items as $key => $cuenta){
			if($y_max>$y) $y = $y_max;
			$this->SetFont('Arial','BU',8);
			$cta = $item['cuenta']['cod'];
			$cta_tot = 0;
			$cta_y=$y;
			$this->setXY(7,$y);$this->MultiCell(40,$height,$key,'0','L');
			$this->setXY(32,$y);$this->MultiCell(65,$height,$cuenta[0]['cuenta']['descr'],'0','L');
			$y=$this->GetY();
			
			$serv = array();
			foreach ($cuenta as $k => $item){
				if(isset($serv[$item['comprobante']['servicio']['_id']->{'$id'}])){
					$serv[$item['comprobante']['servicio']['_id']->{'$id'}][] = $item;
				}else{
					$serv[$item['comprobante']['servicio']['_id']->{'$id'}] = array($item);
				}
			}
			
			foreach ($serv as $k => $row){
			    $ctass[$k] = $row['comprobante']['servicio']['_id']->{'$id'};
			}
			array_multisort($ctass,SORT_ASC,$serv);
			foreach ($serv as $k => $item){
				$comps = ' Rec.';
				$totals = 0;
				foreach ($item as $ke => $value) {
					if($ke==0)
						$comps .= $value['comprobante']['num'].', ';
					else
						$comps .= substr($value['comprobante']['num'],strlen($value['comprobante']['num'])-2,2).', ';
					$totals += floatval($value['monto']);
				}
				$serv[$k][0]['comps'] = substr($comps, 0, strlen($comps)-2);
				$serv[$k][0]['totals'] = $totals;
			}
			$this->SetFont('Arial','',8);
			$y_max = $y;
			foreach ($serv as $k => $item){
				if(isset($item[0]['comprobante']['items'][0]['servicio'])){






					$this->setXY(32,$y);$this->MultiCell(100,$height,sizeof($item).' '.$item[0]['comprobante']['items'][0]['servicio']['nomb'].$item[0]['comps'],'0','L');




				}else{
					switch($item[0]['comprobante']['hospitalizacion']['tipo']){
						case 'P': $tipo = 'Parcial'; break;
						case 'C': $tipo = 'Completa'; break;
					}
					$this->setXY(32,$y);$this->MultiCell(100,$height,sizeof($item).' Hospitalización '.$tipo.' x'.
						$item[0]['comprobante']['hospitalizacion']['cant'].'.'.$item[0]['comprobante']['hospitalizacion']['modalidad'].
						' Categ."'.$item[0]['comprobante']['hospitalizacion']['categoria'].'"'.$item[0]['comps'],'0','L');
				}
				$y_max = $this->GetY();
				$this->setXY(125,$y);$this->MultiCell(15,$height,number_format($item[0]['totals'],2),'0','R');
				$y=$this->GetY();
				$cta_tot += $item[0]['totals'];
			}
			$this->SetFont('Arial','B',8);
			$this->setXY(168,$cta_y);$this->MultiCell(20,$height,number_format($cta_tot,2),'0','L');
			$total += $cta_tot;
		}
		$y_anul = 175;
		if($this->recibo['organizacion']['_id']->{'$id'}=='51a50edc4d4a13441100000e'){
			$y_anul = $cta_y+16;
		}
		if(isset($this->recibo['comprobantes_anulados'])){
			$this->recibo['comprobantes_anulados'] = array_reverse($this->recibo['comprobantes_anulados']);
			$tmp_anul = 'ANULADOS: ';
			foreach($this->recibo['comprobantes_anulados'] as $tmp_anul_i=>$item){
				if($tmp_anul_i!=0)
					$tmp_anul .= ', ';
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
		$this->setXY(8,210);$this->MultiCell(30,$height,'8501','0','L');
		$this->setXY(19,210);$this->MultiCell(30,$height,'8201','0','L');
		$this->setXY(30,210);$this->MultiCell(30,$height,'034','0','L');
		$this->setXY(39,210);$this->MultiCell(30,$height,'001','0','L');
		$this->setXY(50,210);$this->MultiCell(30,$height,'040','0','L');
		$this->setXY(72,210);$this->MultiCell(30,$height,'0123','0','L');
		$this->setXY(110,210);$this->MultiCell(30,$height,'30205','0','L');
		$this->setXY(125,210);$this->MultiCell(30,$height,'1540','0','L');
		$this->setXY(135,210);$this->MultiCell(30,$height,'09','0','L');
		$y=255;
		$this->recibo['cont_patrimonial'] = array_reverse($this->recibo['cont_patrimonial']);
		foreach ($this->recibo['cont_patrimonial'] as $item){
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
				$this->setXY(36,$y);$this->MultiCell(40,$height,$item['cuenta']['cod'],'0','L');
				$this->setXY(89,$y);$this->MultiCell(20,$height,number_format($item["monto"],2),'0','L');
			}else{
				$this->setXY(20,$y);$this->MultiCell(40,$height,$item['cuenta']['cod'],'0','L');
				$this->setXY(63,$y);$this->MultiCell(20,$height,number_format($item["monto"],2),'0','L');
			}
			$y=267;
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
$pdf->Publicar($recibo['cuentas']);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();
?>