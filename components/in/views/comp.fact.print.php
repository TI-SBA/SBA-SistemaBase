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
		$this->d['tmp_vou'] = false;
	}
	function Header(){
		$meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
		$this->AddFont('verdana','','verdana.php');
		$this->SetFont('verdana','',7);
		$cliente = $this->d["cliente"]["nomb"];
		if($this->d["cliente"]["tipo_enti"]=="P"){
			//if($this->d->cliente['cliente']['appat']!='')
				$cliente = $this->d["cliente"]["appat"]." ".$this->d["cliente"]["apmat"].', '.$cliente;
		}
		$x_dif = 3;
		$y_dif = 1;
		$this->setXY(22+$x_dif,39+$y_dif);$this->MultiCell(95,3,$cliente,0,'L',0);
		$domic = "";
		foreach ($this->d['cliente']['domicilios'] as $domi) {
			if(isset($domi['tipo']))
				if($domi['tipo']=='FISCAL')
					$domic = $domi['direccion'];
		}
		if($domic=='')
			$domic = $this->d["cliente"]["domicilios"][0]["direccion"];
		$this->SetFont('verdana','',7);
		//$this->setXY(26,43);$this->Cell(100,5,$domic,0,0,'L');
		$this->setXY(22+$x_dif,45+$y_dif);$this->MultiCell(110,3,$domic ,0,'L',0);
		$this->SetFont('verdana','',9);
		$doc_num = '';
		if(isset($this->d["cliente"]["docident"])){		
			foreach($this->d["cliente"]["docident"] as $dident){
				if($dident["tipo"]=="RUC"){
					$doc_tipo = "RUC";
					$doc_num = $dident["num"];
				}
			}
		}
		$this->SetFont('verdana','',12);
		$this->setXY(22+$x_dif,51+$y_dif);$this->Cell(100,5,$doc_num,0,0,'L');
		$this->SetFont('verdana','',9);
		$this->setXY(150+$x_dif-1,62+$y_dif+2);$this->Cell(8,5,Date::format($this->d["fecreg"]->sec, 'd'),0,0,'L');
		$this->setXY(167+$x_dif-1,62+$y_dif+2);$this->Cell(25,5,Date::format($this->d["fecreg"]->sec, 'm'),0,0,'L');
		$this->setXY(179+$x_dif-1,62+$y_dif+2);$this->Cell(10,5,Date::format($this->d["fecreg"]->sec, 'Y'),0,0,'L');	
		
		//$this->setXY(160,40);$this->Cell(70,5,$this->d["num"],0,0,'L');
		if(!isset($this->d['sin_pago'])){
			if($this->d['num']!=50756 || $this->d['num']!=51166 || $this->d['num']!=51167){
				$this->setXY(81+$x_dif,128+$y_dif);$this->Cell(8,5,Date::format($this->d["fecreg"]->sec, 'd'),0,0,'L');
				$this->setXY(93+$x_dif,128+$y_dif);$this->Cell(25,5,$meses[intval(Date::format($this->d["fecreg"]->sec, 'n'))-1],0,0,'L');
				$anio = Date::format($this->d["fecreg"]->sec, 'Y');
				$anio = substr($anio, 3);
				$this->setXY(124+$x_dif,128+$y_dif);$this->Cell(10,5,$anio,0,0,'L');
			}
		}
		if($this->d['total']>=700){
			$this->d['tmp_vou'] = true;
		}
	}
	function Publicar($items){
		$x_dif = 2;
		$y_dif = 1;
		/*return 0;*/
		global $f;
		$f->library('helpers');
		$tmp_vou = false;
		$meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
		$monedas = array(
			"S"=>array("nomb"=>"SOLES","simb"=>"S/."),
			"D"=>array("nomb"=>"DOLARES AMERICANOS","simb"=>"US$")
		);
		$y = 75;
		$this->SetFont('verdana','',10);
		if(isset($items['alquiler'])){
			$this->SetFont('verdana','',12);
			$this->setXY(20+$x_dif,59+$y_dif);$this->Cell(130,5,'INMUEBLE: '.$items['contrato']['inmueble']['direccion'].' ('.$items['contrato']['inmueble']['tipo']['nomb'].')',0,0,'C');
			$this->SetFont('verdana','',10);
			$total_alq = 0;
			$total_igv = 0;
			$total_mor = 0;
			$alquileres = array();
			foreach ($items['items'] as $key => $row) {
				$total_alq += floatval($row['conceptos'][0]['monto']);
				$total_igv += floatval($row['conceptos'][1]['monto']);
				if(isset($row['conceptos'][2]))
					$total_mor += floatval($row['conceptos'][2]['monto']);
				$alquileres[] = $row;
			}
			if(($total_alq+$total_igv+$total_mor)!=floatval($items['total'])){
				$total_igv += floatval($items['total']) - ($total_alq+$total_igv+$total_mor);
			}
			if(!isset($items['sunat'])){
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
					$texto_alq_ .= ' - cada mes '.$monedas[$items["moneda"]]["simb"].number_format($alquileres[0]['conceptos'][0]['monto'],2);
					$this->setXY(20+$x_dif,$y+$y_dif);$this->Cell(25,5,$texto_alq_,0,0,'L');
					$tot_alq = 0;
					foreach($alquileres as $k => $alq) {
						$tot_alq += floatval($alq['conceptos'][0]['monto']);
					}
					$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($tot_alq,2),0,0,'R');
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
				                      if($items['num']=='50230'){
				                      	$texto_alq_ = "POR OCUPACION DEL 06-11-2016 AL 30-11-2016";
				                      }
						      if($items['num']=='50407'){
				                      	$texto_alq_ = "ALQUILER DE ENERO 2017 (50%)";
				                      }
							$this->setXY(20+$x_dif,$y+$y_dif);$this->Cell(25,5,$texto_alq_,0,0,'L');
							$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($alq['conceptos'][0]['monto'],2),0,0,'R');
							$y += 4;
						}
						$y += 2;
					}else{
						//$this->setXY(20+$x_dif,$y+$y_dif);$this->Cell(25,5,'PARCIAL',0,0,'L');
						//$texto_alq .= ' - PAGO PARCIAL';
						switch ($items['tipo_pago']) {
							case 'AN':
							$texto_alq .= ' - A CTA.';
							break;
						case 'AC':
							$texto_alq .= ' - A CTA.';
								break;
							case 'RE':
								$texto_alq .= '';
								break;
							case 'CA':
								$texto_alq .= ' - CANCELACION';
								break;
							case 'CO':
								$texto_alq .= ' - COMPENSACION';
								break;
							default:
								$texto_alq .= '';
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
							//$texto_alq_ .= ' ('.$porc.'%)';
						}
	                  //$texto_alq_ = 'asd';
						if($items['num']==49708){
							if($alquileres[0]['conceptos'][0]['monto']==19.77)
								$texto_alq_ = "POR OCUPACION - CANCELA EL DIA 22 DE MARZO 2016";
						}
						if($items['num']==49710){
							if($alquileres[0]['conceptos'][0]['monto']==14.41)
								$texto_alq_ = "POR OCUPACION - CANCELA EL DIA 22 DE MARZO 2016";
						}
						/*if($items['observ']!=''){
							$this->setXY(20,$y);$this->Cell(25,5,strtoupper($items['observ']),0,0,'L');
						}else{*/
							$this->setXY(20,$y);$this->Cell(25,5,$texto_alq_,0,0,'L');
						//}
						$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($alquileres[0]['conceptos'][0]['monto'],2),0,0,'R');
						$y += 4;
					}
				}
			}else{
				$this->setXY(20,$y+$y_dif);$this->MultiCell(150,3,$items['sunat'][0]['descr'],0,'L',0);
				$y_tmp = $this->GetY();
				$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($items['sunat'][0]['valor_unitario']*$items['sunat'][0]['cant'],2),0,0,'R');
				//$y += 4;
				$y = $y_tmp;
			}
		}elseif(isset($items['acta_conciliacion'])){

			$this->SetFont('verdana','',12);
			$this->setXY(20+$x_dif,59+$y_dif);$this->Cell(130,5,'INMUEBLE: '.$items['acta_conciliacion']['inmueble']['direccion'].' ('.$items['acta_conciliacion']['inmueble']['tipo']['nomb'].')',0,0,'C');
			$this->SetFont('verdana','',10);
          
          
          
          
          
          
          
          /*
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
					}elseif($conc['cuenta']['cod']=='2101.010503.47'){
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
					if($items['observ']!=''){
						$texto_alq_ = $items['observ'];
						$items['observ'] = '';
					}
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
			$items['observ'] = $observ;
          */
          
          
          
          
          
          
          
          
			if(!isset($items['sunat'])){
				$total_alq = 0;
				$total_igv = 0;
				$total_mor = 0;
				$alquileres = array();
				foreach ($items['items'] as $key => $row) {
					foreach ($row['conceptos'] as $conc) {
						if($conc['cuenta']['cod']=='1202.0901.47'){
							$total_mor += $conc['monto'];
						}elseif($conc['cuenta']['cod']=='2101.010503.47' || $conc['cuenta']['cod']=='2101.010501'){
							$total_igv += $conc['monto'];
						}else{
							$total_alq += $conc['monto'];
							$alquileres[] = $conc;
						}
					}
				}
				$texto_alq = "";
				$size_alq = sizeof($alquileres);
				if($size_alq>3){
					/*$texto_alq_ = $texto_alq.' '.$meses[$alq['pago']['mes']-1].' DEL '.$alq['pago']['ano'];
					$this->setXY(20+$x_dif,$y+$y_dif);$this->Cell(25,5,$texto_alq_,0,0,'L');
					$tot_alq = 0;
					foreach($alquileres as $k => $alq) {
						$tot_alq += floatval($alq['conceptos'][0]['monto']);
					}
					$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($tot_alq,2),0,0,'R');
					$y += 5;*/
					if($items['num']=='51230'){
						foreach($alquileres as $k => $alq) {
						$texto_alq_ = ''.$alq['concepto'];
							if($k==2){
								$this->setXY(20+$x_dif,$y+$y_dif);$this->Cell(25,5,"I.G.V.  18%",0,0,'L');
								$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,"S/. 332.24",0,0,'R');
								$y += 4;
								$this->setXY(20+$x_dif,$y+$y_dif);$this->Cell(25,5,$texto_alq_,0,0,'L');
								$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($alq['monto'],2),0,0,'R');
							}
							else{
								$this->setXY(20+$x_dif,$y+$y_dif);$this->Cell(25,5,$texto_alq_,0,0,'L');
								$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($alq['monto'],2),0,0,'R');
							}
							$y += 4;
						}
						$y += -2;
					}
				}else{
					foreach($alquileres as $k => $alq) {
						$texto_alq_ = ''.$alq['concepto'];
						$this->setXY(20+$x_dif,$y+$y_dif);$this->Cell(25,5,$texto_alq_,0,0,'L');
						$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($alq['monto'],2),0,0,'R');
						$y += 4;
					}
					$y += 2;
				}
				$observ = "CORRESPONDE A CLAUSULA QUINTA";
				foreach ($cuotas as $k=>$cuota) {
					if($k!=0){
						$observ .= ', ';
					}
					$observ .= "CUOTA ".$cuota;
				}
				$observ .= ' - '.$items['acta_conciliacion']['num'];
				$items['observ_acta'] = $observ;
			}else{
				$sunat_pendiente = array();
				foreach ($items['sunat'] as $key => $comp_sun) {
					if($comp_sun['codigo']=='MORA_INC'){
						$sunat_pendiente[] = $comp_sun;
						continue;
					}else{
						$this->setXY(20+$x_dif,$y+$y_dif);$this->Cell(25,5,$comp_sun['descr'],0,0,'L');
						$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format(floatval($comp_sun['ope_gravadas']),2),0,0,'R');
						$y += 4;	
					}
					$total_igv += floatval($comp_sun['importe_total'])-floatval($comp_sun['ope_gravadas'])-floatval($comp_sun['ope_inafectas']);
					$total_mor += floatval($comp_sun['ope_inafectas']);
				}
			}
		}elseif(isset($items['combinar_alq'])){
			$inmus = false;
			$tmp_inm;
			foreach ($items['items'] as $i=>$item) {
				$items['items'][$i]['contrato'] = $f->model('in/cont')->params(array('_id'=>$item['contrato']))->get('one')->items;
				if($i==0) $tmp_inm = $items['items'][$i]['contrato']['inmueble']['_id']->{'$id'};
				else{
					if($items['items'][$i]['contrato']['inmueble']['_id']->{'$id'}!=$tmp_inm) $inmus = true;
				}
			}
			//print_r($inmus);
			//print_r($items['contrato']['inmueble']);
			if($inmus == false){
			//if($inmus == true){
				$this->SetFont('verdana','',12);
				$this->setXY(20+$x_dif,59+$y_dif);$this->Cell(130,5,$items['contrato']['inmueble']['direccion'],0,0,'C');
			}
			$this->SetFont('verdana','',10);
			$total_igv = 0;
			$total_mor = 0;
			if(!isset($items['sunat'])){
				foreach ($items['items'] as $i_c=>$comp) {
					if(isset($comp['alquiler'])){
						/***************************************************************************************************
						* COBRO DE ALQUILERES
						***************************************************************************************************/
						$alquileres = array();
						foreach ($comp['items'] as $row) {
							$total_alq += floatval($row['conceptos'][0]['monto']);
							$total_igv += floatval($row['conceptos'][1]['monto']);
							if(isset($row['conceptos'][2]))
								$total_mor += floatval($row['conceptos'][2]['monto']);
							$alquileres[] = $row;
						}
						/*if(($total_alq+$total_igv+$total_mor)!=floatval($comp['total'])){
							$total_igv += floatval($items['total']) - ($total_alq+$total_igv+$total_mor);
						}*/
						$texto_alq = "";
						//$comp['contrato'] = $f->model('in/cont')->params(array('_id'=>$comp['contrato']))->get('one')->items;
						$comp['contrato'] = $items['items'][$i_c]['contrato'];
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
						if($size_alq>2){
							if($dia_ini==1){
								$texto_alq_ = $texto_alq.' '.$meses[$alquileres[0]['pago']['mes']-1].'-'.$alquileres[0]['pago']['ano'].' AL '.$meses[$alquileres[$size_alq-1]['pago']['mes']-1].'-'.$alquileres[$size_alq-1]['pago']['ano'];
							}else{
								$mes_ante = $alquileres[0]['pago']['mes']-1;
	                            $ano_ante = $alquileres[0]['pago']['ano'];
	                            if($mes_ante==-1){
	                                $mes_ante = 12;
	                                $ano_ante--;
	                            }
	                            $texto_alq_ = $texto_alq.' DEL '.$dia_ini.'-'.$mes_ante.'-'.$ano_ante.' AL '.($dia_ini-1).'-'.$alquileres[$size_alq-1]['pago']['mes'].'-'.$alquileres[$size_alq-1]['pago']['ano'];
							}
							$texto_alq_ .= ' - cada mes '.$monedas[$items["moneda"]]["simb"].number_format($alquileres[0]['conceptos'][0]['monto'],2);
							if($inmus==true){
								$texto_alq_ = $comp['contrato']['inmueble']['direccion'].' - '.$texto_alq_;
							}
						
						
						
							if($items['num']==50369){
								$texto_alq_ = 'asd';
							}
						
						
						
							$this->setXY(20+$x_dif,$y+$y_dif);$this->Cell(25,5,$texto_alq_,0,0,'L');
							$tot_alq = 0;
							foreach($alquileres as $k => $alq) {
								$tot_alq += floatval($alq['conceptos'][0]['monto']);
							}
							$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,number_format($tot_alq,2),0,0,'R');
							$y += 5;
						}else{
							if(!isset($comp['parcial'])){
								foreach($alquileres as $k => $alq) {
									if($dia_ini==1){
										$texto_alq_ = $texto_alq.' '.$meses[$alq['pago']['mes']-1].' DEL '.$alq['pago']['ano'];
									}else{
										$mes_ante = $alq['pago']['mes']-1;
										$ano_ante = $alq['pago']['ano'];
										if($mes_ante==-1){
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
										$texto_alq_ .= ' ('.$porc.'%)';
									}
							if($inmus==true){
								$texto_alq_ = $comp['contrato']['inmueble']['direccion'].' - '.$texto_alq_;
							}
									$this->setXY(20+$x_dif,$y+$y_dif);$this->Cell(25,5,$texto_alq_,0,0,'L');
									$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,number_format($alq['conceptos'][0]['monto'],2),0,0,'R');
									$y += 4;
								}
								$y += 2;
							}else{
								//$this->setXY(20+$x_dif,$y+$y_dif);$this->Cell(25,5,'PARCIAL',0,0,'L');
								$texto_alq .= '';
								switch ($comp['tipo_pago']) {
									case 'AN':
										$texto_alq .= ' - A CTA. DE';
										break;
									case 'AC':
										$texto_alq .= ' - A CTA. DE';
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
										$texto_alq .= ' - REINTEGRO';
										break;
								}
								if($dia_ini==1){
									$texto_alq_ = $texto_alq.' DE '.$meses[$alquileres[0]['pago']['mes']-1].' DEL '.$alquileres[0]['pago']['ano'];
								}else{
									$mes_ante = $alquileres[0]['pago']['mes']-1;
									$ano_ante = $alquileres[0]['pago']['ano'];
									if($mes_ante==-1){
										$mes_ante = 12;
										$ano_ante--;
									}
									$texto_alq_ = $texto_alq.' DEL '.$dia_ini.'-'.$mes_ante.'-'.$ano_ante.' AL '.($dia_ini-1).'-'.$alquileres[0]['pago']['mes'].'-'.$alquileres[0]['pago']['ano'];
								}
								if(floatval($alquileres[0]['conceptos'][0]['monto'])==floatval($comp['contrato']['importe'])){
									//
								}else{
									$tmp_vou = true;
									$porc = round((floatval($alquileres[0]['conceptos'][0]['monto'])*100)/floatval($comp['contrato']['importe']),2);
									//$texto_alq_ .= ' ('.$porc.'%)';
	                             					/* $this->SetFont('verdana','',12);
									$this->setXY(20+$x_dif,59+$y_dif);$this->Cell(130,5,'INMUEBLE: CCI 436-D (OFICINA)',0,0,'C');
									$this->SetFont('verDana','',10);*/
								}//$texto_alq_ = 'POR OCUPACIÓN 1 DE MARZO 2016 AL 22 DE MARZO 2016';
								if($items['num']==49707){
									if($alquileres[0]['conceptos'][0]['monto']==949.15)
										$texto_alq_ = "POR OCUPACION DEL 1 AL 21 DE MARZO 2016";
									if($alquileres[0]['conceptos'][0]['monto']==25.43){
										$texto_alq_ = "POR OCUPACION A CUENTA DEL 22 DE MARZO 2016";
										$this->setXY(20,57);$this->Cell(130,5,'INMUEBLE: Av LA PAZ 511 Dpto 123-B (CASA-HABITACION)',0,0,'C');
									}
								}
								if($items['num']==49709){
									if($alquileres[0]['conceptos'][0]['monto']==516.1)
										$texto_alq_ = "POR OCUPACION DEL 1 AL 21 DE MARZO 2016";
									if($alquileres[0]['conceptos'][0]['monto']==10.17){
										$texto_alq_ = "POR OCUPACION A CUENTA DEL 22 DE MARZO 2016";
										$this->setXY(20,57);$this->Cell(130,5,'INMUEBLE: CCI 436-D (OFICINA)',0,0,'C');
									}
								}
								if($items['num']==50369){
									if($alquileres[0]['pago']['mes']=="4")
										$texto_alq_ = "ALQUILER - 50% DE ABRIL DEL 2016 (POR APORTE DE BIENES)";
									if($alquileres[0]['pago']['mes']=="3")
										$texto_alq_ = "ALQUILER - 50% DE MARZO DEL 2016 (POR APORTE DE BIENES)";
									if($alquileres[0]['pago']['mes']=="2")
										$texto_alq_ = "ALQUILER - 50% DE FEBRERO DEL 2016 (POR APORTE DE BIENES)";
									if($alquileres[0]['pago']['mes']=="1")
										$texto_alq_ = "ALQUILER - 50% DE ENERO DEL 2016 (POR APORTE DE BIENES)";
								}
							if($inmus==true){
								$texto_alq_ = $comp['contrato']['inmueble']['direccion'].' - '.$texto_alq_;
							}
								$this->setXY(20+$x_dif,$y+$y_dif);$this->Cell(25,5,$texto_alq_,0,0,'L');
								$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,number_format($alquileres[0]['conceptos'][0]['monto'],2),0,0,'R');
								$y += 4;
							}
						}
					}elseif(isset($comp['acta_conciliacion'])){
						/***************************************************************************************************
						* COBRO DE ACTAS
						***************************************************************************************************/
						$alquileres = array();
						foreach ($comp['items'] as $row) {
							foreach ($row['conceptos'] as $conc) {
								if($conc['cuenta']['cod']=='1202.0901.47'){
									$total_mor += $conc['monto'];
								}elseif($conc['cuenta']['cod']=='2101.010503.47'){
									$total_igv += $conc['monto'];
								}else{
									$total_alq += $conc['monto'];
									$alquileres[] = $conc;
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
								$texto_alq_ = $comp['contrato']['inmueble']['direccion'].' - '.$texto_alq_;
							}
								$this->setXY($x_ini+$x_dif,$y+$y_dif);$this->Cell(25,5,$texto_alq_,0,0,'L');
								//$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($total_alq,2),0,0,'R');
			                  $this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($montos[$k],2),0,0,'R');
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
						$observ .= ' - ACTA DE CONCILIACION '.$items['acta_conciliacion']['num'];
						$items['observ'] = $observ;
					}else{
						/***************************************************************************************************
						* COBRO DE SERVICIOS
						***************************************************************************************************/
						$total_alq = 0;
						$texto_alq_ = '';
						foreach ($comp['items'] as $row) {
							$texto_alq_ = $row['cuenta_cobrar']['servicio']['nomb'];
							foreach ($row['conceptos'] as $conc) {
								if($conc['cuenta']['cod']=='1202.0901.47'){
									$total_mor += $conc['monto'];
								}elseif($conc['cuenta']['cod']=='2101.010503.47'){
									$total_igv += $conc['monto'];
								}else{
									$total_alq += $conc['monto'];
								}
							}
						}
						if(isset($comp['observ'])){
							if($comp['observ']!='')
								$texto_alq_ = $comp['observ'];
						}
						$this->setXY(20+$x_dif,$y+$y_dif);$this->Cell(25,5,$texto_alq_,0,0,'L');
						$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,number_format($total_alq,2),0,0,'R');
						$y += 4;
					}
				}
			}else{
				foreach ($items['sunat'] as $key => $comp_sun) {
					$this->setXY(20+$x_dif,$y+$y_dif);$this->Cell(25,5,$comp_sun['descr'],0,0,'L');
					$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format(floatval($comp_sun['ope_gravadas']),2),0,0,'R');
					$y += 4;
					$total_igv += floatval($comp_sun['importe_total'])-floatval($comp_sun['ope_gravadas'])-floatval($comp_sun['ope_inafectas']);
					$total_mor += floatval($comp_sun['ope_inafectas']);
				}
			}
		}else{
			if(!isset($items['sunat'])){
				$this->SetFont('verdana','',12);
				//$this->setXY(20+$x_dif,59+$y_dif);$this->Cell(130,5,'INMUEBLE: '.$items['inmueble']['direccion'].' ('.$items['inmueble']['tipo']['nomb'].')',0,0,'C');
				$this->SetFont('verdana','',10);
				$total_alq = 0;
				$total_igv = 0;
				$total_mor = 0;
				$texto_alq_ = '';
	          	if(isset($items['items'])){
					$this->SetFont('verdana','',12);
					$this->setXY(20+$x_dif,59+$y_dif);$this->Cell(130,5,'INMUEBLE: '.$items['inmueble']['direccion'].' ('.$items['inmueble']['tipo']['nomb'].')',0,0,'C');
					$this->SetFont('verdana','',10);
					foreach ($items['items'] as $key => $row) {
						$texto_alq_ = $row['cuenta_cobrar']['servicio']['nomb'];
						foreach ($row['conceptos'] as $conc) {
							if($conc['cuenta']['cod']=='1202.0901.47'){
								$total_mor += $conc['monto'];
							}elseif($conc['cuenta']['cod']=='2101.010503.47'){
								$total_igv += $conc['monto'];
							}else{
								$total_alq += $conc['monto'];
							}
						}
					}
				}elseif(isset($items['conceptos'])){
					foreach ($items['conceptos'] as $key => $row) {
						//$texto_alq_ = $row['concepto']['nomb'];
						if(is_array($row['concepto']))
							$texto_alq_ = $row['concepto']['nomb'];
						else
							$texto_alq_ = $row['concepto'];
						if($row['concepto']['cod']=='1202.0901.47'){
							$total_mor += $row['monto'];
						}elseif($row['concepto']['cod']=='2101.010501'){
							$total_igv += $row['monto'];
						}elseif($row['cuenta']['cod']=='2101.010501'){
							$total_igv += $row['monto'];
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
					}
				}
				if(isset($items['observ'])){
					if($items['observ']!=''){
						$texto_alq_ = $items['observ'];
						$items['observ'] = '';
					}
							/*$total_alq += 4.24;
	              $total_igv += 0.76;
	              $items['moneda'] = 'S';*/
				}
				
				$tmp_total_alq = $total_alq;
	          	if($total_igv==0){
	            	$total_alq = $total_alq/1.18;
	            	$total_igv = $total_alq*0.18;
	            }

	            //if($items['num']=='50674' || $items['num']=='50673'){
	            if($items['num']=='50674' || $items['num']=='50673' || $items['num']=='51166' || $items['num']=='51167'){
					$total_alq = $tmp_total_alq;
	            	$total_igv = 0;
				}
				$this->setXY(20+$x_dif,$y+$y_dif);$this->MultiCell(130,3,$texto_alq_ ,0,'L',0);
				$y_tmp =$this->GetY();
				//$this->setXY(20+$x_dif,$y+$y_dif);$this->Cell(25,5,$texto_alq_,0,0,'L');
				$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($total_alq,2),0,0,'R');
				$y = $y_tmp;
			}else{
				foreach ($items['sunat'] as $key => $comp_sun) {
					$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format(floatval($comp_sun['ope_gravadas']),2),0,0,'R');
					$this->setXY(20+$x_dif,$y+$y_dif);$this->MultiCell(130,3,$comp_sun['descr'] ,0,'L',0);
					$y=$this->getY()+4;
					$total_igv += floatval($comp_sun['igv']);
				}
			}
		}
		/**********************************************/











		if(isset($items['observ_acta'])){
			$this->setXY(20+$x_dif,$y+15+$y_dif);$this->MultiCell(130,3,$items['observ_acta'],0,'L',0);
			$y+=5;
		}
		if($items['observ']!=''){
			$this->SetFont('verdana','',6);
			$this->setXY(20+$x_dif,$y+15+$y_dif);$this->MultiCell(130,3,$items['observ'] ,0,'L',0);
			$this->SetFont('verdana','',10);
		}


















      //$this->setXY(20+$x_dif,100+$y_dif);$this->MultiCell(130,3,$items['observ'] ,0,'L',0);
		// I.G.V.
		$y+=2;
		if($items['num']=='503331' || $items['num']=='50731' || $items['num']=='50831'){
			$total_igv = $items['igv'];
		}
		if($items['num']!='51230'){
			$this->setXY(20+$x_dif,$y+$y_dif);$this->Cell(25,5,'I.G.V.  18%',0,0,'L');
			$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,number_format($total_igv,2),0,0,'R');
		}

        //$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,number_format(1739.44,2),0,0,'R');
		$y += 6;
		if($total_mor!=0){
			// MORAS
			$this->setXY(20+$x_dif,$y+$y_dif);$this->Cell(25,0,'MORAS',0,0,'L');
			$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,0,number_format($total_mor,2),0,0,'R');
			$y += 6;
		}

		if(isset($sunat_pendiente)){
			if(count($sunat_pendiente)>0){
				foreach ($sunat_pendiente as $comp_sun) {
					$this->setXY(20+$x_dif,$y+$y_dif-3);$this->Cell(25,5,$comp_sun['descr'],0,0,'L');
					$this->setXY(165+$x_dif,$y+$y_dif-3);$this->Cell(25,5,number_format(floatval($comp_sun['ope_gravadas']),2),0,0,'R');
						$y += 5;	
				}
			}
		}

		if($items['num']=='50674' || $items['num']=='50673' || $items['num']=='51166' || $items['num']=='51167'){
			$this->SetFont('verdana','',7);
			$this->setXY(20+$x_dif,$y+$y_dif);$this->Cell(25,5,'EXONERADO DEL IMPUESTO GENERAL A LAS VENTAS DE ACUERDO A LA LEY 30347, ART. 19.1',0,0,'L');
			$y += 6;
		}
		$this->SetFont('verdana','',11);
		$total = $items['total'];
        //$total = 11403.04;
		$this->Line(163+$y_dif-0, 104+$x_dif-4, 190+$y_dif+2, 104+$x_dif-4);
		$this->setXY(147+$x_dif,105+$y_dif);$this->Cell(25,5,'TOTAL',0,0,'L');
		$this->setXY(165+$x_dif,105+$y_dif);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($total,2),0,0,'R');
		$this->SetFont('verdana','',10);
		$y=114;
		$decimal = round((($total-((int)$total))*100),0);
		if($decimal==0||$decimal<10) $decimal = '0'.$decimal;
		$this->setXY(15+$x_dif,$y+$y_dif);$this->Cell(163,5,Number::lit($total).' CON '.$decimal.'/100 '.$monedas[$items["moneda"]]["nomb"],0,0,'L');
		$this->SetFont('verdana','',8);
		$y+=4;
		//$this->setXY(167+$x_dif,104+$y_dif);$this->Cell(25,5,"RECIBIDO EN",0,0,'L');
		$y = 112;
		if($items['num']!='51167')
		if(isset($items['efectivos'])){
			if(floatval($items['efectivos'][0]['monto'])!=0){
				$this->setXY(152+$x_dif,$y+$y_dif);$this->Cell(25,5,'Efectivo ',0,0,'L');
				$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,$monedas[$items['efectivos'][0]["moneda"]]["simb"].number_format($items['efectivos'][0]['monto'],2),0,0,'R');
			        //$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,$monedas[$items['efectivos'][0]["moneda"]]["simb"].number_format(10262.74,2),0,0,'R');
				$y+=4;
			}
			if(floatval($items['efectivos'][1]['monto'])!=0){
				$this->setXY(152+$x_dif,$y+$y_dif);$this->Cell(25,5,'Efectivo ',0,0,'L');
				$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,$monedas[$items['efectivos'][1]["moneda"]]["simb"].number_format($items['efectivos'][1]['monto'],2),0,0,'R');
				$y+=4;
			}
		}
		if(isset($items['vouchers'])){
			foreach ($items['vouchers'] as $vouc) {
				$nomb = 'Detracción';
				switch($vouc['cuenta_banco']['cod']){
					case '101-089983':
						$nomb = 'Detracción';
						$tmp_vou = true;
					break;
				}
				$this->setXY(152+$x_dif,$y+$y_dif);$this->Cell(25,5,$nomb,0,0,'L');
				$this->setXY(165+$x_dif,$y+$y_dif);$this->Cell(25,5,$monedas[$vouc['moneda']]["simb"].number_format($vouc['monto'],2),0,0,'R');
				$y+=4;
			}
		}else{
			$tmp_vou = false;
			$this->d['tmp_vou'] = false;
		}
		if($tmp_vou==true) $this->d['tmp_vou'] = true;
	}
	function Footer(){
		//$x_dif = 2;
		//$y_dif = -2;
		//ANTES GIANCARLO
		//$x_dif = 2;
		//$y_dif = 1;
		$x_dif = 6;
		$y_dif = -5;
		if($this->d['tmp_vou']==true){
			$this->SetFont('verdana','',7);
			$this->setXY(130+$x_dif,132+$y_dif);$this->Cell(25,5,'Cuenta de Detracciones',0,0,'C');
			$this->setXY(130+$x_dif,135+$y_dif);$this->Cell(25,5,'Banco de la Nación',0,0,'C');
			$this->setXY(130+$x_dif,138+$y_dif);$this->Cell(25,5,'Cta.Cte.Nº101-089983',0,0,'C');
			$this->setXY(130+$x_dif,141+$y_dif);$this->Cell(25,5,'Según D.L.940',0,0,'C');
		}
	}
}
$pdf=new doc('L','mm','A5');
$pdf->SetTitle("Factura ".$items['serie'].'-'.$items['num']);
$pdf->Open();
$pdf->head($items);
$pdf->AddPage();
$pdf->Publicar($items);
if($print==true){
	$pdf->AutoPrint(true);
}
$pdf->Output();
?>