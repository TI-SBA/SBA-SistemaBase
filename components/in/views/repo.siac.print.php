<?php
global $f;
$f->library('pdf');
class reporte extends FPDF{
	var $sublocal;
	function Filtros($sublocal){
		$this->sublocal = $sublocal;
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/td/expd.gif',15,15,347,249);
		$this->SetFont('Arial','B',14);
		$this->SetXY(10,10);$this->MultiCell(190,5,"SITUACION ACTUAL DE INMUEBLES",'0','C');
		$this->SetFont('Arial','',11);
		$this->SetXY(10,15);$this->MultiCell(190,5,"Por sublocal: (".$this->sublocal['tipo']['nomb'].') '.$this->sublocal['nomb'],'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(277,5,date("d/m/Y")." - Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Inmuebles",'0','C');
		$this->SetXY(10,20);$this->MultiCell(50,5,"INMUEBLE",'1','C');
		$this->SetXY(60,20);$this->MultiCell(80,5,"INQUILINO",'1','C');
		$this->SetXY(140,20);$this->MultiCell(40,5,"ESTADO CONTRATO / FECHA",'1','C');
		$this->SetXY(180,20);$this->MultiCell(30,5,"DEUDA TOTAL",'1','C');
		$this->SetXY(210,20);$this->MultiCell(40,5,"ULTIMO MES PAGADO",'1','C');
	}
	function Publicar($inmuebles){
		global $f;
		$f->library('helpers');
		$helper=new helper();
		$monedas = array(
			"S"=>"S/.",
			"D"=>"$"
		);
		$meses = array("--","ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE","S/.","US$");
		$meses_2 = array("--","ENE-FEB","FEB-MAR","MAR-ABR","ABR-MAY","MAY-JUN","JUN-JUL","JUL-AGO","AGO-SET","SET-OCT","OCT-NOV","NOV-DIC","DIC-ENE","S/.","US$");
		$y = 25;
		$limit_page = 190;
		foreach($inmuebles as $i=>$inmu){
			if($y>$limit_page){
				$this->AddPage();
				$y = 25;
			}
			if(!isset($inmu['desocupado'])){
				foreach ($inmu['contratos'] as $j=>$cont) {
					if($y>$limit_page){
						$this->AddPage();
						$y = 25;
					}
					$titular = $cont['titular']['nomb'];
					if(isset($cont['titular']['appat']))
						$titular = $cont['titular']['appat'].' '.$cont['titular']['apmat'].', '.$cont['titular']['nomb'];
					$dia_contrato = intval(date('d', $cont['fecini']->sec));
					$this->SetXY(10,$y);$this->MultiCell(50,5,$inmu['direccion'],'0','C');
					$y_max = $this->GetY();
					$this->SetXY(60,$y);$this->MultiCell(80,5,$titular,'0','L');
					$y_max = $this->GetY();
					$this->SetXY(140,$y);$this->MultiCell(40,5,$cont['motivo']['nomb'].
						"\n".date("d/m/Y",$cont['fecini']->sec).' <-> '.date("d/m/Y",$cont['fecfin']->sec),'0','C');
					if($this->GetY()>$y_max) $y_max = $this->GetY();
					$total = 0;
					$ultimo = 'NO SE PAGO NINGUNA CUOTA';
					foreach ($cont['pagos'] as $pago) {
						$ult = false;
						if(!isset($pago['estado']))
							$total += floatval($cont['importe']);
						elseif($pago['estado']=='P'){
							$total += (floatval($cont['importe'])-floatval($pago['total']));
							$ult = true;
						}else{
							$ult = true;
						}
						if($ult==true){
							if($dia_contrato==16){
								$pago['mes'] = $pago['mes']-1;
								if($pago['mes']==0) $pago['mes'] = 12;
								$ultimo = $meses_2[$pago['mes']].' - '.$pago['ano'];
							}else{
								$ultimo = $meses[$pago['mes']].' - '.$pago['ano'];
							}
						}
					}
					$this->SetXY(180,$y);$this->MultiCell(30,5,$total,'0','C');
					$this->SetXY(210,$y);$this->MultiCell(40,5,$ultimo,'0','C');
					$this->Line(10, $y, 250,$y);
					$y = $y_max;
				}
			}else{
				$this->SetXY(10,$y);$this->MultiCell(50,5,$inmu['direccion'],'0','C');
				if($y>$limit_page){
					$this->AddPage();
					$y = 25;
				}
				$titular = $inmu['contrato']['titular']['nomb'];
				if(isset($inmu['contrato']['titular']['appat']))
					$titular = $inmu['contrato']['titular']['appat'].' '.$inmu['contrato']['titular']['apmat'].', '.$inmu['contrato']['titular']['nomb'];
				$this->SetXY(60,$y);$this->MultiCell(80,5,$titular,'0','L');
				$y_max = $this->GetY();
				$this->SetXY(60,$y);$this->MultiCell(200,5,'INMUEBLE DESOCUPADO','0','C');
				$ultimo = 'NO SE PAGO NINGUNA CUOTA';
				foreach ($inmu['contrato']['pagos'] as $pago) {
					$ult = false;
					if(!isset($pago['estado'])){
							//
					}elseif($pago['estado']=='P'){
						$ult = true;
					}else{
						$ult = true;
					}
					if($ult==true){
						if($dia_contrato==16){
							$ultimo = $meses_2[$pago['mes']].' - '.$pago['ano'];
						}else{
							$ultimo = $meses[$pago['mes']].' - '.$pago['ano'];
						}
					}
				}
				$this->SetXY(180,$y);$this->MultiCell(30,5,'CANCELADO','0','C');
				$this->SetXY(210,$y);$this->MultiCell(40,5,$ultimo,'0','C');
				$this->Line(10, $y, 250,$y);
				$y = $this->GetY();
			}
		}
		//$this->SetXY(15,$y);$this->MultiCell(85,5,date("d/m/Y",$items['fecini']->sec).'               '.date("d/m/Y",$items['fecfin']->sec),'1','C');
	}
	function Footer(){} 
}
$pdf=new reporte('L','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filtros($sublocal);
$pdf->AddPage();
$pdf->Publicar($inmuebles);
$pdf->SetLeftMargin(25);
$pdf->Output();
?>