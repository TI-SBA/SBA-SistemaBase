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
		if($this->filter['liquidacion_penalidades']==1){
			$this->SetXY(10,10);$this->MultiCell(277,5,"LIQUIDACION DE PENALIDADES",'0','C');
		}else{
			$this->SetXY(10,10);$this->MultiCell(277,5,"LIQUIDACION DE ALQUILER",'0','C');
		}

		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(277,5,date("d/m/Y")." - Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Inmuebles",'0','C');
	}
	function Publicar($contratos, $pagos, $arrendatario, $inmueble, $liquidacion_penalidades){
		global $f;
		$f->library('helpers');
		$helper=new helper();
		$monedas = array(
			"S"=>"S/.",
			"D"=>"$"
		);
		$meses = array("--","ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE","S/.","US$");
		$meses_2 = array("--","ENE-FEB","FEB-MAR","MAR-ABR","ABR-MAY","MAY-JUN","JUN-JUL","JUL-AGO","AGO-SET","SET-OCT","OCT-NOV","NOV-DIC","DIC-ENE","S/.","US$");
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
		$this->SetXY(15,$y);$this->MultiCell(120,5,"TITULAR: ".$arren,'0','L');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(120,5,"INMUEBLE: ".$inmueble['direccion'],'0','L');
		$y+=5;
		$total_alq_all = 0;
		$total_mor_all = 0;
		$total_s_all = 0;
		$total_d_all = 0;
		for($i=1;$i<=14;$i++){
			$x=$i*20;
			$this->SetFont('arial','B',8);
			if( count($contratos) > 0 && floatval(date("d",$contratos[0]["fecini"]->sec))>=15){
				$this->SetXY($x,$y);$this->MultiCell(20,5,ucfirst($meses_2[$i]),'0','C');
			}else{
				$this->SetXY($x,$y);$this->MultiCell(20,5,ucfirst($meses[$i]),'0','C');
			}
		}
		$y+=5;
		foreach($contratos as $cont){
			$this->SetFont('arial','B',10);
			$this->SetXY(5,$y);$this->MultiCell(80,5,"MOTIVO: ".$cont["motivo"]["nomb"],'0','C');
			$fechas = date("d/m/Y",$cont["fecini"]->sec)."-".date("d/m/Y",$cont["fecfin"]->sec);
			if(isset($cont["fecdes"]->sec)){
				if($cont["fecdes"]->sec>0){
					$fechas.=' Fec.Desoc. '.date("d/m/Y",$cont["fecdes"]->sec);
				}
			}
			$this->SetXY(85,$y);$this->MultiCell(120,5,"Fec. Cnt.: ".$fechas,'0','L');
			$y+=5;
		}
		/*foreach($contratos as $cont){
			if($this->getY()+20>190){
				$this->addPage();
				$y = $y_ini;
			}
			for($i=1;$i<=14;$i++){
				$x=$i*20;
				$this->SetFont('arial','B',8);
				if(floatval(date("d",$cont["fecini"]->sec))>=15){
					$this->SetXY($x,$y);$this->MultiCell(20,5,ucfirst($meses_2[$i]),'0','C');
				}else{
					$this->SetXY($x,$y);$this->MultiCell(20,5,ucfirst($meses[$i]),'0','C');
				}
			}
			$y+=5;*/
			foreach($pagos as $key=>$pago){
				//$y=$this->getY()+10;
				//print_r($pago);die();
				if($this->getY()+20>190){
					$this->addPage();
					$y = $y_ini;
				}
				$this->SetFont('arial','B',8);
				$this->SetXY(5,$y);$this->MultiCell(20,5,'AÑO '.$key,'0','L');
				$this->SetXY(5,$y+5);$this->MultiCell(20,5,"Alquiler",'0','L');
				$this->SetXY(5,$y+10);$this->MultiCell(20,5,"Moras ".$pago[1]['mora_porc']."%",'0','L');
				$this->SetXY(5,$y+15);$this->MultiCell(20,5,"I.G.V.",'0','L');
				$this->Line(5,$y+20,292,$y+20);
				$this->SetXY(5,$y+20);$this->MultiCell(20,5,"Total S/.",'0','L');
				$this->SetXY(5,$y+25);$this->MultiCell(20,5,"Total US$",'0','L');
				$this->SetFont('arial','',8);
				$tot_1_s = 0;
				$tot_2_s = 0;
				$tot_3_s = 0;
				$tot_4_s = 0;

				$tot_1_d = 0;
				$tot_2_d = 0;
				$tot_3_d = 0;
				$tot_4_d = 0;

				$total_row_s = 0;
				$total_row_d = 0;
				//$aux = 0;
				$aux = 1;
				foreach($pago as $r=>$mes)
				{
					$this->SetFont('arial','',7);
					$x = $r*20;
					$alq = $mes["alquiler"];
					$mor = round(round($mes["mora_2"]*$alq,2)/100,2);
					$igv = $mes["igv"];
					$tot = $alq+$mor+$igv;
					if($liquidacion_penalidades==1){
						$alq = $mes["alquiler"];
						$mor = 0;
						$igv = $mes["igv"];
						$tot = $alq+$igv;
					}else{
						$alq = $mes["alquiler"];
						$mor = round(round($mes["mora_2"]*$alq,2)/100,2);
						$igv = $mes["igv"];
						$tot = $alq+$mor+$igv;
					}
					//print_r($aux);
					//if(($mes["alquiler"]>0) && ($pago[$aux]["mora_2"]!=$mes["mora_2"]))

					if(isset($mes["penalidad"]) && $mes["penalidad"]==true)
					{
						$habilitar_total=true;
						if($mes["moneda"]=="D")	$dolar = true;
						else $dolar = false;
					}

					//var_dump($mes["mora_2"]);
					//echo "br";
					//echo "br";

					//if(($mes["alquiler"]>0) && ($pago[$aux]["mora_2"]!=$mes["mora_2"]))
					if((($mes["alquiler"]>0) && ($pago[$aux]["mora_2"]!=$mes["mora_2"])) || (isset($mes["penalidad"]) && $mes["penalidad"]==true))
					{
						//print_r($mes);
						$mes_mora_2 = $mes["mora_2"];
						if(isset($mes['porcentaje_contrato'])){
							//$mes_mora_2 = floatval($mes['porcentaje_contrato']);
							//echo $mes_mora_2;
							//echo $mes['porcentaje_contrato'];
							if(floatval($mes['porcentaje_contrato'])==0){
								//print_r($mes['params_pago']);
								$mor = 0;
								$tot = $alq+$mor+$igv;
								$mes_mora_2 = 0;
							}
						}
						//if(floatval($mes["mora_2"])>0){
						if(floatval($mes["mora_2"])>0 || (isset($mes["penalidad"]) && $mes["penalidad"]==true) ){
							//print_r($mes);
							$this->SetXY($x,$y+5);$this->MultiCell(20,5,number_format($alq,2),'0','R');
							$this->SetXY($x,$y+10);$this->MultiCell(20,3,(($liquidacion_penalidades==1)?"0.00":$mes_mora_2)."% ".number_format($mor,2),'0','R');
							//$this->SetXY($x,$y+15);$this->MultiCell(20,5,"18% ".number_format($igv,2),'0','R');
							/*INAFECTO AL IGV por C.I. Nº784-2017-SBPA-OGA por penalidad*/
							if($igv>0){
								$this->SetXY($x,$y+15);$this->MultiCell(20,5,"18% ".number_format($igv,2),'0','R');
							}
							else{
								$this->SetXY($x,$y+15);$this->MultiCell(20,5,"0% ".number_format($igv,2),'0','R');
							}
							if($mes["moneda"]=="D")
							{
								$tot_1_d += $alq;
								$tot_2_d += $mor;
								$tot_3_d += $igv;
								$tot_4_d += $tot;
							}else{
								$tot_1_s += $alq;
								$tot_2_s += $mor;
								$tot_3_s += $igv;
								$tot_4_s += $tot;
							}
						}else{
							if(intval(date('m'))==$r){
								$this->SetXY($x,$y+5);$this->MultiCell(20,5,number_format($alq,2),'0','R');
								$this->SetXY($x,$y+10);$this->MultiCell(20,5,(($liquidacion_penalidades==1)?"0.00":$mes_mora_2)."% ".number_format($mor,2),'0','R');
								//$this->SetXY($x,$y+15);$this->MultiCell(20,5,"18% ".number_format($igv,2),'0','R');
								//print_r($igv);
								/*INAFECTO AL IGV por C.I. Nº784-2017-SBPA-OGA por penalidad*/
								if($igv>0){
									$this->SetXY($x,$y+15);$this->MultiCell(20,5,"18% ".number_format($igv,2),'0','R');
								}
								else{
									$this->SetXY($x,$y+15);$this->MultiCell(20,5,"0% ".number_format($igv,2),'0','R');
								}

								if($mes["moneda"]=="D")
								{
									$tot_1_d += $alq;
									$tot_2_d += $mor;
									$tot_3_d += $igv;
									$tot_4_d += $tot;
								}else{
									$tot_1_s += $alq;
									$tot_2_s += $mor;
									$tot_3_s += $igv;
									$tot_4_s += $tot;
								}
							}
						}
						$subtotal = 0;
						//$this->Line(5,$y+20,292,$y+20);
						if($mes["moneda"]=="D")
						{
							if(floatval($mes["mora_2"])>0 || (isset($mes["penalidad"]) && $mes["penalidad"]==true) ){
								$total_row_d+=$tot;
								$total_d_all+=$tot;
								$this->SetXY($x,$y+25);$this->MultiCell(20,5,number_format($tot,2),'0','R');
							}else{
								if(intval(date('m'))==$r){
									$total_row_d+=$tot;
									$total_d_all+=$tot;
									$this->SetXY($x,$y+25);$this->MultiCell(20,5,number_format($tot,2),'0','R');
								}
							}
						}
						else
						{
							if(floatval($mes["mora_2"] )>0  || (isset($mes["penalidad"]) && $mes["penalidad"]==true) ){
								$total_row_s+=$tot;
								$total_s_all+=$tot;
								$this->SetXY($x, $y+20);$this->MultiCell(20,5,number_format($tot,2),'0','R');
							}else{
								if(intval(date('m'))==$r){
									$total_row_s+=$tot;
									$total_s_all+=$tot;
									$this->SetXY($x, $y+20);$this->MultiCell(20,5,number_format($tot,2),'0','R');
								}
							}
						}
					}
					elseif($mes["alquiler"]>0){
						/*DEYANIRA error en liquidacion*/
						$mes_mora_2 = $mes["mora_2"];
						if(isset($mes['porcentaje_contrato'])){
							//$mes_mora_2 = floatval($mes['porcentaje_contrato']);
							//echo $mes_mora_2;
							//echo $mes['porcentaje_contrato'];
							if(floatval($mes['porcentaje_contrato'])==0){
								//print_r($mes['params_pago']);
								$mor = 0;
								$tot = $alq+$mor+$igv;
								$mes_mora_2 = 0;
							}
						}
						//if(floatval($mes["mora_2"])>0){
						if(floatval($mes["mora_2"])>0 || (isset($mes["penalidad"]) && $mes["penalidad"]==true) ){
							//print_r($mes);
							$this->SetXY($x,$y+5);$this->MultiCell(20,5,number_format($alq,2),'0','R');
							$this->SetXY($x,$y+10);$this->MultiCell(20,3,(($liquidacion_penalidades==1)?"0.00":$mes_mora_2)."% ".number_format($mor,2),'0','R');
							//$this->SetXY($x,$y+15);$this->MultiCell(20,5,"18% ".number_format($igv,2),'0','R');
							/*INAFECTO AL IGV por C.I. Nº784-2017-SBPA-OGA por penalidad*/
							if($igv>0){
								$this->SetXY($x,$y+15);$this->MultiCell(20,5,"18% ".number_format($igv,2),'0','R');
							}
							else{
								$this->SetXY($x,$y+15);$this->MultiCell(20,5,"0% ".number_format($igv,2),'0','R');
							}
							if($mes["moneda"]=="D")
							{
								$tot_1_d += $alq;
								$tot_2_d += $mor;
								$tot_3_d += $igv;
								$tot_4_d += $tot;
							}else{
								$tot_1_s += $alq;
								$tot_2_s += $mor;
								$tot_3_s += $igv;
								$tot_4_s += $tot;
							}
						}else{
							if(intval(date('m'))==$r){
								$this->SetXY($x,$y+5);$this->MultiCell(20,5,number_format($alq,2),'0','R');
								$this->SetXY($x,$y+10);$this->MultiCell(20,5,(($liquidacion_penalidades==1)?"0.00":$mes_mora_2)."% ".number_format($mor,2),'0','R');
								//$this->SetXY($x,$y+15);$this->MultiCell(20,5,"18% ".number_format($igv,2),'0','R');
								//print_r($igv);
								/*INAFECTO AL IGV por C.I. Nº784-2017-SBPA-OGA por penalidad*/
								if($igv>0){
									$this->SetXY($x,$y+15);$this->MultiCell(20,5,"18% ".number_format($igv,2),'0','R');
								}
								else{
									$this->SetXY($x,$y+15);$this->MultiCell(20,5,"0% ".number_format($igv,2),'0','R');
								}

								if($mes["moneda"]=="D")
								{
									$tot_1_d += $alq;
									$tot_2_d += $mor;
									$tot_3_d += $igv;
									$tot_4_d += $tot;
								}else{
									$tot_1_s += $alq;
									$tot_2_s += $mor;
									$tot_3_s += $igv;
									$tot_4_s += $tot;
								}
							}
						}
						$subtotal = 0;
						//$this->Line(5,$y+20,292,$y+20);
						if($mes["moneda"]=="D")
						{
							if(floatval($mes["mora_2"])>0 || (isset($mes["penalidad"]) && $mes["penalidad"]==true) ){
								$total_row_d+=$tot;
								$total_d_all+=$tot;
								$this->SetXY($x,$y+25);$this->MultiCell(20,5,number_format($tot,2),'0','R');
							}else{
								if(intval(date('m'))==$r){
									$total_row_d+=$tot;
									$total_d_all+=$tot;
									$this->SetXY($x,$y+25);$this->MultiCell(20,5,number_format($tot,2),'0','R');
								}
							}
						}
						else
						{
							if(floatval($mes["mora_2"] )>0  || (isset($mes["penalidad"]) && $mes["penalidad"]==true) ){
								$total_row_s+=$tot;
								$total_s_all+=$tot;
								$this->SetXY($x, $y+20);$this->MultiCell(20,5,number_format($tot,2),'0','R');
							}else{
								if(intval(date('m'))==$r){
									$total_row_s+=$tot;
									$total_s_all+=$tot;
									$this->SetXY($x, $y+20);$this->MultiCell(20,5,number_format($tot,2),'0','R');
								}
							}
						}
					}
					$aux++;
				}
				$_x = $x;
				if(floatval($tot_3_d)>0 || (isset($habilitar_total) && $habilitar_total==true && $dolar==true))
				{
					//print_r($tot_2);
					$tot_2 = round($tot_2,2);
					$x=$_x+35;
					$this->SetXY($x,$y+25);$this->MultiCell(20,5,number_format($total_row_d,2),'0','R');
					$this->SetXY($x,$y+5);$this->MultiCell(20,5,number_format($tot_1_d,2),'0','R');
					$this->SetXY($x,$y+10);$this->MultiCell(20,5,number_format($tot_2_d,2),'0','R');
					$this->SetXY($x,$y+15);$this->MultiCell(20,5,number_format($tot_3_d,2),'0','R');
				}

				if(floatval($tot_3_s)>0 || (isset($habilitar_total) && $habilitar_total==true && $dolar==false))
				{
					//print_r($tot_2);
					$tot_2_s = round($tot_2_s,2);
					$x=$_x+20;
					$this->SetXY($x,$y+20);$this->MultiCell(20,5,number_format($total_row_s,2),'0','R');
					$this->SetXY($x,$y+5);$this->MultiCell(20,5,number_format($tot_1_s,2),'0','R');
					$this->SetXY($x,$y+10);$this->MultiCell(20,5,number_format($tot_2_s,2),'0','R');
					$this->SetXY($x,$y+15);$this->MultiCell(20,5,number_format($tot_3_s,2),'0','R');
				}
				$y+=35;
				$total_alq_all += $tot_1_s + $tot_3_s;
				$total_mor_all += $tot_2_s;
				//echo $tot_2.'<br />';
			}
		//}
		if($this->getY()+20>200){
			$this->addPage();
			$y = $y_ini;
		}
		$this->SetXY(10,$y);$this->MultiCell(277,5,"TOTAL ALQUILERES S/. ====> ".number_format($total_alq_all,2),'0','R');
		$this->SetXY(10,$y+5);$this->MultiCell(277,5,"TOTAL MORAS S/. ====> ".number_format($total_mor_all,2),'0','R');
		$this->SetXY(10,$y+10);$this->MultiCell(277,5,"TOTAL ACUMULADO S/. ====> ".number_format($total_s_all,2),'0','R');
		$this->SetXY(10,$y+15);$this->MultiCell(277,5,"TOTAL ACUMULADO US$ ====> ".number_format($total_d_all,2),'0','R');
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
$pdf->Filtros(array('liquidacion_penalidades'=>$liquidacion_penalidades));
$pdf->AddPage();
$pdf->Publicar($contratos, $pagos, $arrendatario, $inmueble,$liquidacion_penalidades);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>