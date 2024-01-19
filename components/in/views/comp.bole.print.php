<?php
global $f;
$f->library('pdf');
setlocale(LC_ALL,"esp");
class doc extends FPDF{
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












	var $d;
	function head($items){
		$this->d = $items;
	}
	function Header(){
		$meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
		$this->AddFont('verdana','','verdana.php');
		$this->SetFont('verdana','',9);
		$cliente = $this->d["cliente"]["nomb"];
		if($this->d["cliente"]["tipo_enti"]=="P"){
			//if($this->d->cliente['cliente']['appat']!='')
				$cliente = $this->d["cliente"]["appat"]." ".$this->d["cliente"]["apmat"].', '.$cliente;
		}
		$this->setXY(23,31);$this->MultiCell(95,3,$cliente,0,'L',0);
		$domic = "";
		if(isset($this->d['cliente']['domicilios'])){
			foreach ($this->d['cliente']['domicilios'] as $domi) {
				if(isset($domi['tipo']))
					if($domi['tipo']=='PERSONAL')
						$domic = $domi['direccion'];
			}
			if($domic=='')
				$domic = $this->d["cliente"]["domicilios"][0]["direccion"];
		}
		if(isset($this->d['contrato'])){
			if($domic==''){
				$domic = $this->d['contrato']['inmueble']['direccion'];
			}
		}
		$this->SetFont('verdana','',7);
		//$this->setXY(26,43);$this->Cell(100,5,$domic,0,0,'L');
		$this->setXY(24,37);$this->MultiCell(110,3,$domic ,0,'L',0);
		$this->SetFont('verdana','',9);
		$doc_num = '';
		if(isset($this->d["cliente"]["docident"])){
			foreach($this->d["cliente"]["docident"] as $dident){
				if($dident["tipo"]=="DNI"){
					$doc_tipo = "DNI";
					$doc_num = $dident["num"];
				}
			}
		}
		if($doc_num==''){
			$doc_num = 'RUC '.$this->d["cliente"]["docident"][0]['num'];
		}
      if($doc_num=='RUC ') $doc_num = '';
		$this->SetFont('verdana','',12);
		$this->setXY(33,42);$this->Cell(100,5,$doc_num,0,0,'L');
		$this->SetFont('verdana','',10);
      
		$this->setXY(145,55);$this->Cell(8,5,Date::format($this->d["fecreg"]->sec, 'd'),0,0,'L');
		$this->setXY(161,55);$this->Cell(25,5,Date::format($this->d["fecreg"]->sec, 'm'),0,0,'L');
		$this->setXY(175,55);$this->Cell(10,5,Date::format($this->d["fecreg"]->sec, 'Y'),0,0,'L');

		//$this->setXY(160,40);$this->Cell(70,5,$this->d["num"],0,0,'L');
$this->SetFont('verdana','',9);
		$this->setXY(70,119);$this->Cell(8,5,'Arequipa, '.Date::format($this->d["fecreg"]->sec, 'd').' de '.$meses[intval(Date::format($this->d["fecreg"]->sec, 'n'))-1].' del '.Date::format($this->d["fecreg"]->sec, 'Y'),0,0,'L');
		$this->Line(78, 129, 130, 129);
      $this->SetFont('verdana','',7);
		$this->setXY(80,130);$this->Cell(8,5,'RECAUDACION INMUEBLES',0,0,'L');
		if($this->d['total']>=700){
			$this->d['tmp_vou'] = true;
		}
	}
	function Publicar($items){
		global $f;
		$true_inm = false;
		$incumplimiento = 0;
		$print_otros = true;
		$x_ini = 10;
		$f->library('helpers');
		$tmp_vou = false;
		$meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
		$monedas = array(
			"S"=>array("nomb"=>"SOLES","simb"=>"S/."),
			"D"=>array("nomb"=>"DOLARES AMERICANOS","simb"=>"US$")
		);
		$y = 62;
		$this->SetFont('verdana','',10);
		if(isset($items['alquiler'])){
			$this->SetFont('verdana','',11);
			$tmp_tipo_l = "INMUEBLE: ";
			if($items['contrato']['inmueble']['tipo']['nomb']=='EX NUEVO MILENIO'){
				if($items['inmueble']['direccion'] == "ESQ. AV. INDEPENDENCIA Y PSJE. SANTA ROSA SUB-LOTE A SUBLOTE A4")
					$tmp_tipo_l = "TERRENO: ";
				else
					$tmp_tipo_l = "STAND: ";

			}
			if($items['contrato']['inmueble']['tipo']['nomb']=='LA CHOCITA'){
				$tmp_tipo_l = "ESPACIO: ";
			}
			if($items['contrato']['inmueble']['tipo']['nomb']=='VARIOS'){
				$tmp_tipo_l = "POR OCUPACION ";
			}
			if(isset($items['inmueble'])){
				if(isset($items['inmueble']['abrev'])){
					if($true_inm == false){
						$this->setXY(15,50);$this->MultiCell(130,5,$tmp_tipo_l.$items['inmueble']['abrev'].' ('.$items['inmueble']['tipo']['nomb'].')',0,'L',0);
						$true_inm = true;
					}
				}
			}
			$this->SetFont('verdana','',10);
			$total_alq = 0;
			$total_igv = 0;
			$total_mor = 0;
			if(!isset($items['sunat'])){
			$alquileres = array();
			foreach ($items['items'] as $key => $row) {
				$total_alq += floatval($row['conceptos'][0]['monto']);
				$total_alq += floatval($row['conceptos'][1]['monto']);
				if(isset($row['conceptos'][2]))
					$total_mor += floatval($row['conceptos'][2]['monto']);
				$alquileres[] = $row;
			}
			/*if(($total_alq+$total_igv+$total_mor)!=floatval($items['total'])){
				$total_igv += floatval($items['total']) - ($total_alq+$total_igv+$total_mor);
			}*/
			$texto_alq = "";
			switch($items['contrato']['motivo']['_id']->{'$id'}){
				case '55316577bc795ba80100003b':
					//SIN CONTRATO
					$texto_alq = 'POR OCUPACION';
					break;
				case '55316565bc795ba801000037':
					//RENOVACION SIN CONTRATO
					$texto_alq = 'POR OCUPACION';
					break;
				case '5531652fbc795ba80100002d':
					//ACTA DE CONCILIACION
					$texto_alq = 'POR OCUPACION';
					break;
				case '5531656fbc795ba801000039':
					//RENOVACION
					$texto_alq = 'ALQUILER';
					break;
				case '5531654cbc795ba801000033':
					//NUEVO
					$texto_alq = 'ALQUILER';
					//$texto_alq = 'POR OCUPACION';
					break;
				case '5540f3c3bc795b7801000029':
					//convenios
					$texto_alq = 'ALQUILER';
					break;
				case '55316543bc795ba801000031':
					//convenios
					$texto_alq = 'POR AUTORIZACION';
					break;
				default: $texto_alq = 'asdasdasdasdsadasdas';
			}
			if(isset($items['compensacion'])){
				$texto_alq = 'POR COMPENSACION';
			}
			$dia_ini = intval(date('d', $items['contrato']['fecini']->sec));
			$size_alq = sizeof($alquileres);
			if($size_alq>3){
				if($dia_ini==1){
					$texto_alq_ = $texto_alq.' '.$meses[$alquileres[0]['pago']['mes']-1].'-'.$alquileres[0]['pago']['ano'].' AL '.$meses[$alquileres[$size_alq-1]['pago']['mes']-1].'-'.$alquileres[$size_alq-1]['pago']['ano'];
				}else{
					//$texto_alq_ = $texto_alq.' '.$meses[$alquileres[0]['pago']['mes']-1].' DEL '.$alquileres[0]['pago']['ano'].' A '.$meses[$alquileres[$size_alq-1]['pago']['mes']-1].'-'.$alquileres[$size_alq-1]['pago']['ano'];
					$mes_ante = $alquileres[0]['pago']['mes']-1;
					$ano_ante = $alquileres[0]['pago']['ano'];
					if($mes_ante==-1){
						$mes_ante = 12;
						$ano_ante--;
					}
					$texto_alq_ = $texto_alq.' DEL '.$dia_ini.'-'.$mes_ante.'-'.$ano_ante.' AL '.($dia_ini-1).'-'.$alquileres[$size_alq-1]['pago']['mes'].'-'.$alquileres[$size_alq-1]['pago']['ano'];
				}
				$texto_alq_ .= ' - cada mes '.$monedas[$items["moneda"]]["simb"].number_format($alquileres[0]['conceptos'][0]['monto']+$alquileres[0]['conceptos'][1]['monto'],2);
				$this->setXY($x_ini,$y);$this->Cell(25,5,$texto_alq_,0,0,'L');
				$tot_alq = 0;
				foreach($alquileres as $k => $alq) {
					$tot_alq += floatval($alq['conceptos'][0]['monto']);
					$tot_alq += floatval($alq['conceptos'][1]['monto']);
				}
				$this->setXY(165,$y);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($tot_alq,2),0,0,'R');
				$y += 5;
			}else{
				if(!isset($items['parcial'])){
					foreach($alquileres as $k => $alq) {
						if($dia_ini==1){
							$texto_alq_ = $texto_alq.' '.$meses[$alq['pago']['mes']-1].' DEL '.$alq['pago']['ano'];
						}else{
							$mes_ante = $alq['pago']['mes']-1;
							$ano_ante = $alq['pago']['ano'];
							if($mes_ante<1){
								$mes_ante = 12;
								$ano_ante--;
							}
							$texto_alq_ = $texto_alq.' DEL '.$dia_ini.'-'.$mes_ante.'-'.$ano_ante.' AL '.($dia_ini-1).'-'.$alq['pago']['mes'].'-'.$alq['pago']['ano'];
						}
						if(floatval($alquileres[0]['conceptos'][0]['monto'])==floatval($items['contrato']['importe'])){
							//
						}else{
							$tmp_vou = true;
							$porc = round((floatval($alquileres[0]['conceptos'][0]['monto'])*100)/floatval($items['contrato']['importe']),2);
							//$texto_alq_ .= ' ('.$porc.'%)';
						}
                      
                  if($items['num']==50215){
                    	$texto_alq_ = "POR OCUPACIÓN - DEL 31-08-2016 AL 04-09-2016";
                   }
		   if($items['num']==50985){
                    	$texto_alq_ = "POR OCUPACION DEL 28-12-2016 AL 31-12-2016";
                   }
						$this->setXY($x_ini,$y);$this->Cell(25,5,$texto_alq_,0,0,'L');
						$this->setXY(165,$y);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($alq['conceptos'][0]['monto']+$alq['conceptos'][1]['monto'],2),0,0,'R');
						$y += 4;
					}
					$y += 2;
				}else{
					//$this->setXY(20,$y);$this->Cell(25,5,'PARCIAL',0,0,'L');
					//$texto_alq .= ' - REINTEGRO';
					switch ($items['tipo_pago']) {
						case 'AN':
							$texto_alq .= ' - A CTA.';
							break;
						case 'AC':
							$texto_alq .= ' - A CTA.';
							break;
						case 'RE':
							$texto_alq .= ' - REINTEGRO';
							break;
						case 'CA':
							$texto_alq .= ' - CANCELACION';
							break;
						case 'CO':
							$texto_alq .= ' - COMPENSACION';
							break;
						default:
							$texto_alq .= ' - ';
							break;
					}
					if($dia_ini==1){
						$texto_alq_ = $texto_alq.' DE '.$meses[$alquileres[0]['pago']['mes']-1].' DEL '.$alquileres[0]['pago']['ano'];
					}else{
						$mes_ante = $alquileres[0]['pago']['mes']-1;
						$ano_ante = $alquileres[0]['pago']['ano'];
						if($mes_ante<1){
							$mes_ante = 12;
							$ano_ante--;
						}
						$texto_alq_ = $texto_alq.' DEL '.$dia_ini.'-'.$mes_ante.'-'.$ano_ante.' AL '.($dia_ini-1).'-'.$alquileres[0]['pago']['mes'].'-'.$alquileres[0]['pago']['ano'];
					}
					if(floatval($alquileres[0]['conceptos'][0]['monto'])==floatval($items['contrato']['importe'])){
						//
					}else{
						$tmp_vou = true;
						$porc = round((floatval($alquileres[0]['conceptos'][0]['monto'])*100)/floatval($items['contrato']['importe']),2);
						switch ($items['tipo_pago']) {
							case 'AN':
								//$texto_alq .= ' - A CTA. DE';
								break;
							case 'RE':
								//$texto_alq .= ' - REINTEGRO';
								break;
							case 'CA':
								//$texto_alq .= ' - CANCELACION';
								break;
							case 'CO':
								$texto_alq_ .= ' ('.$porc.'%)';
								break;
							default:
								//$texto_alq .= ' - REINTEGRO';
								break;
						}
					}
					/*if($items['observ']!=''){
						$this->setXY($x_ini,$y);$this->Cell(25,5,strtoupper($items['observ']),0,0,'L');
					}else{*/
					if($item['num']==50982){
						echo "asd";
						if($k==0){
							$texto_alq_ = "POR OCUPACION - DEL 16-12-2016 AL 31-12-2016";
						}
					}
                  //$texto_alq_ = "PAGO DEL 16 DE ABRIL DEL 2016 AL 30 DE ABRIL DEL 2016";
                  //$texto_alq_ = 'POR OCUPACION A CTA. DE NOVIEMBRE 2015';
                  if($items['num']==49557){
                    $texto_alq_ = 'POR OCUPACION A CTA. DE NOVIEMBRE 2005';
                   }
                  if($items['num']==49448){
                    	$texto_alq_ = "ALQUILER - CANCELACION MARZO 2016";
                   }
                  if($items['num']==49591){
                    	$texto_alq_ = "POR OCUPACIÓN 23 DÍAS OCTUBRE 2015";
                   }
		   if($items['num']==50967){
                    	$texto_alq_ = "ALQUILER - DEL 16-12-2016 AL 31-12-2016";
                   }
		   if($items['num']==50982){
                    	$texto_alq_ = "POR OCUPACION DEL 16-12-2016 AL 31-12-2016";
                   }
		   if($items['num']==50983){
                    	$texto_alq_ = "ALQUILER - DEL 16-12-2016 AL 31-12-2016";
                   }
		   if($items['num']==50991){
                    	$texto_alq_ = "CANCELACION DEL 16-12-2016 AL 31-12-2016";
                   }
		   if($items['num']==51093){
                    	$texto_alq_ = "ALQUILER DEL 16-12-2016 AL 31-12-2016";
                   }
		   
		   
						$this->setXY($x_ini,$y);$this->Cell(25,5,$texto_alq_,0,0,'L');
					
					$this->setXY(165,$y);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($alquileres[0]['conceptos'][0]['monto']+$alquileres[0]['conceptos'][1]['monto'],2),0,0,'R');
					$y += 4;
					}
				}
			}else{
				foreach ($items['sunat'] as $item) {
					/*SOLUCIONES PARCHE*/
					if($items['num']==53532){
						$this->setXY($x_ini,$y);$this->Cell(25,5,"POR OCUPACION - CANCELACIÓN  POR DOS HORAS DEL 17.12.2017",0,0,'L');
						$y += 4;
						$this->setXY($x_ini,$y);$this->Cell(25,5,"(de 16.00 horas a 18.00 horas)",0,0,'L');
					}
					if($items['num']==53603){
						$this->setXY($x_ini,$y);$this->Cell(25,5,"POR OCUPACION- CANCELACION POR DOS HORAS DEL DIA 30.12.2017",0,0,'L');
						$y += 4;
						$this->setXY($x_ini,$y);$this->Cell(25,5,"(de 10:00 horas a 12:00 horas)",0,0,'L');
					}
					if($items['num']==53604){
						$this->setXY($x_ini,$y);$this->Cell(25,5,"POR OCUPACION - CANCELACIÓN  POR DOS HORAS DEL 07.01.2018",0,0,'L');
						$y += 4;
						$this->setXY($x_ini,$y);$this->Cell(25,5,"(de 10.00 horas a 12.00 horas)",0,0,'L');
					}
					else{
						$this->setXY($x_ini,$y);$this->Cell(25,5,$item['descr'],0,0,'L');
					}
					
					//$this->setXY(165,$y);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($items['sunat'][0]['valor_unitario']*$items['sunat'][0]['cant']+(($items['sunat'][0]['valor_unitario']*$items['sunat'][0]['cant'])*0.18),2),0,0,'R');
					$this->setXY(165,$y);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($item['importe_total']-$item['ope_inafectas'],2),0,0,'R');
					$y += 4;
					$total_mor += floatval($item['ope_inafectas']);
				}
			}
		}elseif(isset($items['acta_conciliacion'])){
			$this->SetFont('verdana','',12);
			$this->setXY($x_ini,55);$this->Cell(130,5,'INMUEBLE: '.$items['acta_conciliacion']['inmueble']['direccion'].' ('.$items['acta_conciliacion']['inmueble']['tipo']['nomb'].')',0,0,'C');
			$this->SetFont('verdana','',10);
			$total_alq = 0;
			$total_igv = 0;
			$total_mor = 0;
			$alquileres = array();
			$montos = array();
			$cuotas = array();
        	$tmp_i = 0;
			foreach ($items['items'] as $key => $row) {
				$cuotas[] = $row['pago']['num'];
				$incumplimiento = 0;
				foreach ($row['conceptos'] as $conc) {
					if($conc['cuenta']['cod']=='1202.0901.47'){
						if(isset($conc['incumplimiento'])){
							$incumplimiento += floatval($conc['monto']);
						}else{
							$total_mor += $conc['monto'];
						}
					}elseif($conc['cuenta']['cod']=='2101.010503.47' || $conc['cuenta']['cod']=='2101.010501'){
						$total_alq += $conc['monto'];
                        //$cuotas[] = $conc['monto'];
                        $montos[$tmp_i] += $conc['monto'];
					}else{
						$total_alq += $conc['monto'];
                      $tmp_i = sizeof($montos);
						$alquileres[] = $conc;
                        $montos[] = $conc['monto'];
					}
				}
			}
			$texto_alq = "";
			$size_alq = sizeof($alquileres);
			if($size_alq>30){
				//
			}else{
				foreach($alquileres as $k => $alq) {
					$texto_alq_ = $alq['concepto'];
					/*if($items['observ']!=''){
						$texto_alq_ = $items['observ'];
						$items['observ'] = '';
					}*/
					if($k==0&&$items['observ_detalle']!=''){
						$texto_alq_ = $items['observ_detalle'];
						$items['observ'] = '';
					}
					$this->setXY($x_ini,$y);$this->Cell(25,5,$texto_alq_,0,0,'L');
					//$this->setXY(165,$y);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($total_alq,2),0,0,'R');
                  $this->setXY(165,$y);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($montos[$k],2),0,0,'R');
					$y += 4;
				}
				$y += 2;
			}
			$observ = "CORRESPONDE A ";
			foreach ($cuotas as $k=>$cuota) {
				if($k!=0){
					$observ .= ', ';
				}
				$observ .= "CUOTA ".$cuota;
			}
			$observ .= ' - '.$items['acta_conciliacion']['num'];
			if($items['observ']==''){
				$items['observ'] = $observ;
			}
			if($items['observ_general']!=''){
				$items['observ'] = $items['observ_general'];
			}
		}elseif(isset($items['combinar_alq'])){
			$this->SetFont('verdana','',11);
            $tmp_tipo_l = "INMUEBLE: ";
            if(isset($items['contrato'])){
            	if(isset($items['contrato']['inmueble'])){
		            if($items['contrato']['inmueble']['tipo']['nomb']=='EX NUEVO MILENIO'){
		              $tmp_tipo_l = "TERRENO: ";
		             }
		          if($items['contrato']['inmueble']['tipo']['nomb']=='LA CHOCITA'){
		              $tmp_tipo_l = "ESPACIO: ";
		             }
		          if($items['contrato']['inmueble']['tipo']['nomb']=='VARIOS'){
		              $tmp_tipo_l = "ESPACIO: ";
		             }
		          }
	        }
            $inmus = false;
	    $true_inm = false;
            $tmp_inm;
            foreach ($items['items'] as $i=>$item) {
				if(isset($items['contrato'])){
					$item['contrato'] = $f->model('in/cont')->params(array('_id'=>$item['contrato']))->get('one')->items;
	             	if($i==0) $tmp_inm = $item['contrato']['inmueble']['_id']->{'$id'};
	             	else{
	             		if($item['contrato']['inmueble']['_id']->{'$id'}!=$tmp_inm){
					$inmus = true;
					$this->setXY(15,52);
					//$this->Cell(130,10,$tmp_tipo_l.$items['contrato']['inmueble']['direccion'].' ('.$items['contrato']['inmueble']['tipo']['nomb'].')',0,0,'L');
					$this->MultiCell(120,4,$tmp_tipo_l.$items['contrato']['inmueble']['direccion'].' ('.$items['contrato']['inmueble']['tipo']['nomb'].')',0,'L',0);
					$true_inm = true;
				}
	             	}
				}
			}
			if(isset($items['contrato'])){
				if(isset($items['contrato']['inmueble'])){
					if($inmus == false){
						if($true_inm == false){
							if(isset($items['contrato']['inmueble']['direccion'])){
								$this->setXY(15,52);
								//$this->Cell(130,10,$tmp_tipo_l.$items['contrato']['inmueble']['direccion'].' ('.$items['contrato']['inmueble']['tipo']['nomb'].')',0,0,'L');
								$this->MultiCell(120,4,$tmp_tipo_l.$items['contrato']['inmueble']['direccion'].' ('.$items['contrato']['inmueble']['tipo']['nomb'].')',0,'L',0);
								$true_inm = true;
							}
						}
					}else $true_inm = true;
				}
			}
			$this->SetFont('verdana','',10);
			$total_igv = 0;
			$total_mor = 0;
			if(!isset($items['sunat'])){
			foreach ($items['items'] as $index_item=>$comp) {
				if(isset($comp['alquiler'])){
					/***************************************************************************************************
					* COBRO DE ALQUILERES
					***************************************************************************************************/
					$alquileres = array();
					foreach ($comp['items'] as $row) {
						$total_alq += floatval($row['conceptos'][0]['monto']);
						$total_alq += floatval($row['conceptos'][1]['monto']);
						if(isset($row['conceptos'][2]))
							$total_mor += floatval($row['conceptos'][2]['monto']);
						$alquileres[] = $row;
					}
					$texto_alq = "";
					$comp['contrato'] = $f->model('in/cont')->params(array('_id'=>$comp['contrato']))->get('one')->items;
					switch($comp['contrato']['motivo']['_id']->{'$id'}){
						case '55316577bc795ba80100003b':
							//SIN CONTRATO
							$texto_alq = 'POR OCUPACION';
							break;
						case '55316565bc795ba801000037':
							//RENOVACION SIN CONTRATO
							$texto_alq = 'POR OCUPACION';
							break;
						case '5531652fbc795ba80100002d':
							//ACTA DE CONCILIACION
							$texto_alq = 'POR OCUPACION';
							break;
						case '5531656fbc795ba801000039':
							//RENOVACION
							$texto_alq = 'ALQUILER';
							break;
						case '5531654cbc795ba801000033':
							//NUEVO
							$texto_alq = 'ALQUILER';
							break;
						case '5540f3c3bc795b7801000029':
							//convenios
							$texto_alq = 'ALQUILER';
							break;
						case '55316543bc795ba801000031':
							//convenios
							$texto_alq = 'POR AUTORIZACION';
							break;
						default: $texto_alq = 'asdasdasdasdsadasdas';
					}
					if(isset($comp['compensacion'])){
						$texto_alq = 'POR COMPENSACION';
					}
					$dia_ini = intval(date('d', $comp['contrato']['fecini']->sec));
					$size_alq = sizeof($alquileres);
					if($size_alq>1){
						if($dia_ini==1){
							$texto_alq_ = $texto_alq.' '.$meses[$alquileres[0]['pago']['mes']-1].'-'.$alquileres[0]['pago']['ano'].' AL '.$meses[$alquileres[$size_alq-1]['pago']['mes']-1].'-'.$alquileres[$size_alq-1]['pago']['ano'];
						}else{
							$mes_ante = $alquileres[0]['pago']['mes']-1;
							$ano_ante = $alquileres[0]['pago']['ano'];
							if($mes_ante==0){
								$mes_ante = 12;
								$ano_ante--;
							}
							$texto_alq_ = $texto_alq.' DEL '.$dia_ini.'-'.$mes_ante.'-'.$ano_ante.' AL '.($dia_ini-1).'-'.$alquileres[$size_alq-1]['pago']['mes'].'-'.$alquileres[$size_alq-1]['pago']['ano'];
						}
						$texto_alq_ .= ' - cada mes '.$monedas[$items["moneda"]]["simb"].number_format($alquileres[0]['conceptos'][0]['monto']+$alquileres[0]['conceptos'][1]['monto'],2);
						if($inmus==true){
							$texto_alq_ = $comp['contrato']['inmueble']['direccion'].' - '.$texto_alq_;
						}
						//$this->setXY($x_ini,$y);$this->Cell(25,5,$texto_alq_,0,0,'L');
						$this->setXY($x_ini,$y);$this->Cell(25,5,$texto_alq_,0,0,'L');
						$tot_alq = 0;
						foreach($alquileres as $k => $alq) {
							$tot_alq += floatval($alq['conceptos'][0]['monto'])+$alq['conceptos'][1]['monto'];
						}
						$this->setXY(165,$y);$this->Cell(25,5,number_format($tot_alq,2),0,0,'R');
						$y += 5;
					}else{
						if(!isset($comp['parcial'])){
							foreach($alquileres as $k => $alq) {
								if($dia_ini==1){
									$texto_alq_ = $texto_alq.' '.$meses[$alq['pago']['mes']-1].' DEL '.$alq['pago']['ano'];
								}else{
									$mes_ante = $alq['pago']['mes']-1;
									$ano_ante = $alq['pago']['ano'];
									if($mes_ante<=1){
										$mes_ante = 12;
										$ano_ante--;
									}
									$texto_alq_ = $texto_alq.' DEL '.$dia_ini.'-'.$mes_ante.'-'.$ano_ante.' AL '.($dia_ini-1).'-'.$alq['pago']['mes'].'-'.$alq['pago']['ano'];
								}
								if(floatval($alquileres[0]['conceptos'][0]['monto'])==floatval($comp['contrato']['importe'])){
									//
								}else{
									$tmp_vou = true;
									$porc = round((floatval($alquileres[0]['conceptos'][0]['monto'])*100)/floatval($comp['contrato']['importe']),2);
								}
								
								if($inmus==true){
									$texto_alq_ = $comp['contrato']['inmueble']['direccion'].' - '.$texto_alq_;
								}
								if($items['num']==49550){
									if($k==0)
										$texto_alq_ = "POR OCUPACION DEL 16-1-2016 AL 15-2-2016";
								}
								if($items['num']==49930){
									if($k==0)
										$texto_alq_ = "POR OCUPACION DEL 16-12-2015 AL 31-12-2015";
								}
								if($items['num']==50933){
									if($k==0){
										$texto_alq_ = "POR OCUPACION - DICIEMBRE 2016";
									}
									if($k==1){
										$texto_alq_ = "POR OCUPACION - CANCELO ALQUILER ABRIL 2016";
									}
									if($k==2){
										$texto_alq_ = "POR OCUPACION - ADELANTO ALQUILER MAYO 2016";
									}
								}
						if(isset($comp['observ'])){
							if($comp['observ']!='')
								$texto_alq_ = $comp['observ'];
						}
								$this->setXY($x_ini,$y);$this->Cell(25,5,$texto_alq_,0,0,'L');
								$this->setXY(165,$y);$this->Cell(25,5,number_format($alq['conceptos'][0]['monto']+$alq['conceptos'][1]['monto'],2),0,0,'R');
								$y += 4;
							}
							$y += 2;
						}else{
					switch ($comp['tipo_pago']) {
						case 'AN':
							$texto_alq .= ' - A CTA.';
							break;
						case 'RE':
							$texto_alq .= ' - REINTEGRO';
							break;
						case 'CA':
							$texto_alq .= ' - CANCELACION';
							break;
						case 'CO':
							$texto_alq .= ' - COMPENSACION';
							break;
						default:
							$texto_alq .= ' - ';//REINTEGRO
							break;
					}
							if($dia_ini==1){
								$texto_alq_ = $texto_alq.' DE '.$meses[$alquileres[0]['pago']['mes']-1].' DEL '.$alquileres[0]['pago']['ano'];
							}else{
								//$alquileres[0]['pago']['mes']--;
								$mes_ante = $alquileres[0]['pago']['mes']-1;
								$ano_ante = $alquileres[0]['pago']['ano'];
								if($mes_ante<=0){
									$mes_ante = 12;
									$ano_ante--;
								}
								$texto_alq_ = $texto_alq.' DEL '.$dia_ini.'-'.$mes_ante.'-'.$ano_ante.' AL '.($dia_ini-1).'-'.$alquileres[0]['pago']['mes'].'-'.$alquileres[0]['pago']['ano'];
								if($items['num']==49550){
									$texto_alq_ = "A CUENTA - POR OCUPACION DEL 16-2-2016 AL 15-3-2016";
								}
							}
							if(floatval($alquileres[0]['conceptos'][0]['monto'])==floatval($comp['contrato']['importe'])){
								//
							}else{
								$tmp_vou = true;
								$porc = round((floatval($alquileres[0]['conceptos'][0]['monto'])*100)/floatval($comp['contrato']['importe']),2);
								//$texto_alq_ .= ' ('.$porc.'%)';
							}
								if($items['num']==50191){
										$texto_alq_ = "ALQUILER DEL 10-08-2016 AL 20-08-2016";
								}
								if($items['num']==50299){
										if($index_item==0){
											$texto_alq_ = "POR OCUPACION - DEL 01-09-2016 AL 12-09-2016";
										}elseif($index_item==2){
											$texto_alq_ = "POR OCUPACION - REINTEGRO DE OCTUBRE-2015 A JULIO-2016 c/m 100.01";
										}elseif($index_item==3){
											$texto_alq_ = "POR OCUPACION - REINTEGRO DE ENERO-2015 A SETIEMBRE-2015 c/m 100.00";
										}
								}
								
						if($inmus==true){
							$texto_alq_ = $comp['contrato']['inmueble']['direccion'].' - '.$texto_alq_;
						}
								if($items['num']==50933){
									echo "aa";die();
									if($k==0){
										$texto_alq_ = "POR OCUPACION - DICIEMBRE 2016";
									}
									if($index_item==1){
										$texto_alq_ = "POR OCUPACION - CANCELO ALQUILER ABRIL 2016";
									}
									if($index_item==2){
										$texto_alq_ = "POR OCUPACION - ADELANTO ALQUILER MAYO 2016";
									}
								}
						if(isset($comp['observ'])){
							if($comp['observ']!='')
								$texto_alq_ = $comp['observ'];
						}
							$this->setXY($x_ini,$y);$this->Cell(25,5,$texto_alq_,0,0,'L');
							$this->setXY(165,$y);$this->Cell(25,5,number_format($alquileres[0]['conceptos'][0]['monto']+$alquileres[0]['conceptos'][1]['monto'],2),0,0,'R');
							$y += 4;
						}
					}
				}elseif(isset($comp['acta_conciliacion'])){

					/***************************************************************************************************
					* COBRO DE ACTAS
					***************************************************************************************************/
					$alquileres = array();
					$incumplimiento = 0;
					foreach ($comp['items'] as $row) {
						$cuotas[] = $row['pago']['num'];
						foreach ($row['conceptos'] as $conc) {
							if($conc['cuenta']['cod']=='1202.0901.47'){
								if(isset($conc['incumplimiento'])){
									$incumplimiento += floatval($conc['monto']);
								}else{
									$total_mor += $conc['monto'];
								}
							}elseif($conc['cuenta']['cod']=='2101.010503.47'|| $conc['cuenta']['cod']=='2101.010501'){
								$total_alq += $conc['monto'];
		                        //$cuotas[] = $conc['monto'];
		                        $montos[$tmp_i] += $conc['monto'];
							}else{
								if(!isset($total_alq)) $total_alq = 0;
								$total_alq += $conc['monto'];
								if(!isset($montos)) $montos = array();
								$tmp_i = sizeof($montos);
								$alquileres[] = $conc;
		                        $montos[] = $conc['monto'];
							}
						}
					}
					/*$texto_alq = "";
					$size_alq = sizeof($alquileres);
					if($size_alq>3){
						/* EN CASO QUE SEAN MAS DE 4 *
					}else{
						foreach($alquileres as $k => $alq) {
							$texto_alq_ = 'ACTA DE CONCILIACION - '.$alq['concepto'];
							$this->setXY($x_ini,$y);$this->Cell(25,5,$texto_alq_,0,0,'L');
							$this->setXY(165,$y);$this->Cell(25,5,number_format($alq['monto'],2),0,0,'R');
							$y += 4;
						}
					}*/
					$texto_alq = "";
					$size_alq = sizeof($alquileres);
					if($size_alq>30){
						//
					}else{
						foreach($alquileres as $k => $alq) {
							$texto_alq_ = $alq['concepto'];
							if($items['observ']!=''){
								$texto_alq_ = $items['observ'];
								$items['observ'] = '';
							}
							if($inmus==true){
								//$texto_alq_ = $comp['contrato']['inmueble']['direccion'].' - '.$texto_alq_;
								$texto_alq_ = 'POR OCUPACION - '.$texto_alq_;
							}
							$this->setXY($x_ini,$y);$this->Cell(25,5,$texto_alq_,0,0,'L');
							//$this->setXY(165,$y);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($alq['monto'],2),0,0,'R');
		                			$this->setXY(165,$y);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($montos[$k],2),0,0,'R');
							$y += 4;
						}
						if($incumplimiento!=0){
							$this->setXY($x_ini,$y);$this->Cell(25,5,'MORAS POR INCUMPLIMIENTO',0,0,'L');
		                	$this->setXY(165,$y);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($incumplimiento,2),0,0,'R');
							$y += 4;
						}
						$y += 2;
					}
					$observ = "CORRESPONDE A ";
					foreach ($cuotas as $k=>$cuota) {
						if($k!=0){
							$observ .= ', ';
						}
						$observ .= "CUOTA ".$cuota;
					}
					$observ .= ' - '.$comp['acta_conciliacion']['num'];
					$items['observ'] = $observ;

					$tmp_tipo_l = $tmp_tipo_l.$comp['acta_conciliacion']['inmueble']['direccion'].' ('.$comp['acta_conciliacion']['inmueble']['tipo']['nomb'].')';
				}else{
					/***************************************************************************************************
					* COBRO DE SERVICIOS
					***************************************************************************************************/
					if($true_inm==false){
						if(isset($items['inmueble'])){
							$true_inm = true;
							$this->setXY(20,54);$this->Cell(130,5,'INMUEBLE: '.$items['inmueble']['direccion'].' ('.$items['inmueble']['tipo']['nomb'].')',0,0,'C');
						}
					}
					$total_alq = 0;
					$texto_alq_ = '';
					foreach ($comp['items'] as $row) {
						$texto_alq_ = $row['cuenta_cobrar']['servicio']['nomb'];
						foreach ($row['conceptos'] as $conc) {
							if($conc['cuenta']['cod']=='1202.0901.47'){
								$total_mor += $conc['monto'];
							}elseif($conc['cuenta']['cod']=='2101.010503.47'){
								$total_alq += $conc['monto'];
							}else{
								$total_alq += $conc['monto'];
							}
						}
					}
					if(isset($comp['observ'])){
						if($comp['observ']!='')
							$texto_alq_ = $comp['observ'];
					}
					$this->setXY($x_ini,$y);$this->Cell(25,5,$texto_alq_,0,0,'L');
					$this->setXY(165,$y);$this->Cell(25,5,number_format($total_alq,2),0,0,'R');
					$y += 4;
				}
				if($true_inm==false){
					$this->setXY(15,54);$this->Cell(130,5,$tmp_tipo_l,0,0,'C');
					$true_inm = true;
					}
				}
			}else{
				foreach ($items['sunat'] as $key => $comp_sun) {
					$this->setXY($x_ini,$y);$this->Cell(25,5,$comp_sun['descr'],0,0,'L');
					if($comp_sun['inafecto']=='false'){
						$this->setXY(165,$y);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($comp_sun['importe_total'],2),0,0,'R');
					}else{
						$this->setXY(165,$y);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($comp_sun['importe_total']-$comp_sun['ope_inafectas'],2),0,0,'R');
					}
					$y += 4;
					$total_mor += floatval($comp_sun['ope_inafectas']);
				}
			}
		}else{
			if(!isset($items['sunat'])){
				$this->SetFont('verdana','',11);
				if(isset($items['inmueble'])){
					if(strtoupper(substr($items['observ'],0,8))!='GARANTIA'){
						if($print_otros==false){
			            /*$this->setXY($x_ini,$y);$this->MultiCell(150,3,$texto_alq_,0,'L',0);
						if(!isset($items["moneda"])) $items["moneda"] = 'S';
						$this->setXY(165,$y);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($total_alq,2),0,0,'R');*/
						//$y += 6;
					}
						if($items['contrato']['inmueble']['tipo']['nomb']!='EX NUEVO MILENIO'){
							$this->setXY(20,54);$this->Cell(130,5,'INMUEBLE: '.$items['inmueble']['direccion'].' ('.$items['inmueble']['tipo']['nomb'].')',0,0,'C');
						}else{
							$this->setXY(20,53);$this->MultiCell(120,4,'TERRENO: '.$items['inmueble']['direccion'].' ('.$items['inmueble']['tipo']['nomb'].')',0,'L',0);
							///$this->MultiCell(120,4,$tmp_tipo_l.$items['contrato']['inmueble']['direccion'].' ('.$items['contrato']['inmueble']['tipo']['nomb'].')',0,'L',0);
						}
					}
				}
	          //$y += 6;
				$this->SetFont('verdana','',10);
				$total_alq = 0;
				$total_igv = 0;
				$total_mor = 0;
				$texto_alq_ = '';
				$print_otros = true;
				if(isset($items['items'])){
					foreach ($items['items'] as $key => $row) {
						$texto_alq_ = $row['cuenta_cobrar']['servicio']['nomb'];
						//echo $texto_alq_;
						foreach ($row['conceptos'] as $conc) {
							if($conc['cuenta']['cod']=='1202.0901.47'){
								$total_mor += $conc['monto'];
							}elseif($conc['cuenta']['cod']=='2101.010503.47'){
								$total_alq += $conc['monto'];
							}else{
								$total_alq += $conc['monto'];
							}
						}
					}
				}elseif(isset($items['conceptos'])){
					foreach ($items['conceptos'] as $key => $row) {
						if(is_array($row['concepto']))
							$texto_alq_ = $row['concepto']['nomb'];
						else
							$texto_alq_ = $row['concepto'];
						if($row['concepto']['cod']=='1202.0901.47'){
							$total_mor += $row['monto'];
						}elseif($row['concepto']['cod']=='2101.010503.47'){
							$total_alq += $row['monto'];
							//$total_igv += $row['monto'];
						}elseif($row['cuenta']['cod']=='2101.010503.47'){
							$total_alq += $row['monto'];
							//$total_igv += $row['monto'];
						}else{
							$total_alq += $row['monto'];
						}
					}
					if(sizeof($items['conceptos'])>1){
						//$print_otros = false;
						if(is_array($items['conceptos'][0]['concepto']))
							$texto_alq_ = $items['conceptos'][0]['concepto']['nomb'];
						else
							$texto_alq_ = $items['conceptos'][0]['concepto'];
						/*if($print_otros==false){
			            $this->setXY($x_ini,$y);$this->MultiCell(150,3,$texto_alq_,0,'L',0);
						if(!isset($items["moneda"])) $items["moneda"] = 'S';
						$this->setXY(165,$y);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($total_alq,2),0,0,'R');
						$y += 6;
						$this->setXY($x_ini,$y);$this->Cell(25,5,'I.G.V.  18%',0,0,'L');
						$this->setXY(165,$y);$this->Cell(25,5,number_format($total_igv,2),0,0,'R');
						$y += 6;
					}*/
					}
				}
				if(isset($items['observ'])){
					if($items['observ']!=''){
						$texto_alq_ = $items['observ'];
						$items['observ'] = '';
					}
				}
				//$this->setXY($x_ini,$y);$this->Cell(25,5,$texto_alq_,0,0,'L');
				if($print_otros==true){
		            $this->setXY($x_ini,$y);$this->MultiCell(150,3,$texto_alq_,0,'L',0);
					if(!isset($items["moneda"])) $items["moneda"] = 'S';
					$this->setXY(165,$y);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($total_alq,2),0,0,'R');
					$y += 6;
				}
			}else{
				
				//$this->setXY(165,$y);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($items['sunat'][0]['valor_unitario']*$items['sunat'][0]['cant']+(($items['sunat'][0]['valor_unitario']*$items['sunat'][0]['cant'])*0.18),2),0,0,'R');
				foreach ($items['sunat'] as $item) {
					//$this->setXY($x_ini,$y);$this->MultiCell(150,5,$item['sunat']['descr'],0,'L',0);
					$this->setXY($x_ini,$y);$this->MultiCell(150,5,$item['sunat']['descr'],0,'L',0);
					$this->setXY(165,$y);$this->Cell(25,5,$monedas[$item["moneda"]]["simb"].number_format($item['sunat']['importe_total']-$item['sunat']['ope_inafectas'],2),0,0,'R');
					$y += 4;
					$total_mor += floatval($item['sunat']['ope_inafectas']);
				}
			}
		}
		// I.G.V.
		/*$this->setXY(20,$y);$this->Cell(25,5,'I.G.V.  18%',0,0,'L');
		$this->setXY(165,$y);$this->Cell(25,5,number_format($total_igv,2),0,0,'R');
		$y += 6;*/
		if($total_mor!=0){
			// MORAS
			if(isset($items['acta_conciliacion'])){
				//$this->setXY($x_ini,$y);$this->Cell(25,5,'MORAS POR INCUMPLIMIENTO',0,0,'L');
				$this->setXY($x_ini,$y);$this->Cell(25,5,'MORAS',0,0,'L');
			}else{
				$this->setXY($x_ini,$y);$this->Cell(25,5,'MORAS',0,0,'L');
			}
			$this->setXY(165,$y);$this->Cell(25,5,number_format($total_mor,2),0,0,'R');
			$y += 6;
		}
		if($incumplimiento!=0){
			$this->setXY($x_ini,$y);$this->Cell(25,5,'MORAS POR INCUMPLIMIENTO',0,0,'L');
			//$this->setXY(165,$y);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($total_alq,2),0,0,'R');
          	$this->setXY(165,$y);$this->Cell(25,5,number_format($incumplimiento,2),0,0,'R');
		}
		/*
		$this->setXY(20,54);$this->Cell(130,5,'LA PAZ 511-111-A (CASA-HABITACION)',0,0,'C');
		$this->setXY($x_ini,$y);$this->Cell(25,5,'MORAS POR INCUMPLIMIENTO',0,0,'L');
		$this->setXY(165,$y);$this->Cell(25,5,number_format(8.42,2),0,0,'R');
		$y += 6;
*/
$y_tmp = $this->getY();
if($y_tmp<100) $y_tmp = 100;
		$this->SetFont('verdana','',8);
		//$this->setXY($x_ini,$y_tmp);$this->Cell(25,5,$items['observ'],0,0,'L');
		$this->setXY($x_ini,$y_tmp);$this->MultiCell(130,4,$items['observ'],0,'L',0);
		$this->SetFont('verdana','',11);
		$total = $items['total'];
		if($items["moneda"]=='D'){
			$total = $items['total']/$items['tc'];
			$this->SetFont('verdana','',8);
			$this->setXY($x_ini,95);$this->Cell(25,5,'TIPO DE CAMBIO: S/.'.$items['tc'],0,0,'L');
		}
		$this->SetFont('verdana','',11);
		$this->Line(163, 96, 190, 96);
		$this->setXY(147,97);$this->Cell(25,5,'TOTAL',0,0,'L');
		$this->setXY(165,97);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($total,2),0,0,'R');
		$this->SetFont('verdana','',10);
		$y=110;
		$total = round($total,2);
		$decimal = round((($total-((int)$total))*100),0);
		if($decimal==0||$decimal<10) $decimal = '0'.$decimal;
		//print_r($total);
		
		$this->setXY($x_ini,$y);$this->Cell(163,5,'SON: '.Number::lit($total).' CON '.$decimal.'/100 '.$monedas[$items["moneda"]]["nomb"],0,0,'L');
		$this->SetFont('verdana','',7);
		$this->setXY($x_ini,$y+4);$this->Cell(163,5,'NOTA: Vencido el Plazo, MORA 2% Mensual acumulativo',0,0,'L');
		$this->SetFont('verdana','',8);
		$y+=4;
		if($items["moneda"]=='D'){
			$this->SetFont('verdana','',7);
			$this->setXY(153,$y);$this->Cell(25,5,"RECIBIDO EN",0,0,'L');
			//$y = 112;
			if(floatval($items['efectivos'][0]['monto'])!=0){
				//$this->setXY(163,$y);$this->Cell(25,5,'M.N. ',0,0,'L');
				$this->setXY(165,$y);$this->Cell(25,5,$monedas[$items['efectivos'][0]["moneda"]]["simb"].number_format($items['efectivos'][0]['monto'],2),0,0,'R');
				$y+=4;
			}
			if(floatval($items['efectivos'][1]['monto'])!=0){
				//$this->setXY(163,$y);$this->Cell(25,5,'M.E. ',0,0,'L');
				$this->setXY(165,$y);$this->Cell(25,5,$monedas[$items['efectivos'][1]["moneda"]]["simb"].number_format($items['efectivos'][1]['monto'],2),0,0,'R');
				$y+=4;
			}
		}
	}
}
//$pdf=new doc('P','mm',array(210,148));
$pdf=new doc('L','mm','A5');
$pdf->SetTitle("Boleta de Venta");
$pdf->Open();
$pdf->head($items);
$pdf->AddPage();
$pdf->Publicar($items);
if($print==true){
	$pdf->AutoPrint(true);
}
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();
?>