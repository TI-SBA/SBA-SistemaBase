<?php
global $f;
$f->library('pdf');
class expdientes extends FPDF
{
	var $filter;
	function Filtros($filtros){
		$this->filter = $filtros;
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/td/expd.gif',15,15,347,249);
		$this->SetFont('Arial','B',14);
		$this->SetXY(10,10);$this->MultiCell(277,5,"LIQUIDACION DE ALQUILER",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(277,5,date("d/m/Y")." - Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Inmuebles",'0','C');
	}
	function Publicar($items,$arrendatario){
		global $f;
		$f->library('helpers');
		$helper=new helper();
		$monedas = array(
			"S"=>"S/.",
			"D"=>"$"
		);
		$condicion_arrendamiento = array(
			"CT"=>"Nuevo",
			"RE"=>"Renovación",
			"CV"=>"Convenio",
			"CU"=>"Cesión en Uso",
			"CM"=>"Comodato",
			"AC"=>"Acta de Conciliación",
			"RS"=>'Por Ocupación',
			"AU"=>'Autorización',
			"PE"=>'Penalidades',
			"TR"=>'Traspaso',
			"AD"=>'Audiencias'
		);
		$this->SetFont('arial','',8);
		$y=30;
		$y_ini = $y;
		$now = new DateTime(date("Y-m-d"));
		$arren = $arrendatario["nomb"];
		if($arrendatario["tipo_enti"]=="P"){
			$arren .=" ".$arrendatario["appat"]." ".$arrendatario["apmat"];
		}
		$total_s_all = 0;
		$total_d_all = 0;
		foreach($items as $espa){
			if($y>275){
				$this->AddPage();
				$y=$y_ini;
			}
			$this->SetFont('arial','BU',12);
			$this->SetXY(5,$y);$this->MultiCell(190,5,$espa["ubic"]["ref"],'0','L');
			$this->SetFont('arial','B',11);
			$this->SetXY(100,$y);$this->MultiCell(100,5,$arren,'0','L');
			$y+=5;
			foreach($espa["items"] as $item){
				if($y>275){
					$this->AddPage();
					$y=$y_ini;
				}
				$this->SetFont('arial','B',10);
				$this->SetXY(5,$y);$this->MultiCell(80,5,"MOTIVO: ".$condicion_arrendamiento[$item["arrendamiento"]["condicion"]],'0','L');
				$this->SetXY(85,$y);$this->MultiCell(120,5,"Fec. Cnt.: ".Date::format($item["arrendamiento"]["feccon"]->sec,"d/m/Y")."-".Date::format($item["arrendamiento"]["fecven"]->sec,"d/m/Y"),'0','L');
				$y+=5;
				foreach($item["arrendamiento"]["rentas"] as $anio){
					if($y+40>285){
							$this->AddPage();
							$y=$y_ini;
					}
					$this->SetFont('arial','B',8);
					$this->SetXY(5,$y);$this->MultiCell(20,5,$anio["ano"],'0','L');
					$this->SetXY(5,$y+5);$this->MultiCell(20,5,"Alquiler",'0','L');
					$this->SetXY(5,$y+10);$this->MultiCell(20,5,"Moras 2%",'0','L');
					$this->SetXY(5,$y+15);$this->MultiCell(20,5,"I.G.V.",'0','L');
					$this->Line(5,$y+20,292,$y+20);
					$this->SetXY(5,$y+20);$this->MultiCell(20,5,"Total S/.",'0','L');
					$this->SetXY(5,$y+25);$this->MultiCell(20,5,"Total US$",'0','L');
					$this->SetFont('arial','',8);
					$tot_1 = 0;
					$tot_2 = 0;
					$tot_3 = 0;
					$tot_4 = 0;
					foreach($anio["rentas"] as $r=>$rent){
						$mes = floatval(Date::format($rent["fecven"]->sec,"m"));
						$x = $mes*20;
						$fec = new DateTime(Date::format($rent["fecven"]->sec,"Y-m-d"));
						$dif = $fec->diff($now);
						$meses = ( $dif->y * 12 ) + $dif->m;
						if($dif->d>1){
							$meses +=1;
						}
						$alq = $rent["importe"];
						$mor = $meses*0.02*$alq;
						$igv = $rent["importe"]*0.18;
						$tot = $alq+$mor+$igv;
						$this->SetXY($x,$y);$this->MultiCell(20,5,Date::format($rent["fecven"]->sec,"d/m/Y"),'0','R');
						$this->SetXY($x,$y+5);$this->MultiCell(20,5,number_format($alq,2),'0','R');
						$this->SetXY($x,$y+10);$this->MultiCell(20,5,($meses*2)."% ".number_format($mor,2),'0','R');
						$this->SetXY($x,$y+15);$this->MultiCell(20,5,"18% ".number_format($igv,2),'0','R');
						//$this->Line($x,$y+20,$x+20,$y+20);
						$this->SetXY($x,$y+($item["arrendamiento"]["moneda"]=="D"?25:20));$this->MultiCell(20,5,number_format($tot,2),'0','R');
						$tot_1 += $alq;
						$tot_2 += $mor;
						$tot_3 += $igv;
						$tot_4 += $tot;
						if($item["arrendamiento"]["moneda"]=="D"){
							$total_d_all+=$tot;
						}else{
							$total_s_all+=$tot;
						}
					}
					if($item["arrendamiento"]["moneda"]=="S"){
						$this->SetXY(255,$y+5);$this->MultiCell(20,5,number_format($tot_1,2),'0','R');
						$this->SetXY(255,$y+10);$this->MultiCell(20,5,number_format($tot_2,2),'0','R');
						$this->SetXY(255,$y+15);$this->MultiCell(20,5,number_format($tot_3,2),'0','R');
						//$this->Line(255,$y+20,265,$y+20);
						$this->SetXY(255,$y+20);$this->MultiCell(20,5,number_format($tot_4,2),'0','R');
						
						$this->SetXY(270,$y+5);$this->MultiCell(20,5,"0.00",'0','R');
						$this->SetXY(270,$y+10);$this->MultiCell(20,5,"0.00",'0','R');
						$this->SetXY(270,$y+15);$this->MultiCell(20,5,"0.00",'0','R');
						//$this->Line(270,$y+20,290,$y+20);
						$this->SetXY(270,$y+25);$this->MultiCell(20,5,"0.00",'0','R');
					}else{
						$this->SetXY(255,$y+5);$this->MultiCell(20,5,"0.00",'0','R');
						$this->SetXY(255,$y+10);$this->MultiCell(20,5,"0.00",'0','R');
						$this->SetXY(255,$y+15);$this->MultiCell(20,5,"0.00",'0','R');
						//$this->Line(255,$y+20,265,$y+20);
						$this->SetXY(255,$y+20);$this->MultiCell(20,5,"0.00",'0','R');
						
						$this->SetXY(270,$y+5);$this->MultiCell(20,5,number_format($tot_1,2),'0','R');
						$this->SetXY(270,$y+10);$this->MultiCell(20,5,number_format($tot_2,2),'0','R');
						$this->SetXY(270,$y+15);$this->MultiCell(20,5,number_format($tot_3,2),'0','R');
						//$this->Line(270,$y+20,290,$y+20);
						$this->SetXY(270,$y+25);$this->MultiCell(20,5,number_format($tot_4,2),'0','R');
					}
					$y+=40;
				}
			}
		}
		$this->SetXY(10,$y+5);$this->MultiCell(277,5,"TOTAL ACUMULADO S/. ====> ".number_format($total_s_all,2),'0','R');
		$this->SetXY(10,$y+10);$this->MultiCell(277,5,"TOTAL ACUMULADO US$ ====> ".number_format($total_d_all,2),'0','R');
	}
	function Footer()
	{
    	//Footer de la pagina
	} 
}
$pdf=new expdientes('L','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
//$pdf->Filtros($filter);
$pdf->AddPage();
$pdf->Publicar($items,$arrendatario);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>