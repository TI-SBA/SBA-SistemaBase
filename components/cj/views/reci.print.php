<?php
global $f;
$tmp_obs = false;
$tmp_paralosrestos = false;
$tmp_cm_espa = '';
$f->library('pdf');
//setlocale(LC_ALL,"esp");
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
		if(isset($this->d["cliente"]["docident"])){		
			foreach($this->d["cliente"]["docident"] as $dident){
				if($this->d["cliente"]["tipo_enti"]=="P"){
					if($dident["tipo"]=="DNI"){
						$doc_tipo = "DNI";
						$doc_num = $dident["num"];
					}					
				}else{
					if($dident["tipo"]=="RUC"){
						$doc_tipo = "RUC";
						$doc_num = $dident["num"];
					}
				}
			}
		}
		$this->SetFont('courier','',12);
		$cliente = $this->d["cliente"]["nomb"];
		if(isset($this->d["cliente"]["appat"])){
			if($this->d["cliente"]["appat"]!=""){
				$cliente .= " ".$this->d["cliente"]["appat"]." ".$this->d["cliente"]["apmat"];
			}
		}
		
		$this->setXY(27,28);$this->Cell(100,5,$cliente." - ".$doc_tipo.": ".$doc_num,0,0,'L');
		if(isset($this->d["cliente"]["domicilios"][0]["direccion"])){
			$this->setXY(27,35);$this->Cell(100,5,$this->d["cliente"]["domicilios"][0]["direccion"],0,0,'L');	
		}
		//$this->setXY(42,62);$this->Cell(100,5,$doc_tipo." - ".$doc_num,0,0,'L');
		//$this->setXY(147,62);$this->Cell(8,5,Date::format($this->d["fecreg"]->sec, 'd'),0,0,'L');
		//$this->setXY(160,62);$this->Cell(25,5,Date::format($this->d["fecreg"]->sec, 'm'),0,0,'L');
		//$this->setXY(190,62);$this->Cell(10,5,Date::format($this->d["fecreg"]->sec, 'Y'),0,0,'L');
		$this->SetFont('courier','B',10);
		$this->setXY(155,40);$this->Cell(70,5,$this->d["serie"]." - ".$this->d["num"],0,0,'L');
	}	
	function Publicar($items){
		global $f;
		$f->library('helpers');
		$helper=new helper();
		$monedas = array(
			"S"=>array("nomb"=>"SOLES","simb"=>"S/."),
			"D"=>array("nomb"=>"DOLARES","simb"=>"US$")
		);
		$meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
		$y = 55;
		$this->SetFont('courier','',12);
		$tmp_paralosrestos = false;
		$tmp_block_asig = false;
		$tmp_pabe = false;
		$tmp_pabe_glosa = '';
		$observ_dif = true;
		$tmp_obs = true;
		foreach($items["items"] as $ij=>$row){
			$after = "";
			if(isset($row["cuenta_cobrar"]["modulo"])){
				if($row["cuenta_cobrar"]["modulo"]=="CM"){
				
					if(isset($row["cuenta_cobrar"]["operacion"])){
						
						/*if($helper->replace_acc($row["cuenta_cobrar"]["operacion"]["espacio"]["nomb"])!=$helper->replace_acc($row["cuenta_cobrar"]["operacion"]["espacio_nomb"])){
							//$observ_dif = "Se cambio el nombre del espacio ".$helper->replace_acc($row["cuenta_cobrar"]["operacion"]["espacio_nomb"])." por haberse realizado un traslado anterior al espacio actual.";
						}*/
					
					
					if(isset($row["cuenta_cobrar"]["operacion"]["espacio"])){
						$tmp_cm_espa = $helper->replace_acc($row["cuenta_cobrar"]["operacion"]["espacio"]["nomb"]);
					}else{
						$tmp_cm_espa = "";
					}
					
					if(isset($row["cuenta_cobrar"]["operacion"]["espacio"]["nicho"]["pabellon"])){
						if(isset($row["cuenta_cobrar"]["operacion"]["espacio"]["nicho"]["pabellon"]['glosa'])){
							if($tmp_pabe==false){
								$tmp_pabe_glosa .= $row["cuenta_cobrar"]["operacion"]["espacio"]["nicho"]["pabellon"]['glosa'];
								$tmp_pabe = true;
							}
						}
					}
					if(!isset($row["cuenta_cobrar"]["operacion"])){
						//
					}
					if(isset($row["cuenta_cobrar"]["operacion"]["concesion"])){
						if(isset($row["cuenta_cobrar"]["operacion"]["espacio"])){
							if(isset($row["cuenta_cobrar"]["operacion"]["espacio"]["mausoleo"])){
								/*
								 * Detalle para tipo de espacio
								 */
								$tmp_cm_espa = "";
							  $after .= "\n  ".$row["cuenta_cobrar"]["operacion"]["espacio"]["mausoleo"]["denominacion"];
								//$after .= "\n  MAUSOLEO: Zona ".$row["cuenta_cobrar"]["operacion"]["espacio"]["mausoleo"]["zona"].", Lote ".$row["cuenta_cobrar"]["operacion"]["espacio"]["mausoleo"]["lote"].", Medida ".$row["cuenta_cobrar"]["operacion"]["espacio"]["mausoleo"]["medidas"]["medida_total"]."mt.2, Capacidad ".$row["cuenta_cobrar"]["operacion"]["espacio"]["capacidad"].' cuerpos';
								//$after .= "\n  PARA LA FAMILIA: ".$row["cuenta_cobrar"]["operacion"]["mausoleo"]["denominacion"];
							}else{
								if(isset($row["cuenta_cobrar"]["operacion"]["espacio"]['nicho'])){
									if($row["cuenta_cobrar"]["operacion"]["espacio"]["nicho"]["tipo"]=='P')
										$after .= " Parvulo";
								}
								$tmp_cm_espa = "";
							  //if($ij==0)
								$after .= "\n  ESPACIO - ".$helper->replace_acc($row["cuenta_cobrar"]["operacion"]["espacio"]["nomb"]);
								//$after .= "\n  PARA:".$row["cuenta_cobrar"]["operacion"]["propietario"]["appat"]." ".$row["cuenta_cobrar"]["operacion"]["propietario"]["apmat"].", ".$row["cuenta_cobrar"]["operacion"]["propietario"]["nomb"];
							}
							/*
							 * Detalle para temporalidad
							 */
							if($row["cuenta_cobrar"]["operacion"]["concesion"]['condicion']=='T'){
								$tmp_obs = true;
								$after .= "\n  NOTA.- Tiene 90 dias para convertirlo en permanente, pagando intereses (a 30 dias 3%, de 31 a 60 dias 4% y de 61 a 90 dias 5% sobre el saldo, dichos porcentajes son acumulativos).\nVENCE ".strftime("%d de ".$meses[intval(Date::format($this->d["fecreg"]->sec, 'n'))-1]." del %Y",$row["cuenta_cobrar"]["operacion"]["concesion"]['fecven']->sec);
							}
						}
					}
					if(isset($row["cuenta_cobrar"]["operacion"]["colocacion"])){
						$tmp_cm_espa = "";
						$after .= "\nESPACIO - ".$helper->replace_acc($row["cuenta_cobrar"]["operacion"]["espacio"]["nomb"]);
						if(isset($row["cuenta_cobrar"]["operacion"]["espacio"]["ocupantes"])){
							$tmp_paralosrestos = true;
							foreach($row["cuenta_cobrar"]["operacion"]["espacio"]["ocupantes"] as $ocup){
								$after .= "\n  PARA LOS RESTOS DEL QUE FUE: ".$ocup["appat"]." ".$ocup["apmat"].", ".$ocup["nomb"];
							}
						}
						foreach ($row["cuenta_cobrar"]["operacion"]["colocacion"]['accesorios'] as $key => $value) {
							$after .= "\n  ".$value['nomb'].' - S/.'.$value['precio'];
						}
					}
					if(isset($row["cuenta_cobrar"]["operacion"]["inhumacion"])){
						if($tmp_paralosrestos==false){
							$tmp_paralosrestos = true;
							if(isset($row["cuenta_cobrar"]["operacion"]["ocupante"])){
								$after .= "\n  PARA LOS RESTOS DEL QUE FUE: ".$row["cuenta_cobrar"]["operacion"]["ocupante"]["appat"]." ".$row["cuenta_cobrar"]["operacion"]["ocupante"]["apmat"].", ".$row["cuenta_cobrar"]["operacion"]["ocupante"]["nomb"];
							}
						}
						if(isset($row["cuenta_cobrar"]["operacion"]["inhumacion"]["funeraria"])){
							$after .= "\n  FUNERARIA: ".$row["cuenta_cobrar"]["operacion"]["inhumacion"]["funeraria"]["nomb"];	
						}
						if(isset($row["cuenta_cobrar"]["operacion"]["programacion"])){
							$after .= "\n  PROGRAMACION PARA EL: ".Date::format($row["cuenta_cobrar"]["operacion"]["programacion"]["fecprog"]->sec,"d/m/Y H:i");
						}
						if(isset($row["cuenta_cobrar"]["operacion"]["inhumacion"]["puerta"])){
							$after .= "\n  INGRESO POR LA PUERTA ".strtoupper($row["cuenta_cobrar"]["operacion"]["inhumacion"]["puerta"]);
						}
						$tmp_block_asig = true;
					}
					if(isset($row["cuenta_cobrar"]["operacion"]["traslado"])){
						$after .= "\nDE: ".$helper->replace_acc($row["cuenta_cobrar"]["operacion"]["espacio"]["nomb"]);
						if(isset($row["cuenta_cobrar"]["operacion"]["traslado"]["espacio_destino"]))
							$after .= "\nA: ".$helper->replace_acc($row["cuenta_cobrar"]["operacion"]["traslado"]["espacio_destino"]["nomb"]);
						else
							$after .= "\nA: ".$helper->replace_acc($row["cuenta_cobrar"]["operacion"]["traslado"]["cementerio"]["nomb"]);
						if($tmp_paralosrestos==false){
							$tmp_paralosrestos = true;
							if(isset($row["cuenta_cobrar"]["operacion"]["ocupante"])){
								$after .= "\n  PARA LOS RESTOS DEL QUE FUE: ".$row["cuenta_cobrar"]["operacion"]["ocupante"]["appat"]." ".$row["cuenta_cobrar"]["operacion"]["ocupante"]["apmat"].", ".$row["cuenta_cobrar"]["operacion"]["ocupante"]["nomb"];
							}
						}
					}
					if(isset($row["cuenta_cobrar"]["operacion"]["asignacion"])){
						if(isset($row["cuenta_cobrar"]["operacion"]['tmp_adj'])){
							$tmp_obs = true;
							$tmp_ocup_print;
							foreach ($row['cuenta_cobrar']['operacion']['espacio']['ocupantes'] as $tmp_k_o=>$tmp_ocup_esp) {
								if($tmp_ocup_esp['_id']==$row['cuenta_cobrar']['operacion']['ocupante']['_id'])
									$tmp_ocup_print = $tmp_k_o-1;
							}
							$tmp_ocup_print = $row['cuenta_cobrar']['operacion']['espacio']['ocupantes'][$tmp_ocup_print];
							$after .= "\n\n\n  Se reduciran los restos de: ".$tmp_ocup_print['nomb'].' '.$tmp_ocup_print['appat'].' '.$tmp_ocup_print['apmat'].".\nPabellon ".$row["cuenta_cobrar"]["operacion"]["espacio"]["nomb"];
						}else{
							if($tmp_block_asig==false){
								$tmp_obs = true;
								if($tmp_paralosrestos==true){
									$after .= "\n  PARA LOS RESTOS DEL QUE FUE: ".$row["cuenta_cobrar"]["operacion"]["ocupante"]["nomb"].' '.$row["cuenta_cobrar"]["operacion"]["ocupante"]["appat"]." ".$row["cuenta_cobrar"]["operacion"]["ocupante"]["apmat"];
								}else{
									$after .= "\n  PARA CUANDO FALLEZCA: ".$row["cuenta_cobrar"]["operacion"]["ocupante"]["nomb"].' '.$row["cuenta_cobrar"]["operacion"]["ocupante"]["appat"]." ".$row["cuenta_cobrar"]["operacion"]["ocupante"]["apmat"];
								}
								//$after .= "\n  PARA LOS RESTOS DEL QUE FUE: ".$row["cuenta_cobrar"]["operacion"]["ocupante"]["nomb"].' '.$row["cuenta_cobrar"]["operacion"]["ocupante"]["appat"]." ".$row["cuenta_cobrar"]["operacion"]["ocupante"]["apmat"];
								//$after .= "\n  PARA LOS RESTOS DE: ".$row["cuenta_cobrar"]["operacion"]["ocupante"]["nomb"].' '.$row["cuenta_cobrar"]["operacion"]["ocupante"]["appat"]." ".$row["cuenta_cobrar"]["operacion"]["ocupante"]["apmat"];
								
									//$after .= "\n  FUNERARIA: SANTA MARIA";	
								//}
								//if(isset($row["cuenta_cobrar"]["operacion"]["programacion"])){
									//$after .= "\n  PROGRAMACION PARA EL: 04/11/2016 15:30";
								//}
								//if(isset($row["cuenta_cobrar"]["operacion"]["inhumacion"]["puerta"])){
									//$after .= "\n  INGRESO POR LA PUERTA PRINCIPAL";
									
								//}
								//$after .= "\n  FAMILIA: CONTRERAS CAMARGO ZONA NORMAL, LOTE 301";
							}
						}
					}
					$tmp_obs = false;	
					if(isset($row["cuenta_cobrar"]["operacion"]["conversion"])){
							$tmp_obs = true;
							if($tmp_paralosrestos==false){
								$tmp_paralosrestos = true;
								foreach($row["cuenta_cobrar"]["operacion"]["espacio"]["ocupantes"] as $ocup){
									$after .= "\n  PARA LOS RESTOS DE: ".$ocup["appat"]." ".$ocup["apmat"].", ".$ocup["nomb"];
								}
							}
							$tmp_cm_espa = "";
							$after .= "\n  Pabellon ".$row["cuenta_cobrar"]["operacion"]["espacio"]["nomb"];
							$tmp_conv_ope = $f->model('cm/oper')->params(array(
								'_id'=>$row["cuenta_cobrar"]["operacion"]["conversion"]['original']
							))->get('one')->items;
							$tmp_conv_cta = $f->model('cj/cuen')->params(array(
								'_id'=>$tmp_conv_ope['cuentas_cobrar']
							))->get('one')->items;
							$tmp_conv_com = $f->model('cj/comp')->params(array(
								'_id'=>$tmp_conv_cta['comprobantes'][0]
							))->get('one')->items;
							//$after .= "\n  VER: RC.".$tmp_conv_com['serie'].'-'.$tmp_conv_com['num'].' del '.strftime("%d de %B del %Y",$tmp_conv_com['fecreg']->sec).' por '.$monedas[$tmp_conv_com['moneda']]['simb'].number_format($tmp_conv_com["total"],2);
							$after .= "\n";
							$after .= "\n";
						}else{
							$tmp_obs = false;
						}
					}
				}
			}
			
			
			//$this->setXY(14,$y);$this->Cell(163,5,$row["cuenta_cobrar"]["servicio"]["nomb"].$after,0,0,'L');
			if(isset($row['cuenta_cobrar']['items'][0]['cuenta_cobrar']['servicio']['nomb'])){
				$row["cuenta_cobrar"]["servicio"]["nomb"] = $row['cuenta_cobrar']['items'][0]['cuenta_cobrar']['servicio']['nomb'];
			}
			
			$this->setXY(14,$y);$this->MultiCell(163,3,$helper->replace_acc($row["cuenta_cobrar"]["servicio"]["nomb"]).$after,'0','L');		
			//$alto_prev = ceil(strlen($row["cuenta_cobrar"]["servicio"]["nomb"].$after)/60);
			$y=$this->GetY();
			
			if(isset($row["conceptos"])){
				foreach($row["conceptos"] as $con){
					if(isset($con)){
						
						if(isset($con["monto"])){
							if(floatval($con["monto"])==0)continue;
						}else{
							$con["monto"] = $row['cuenta_cobrar']['total'];
						}
						if(isset($con["concepto"]["nomb"])){
							$this->setXY(24,$y);$this->Cell(153,3,$helper->replace_acc($con["concepto"]["nomb"]),0,0,'L');
						}else{
							$con["concepto"]["nomb"] = $row['cuenta_cobrar']['items'][0]['conceptos']['concepto']['nomb'];
							$this->setXY(24,$y);$this->Cell(153,3,$helper->replace_acc($con["concepto"]["nomb"]),0,0,'L');
						}
						
						$this->setXY(172,$y);$this->Cell(25,3,number_format($con["monto"],2),0,0,'R');
						
						
						
						if(isset($items['tickets'])){
							$y = $y+15;
							$this->SetFont('courier','U',10);
							$this->setXY(30,$y);$this->Cell(25,3,'Tickets Vendidos:',0,0,'L');
							$this->SetFont('courier','',10);
							$y = $y+5;
							foreach($items['tickets'] as $tickes){
								$ini = $tickes['ini'];
								$fin = $tickes['fin'];
								$numbers = range($ini, $fin);
								$range = implode(", ", $numbers);
								//$this->Cell(60, 10, $range, 1);
								$this->setXY(30,$y);$this->Cell(25,3,$tickes['ini'].' - '.$tickes['fin'],0,0,'L');
								
								$y = $y+5;
							}
							
						}
						/*if (is_object($row['cuenta_cobrar']['evento'])) {
							if(isset($row['cuenta_cobrar']['evento']->sec)){
								$y = $y + 10;
								$this->setXY(24, $y);
								$this->Cell(40, 5, 'PROGRAMADA PARA EL DIA ' . date('d-m-Y', $row['cuenta_cobrar']['evento']->sec) . ' A LAS ' . date('h:s', $row['cuenta_cobrar']['evento']->sec) . ' HORAS', 0, 0, 'L');
							}
						}else{
							$this->Cell(40, 5,'', 0, 0, 'L');
						}*/
						
						$y+=3;
					}
					
				}
			}
			
		}
		$this->SetFont('courier','',12);
		//echo strftime("%A %d de %B del %Y");
		if($items["autor"]["nomb"]=='RUHT SALUSTIANA'){
			$items["autor"]["nomb"] = "RUHT";
		}
        if($items["autor"]['_id']->{'$id'}=='53db90a0ee6f96940e00000d'){
        	$items["autor"]["nomb"] = "PERCY";
          $items["autor"]["appat"] = "AMESQUITA";
          $items["autor"]["apmat"] = "REVILLA";
        }
	 	//$this->setXY(0,127);$this->MultiCell(210,5,"Arequipa ".Date::format($this->d["fecreg"]->sec, 'd').' de '.$meses[intval(Date::format($this->d["fecreg"]->sec, 'n'))-1].' del '.Date::format($this->d["fecreg"]->sec, 'Y')." \n RECAUDADOR: YESENIA KARIN SANCHEZ QUISANA",'0','C');
		$this->setXY(0,127);$this->MultiCell(210,5,"Arequipa ".Date::format($this->d["fecreg"]->sec, 'd').' de '.$meses[intval(Date::format($this->d["fecreg"]->sec, 'n'))-1].' del '.Date::format($this->d["fecreg"]->sec, 'Y')." \n RECAUDADOR:".$items["autor"]["nomb"]." ".$items["autor"]["appat"]." ".$items["autor"]["apmat"],'0','C');
		//$this->setXY(0,127);$this->MultiCell(210,5,"Arequipa ".strftime("%d de %B del %Y",$items['fecreg']->sec),'0','C');
		//$this->setXY(66,127);$this->MultiCell(85,5,"Arequipa ".Date::format("d de F del Y",$items['fecreg']->sec)." \n RECAUDADOR:".$items["autor"]["nomb"]." ".$items["autor"]["appat"]." ".$items["autor"]["apmat"],'0','C');
		$decimal = round((($items["total"]-((int)$items["total"]))*100),0);
		if($decimal==0) $decimal = '0'.$decimal;
		$this->setXY(25,110);$this->Cell(163,5,Number::lit($items["total"]).' CON '.$decimal.'/100 '.$monedas[$items["moneda"]]["nomb"],0,0,'L');
		
		//$this->setXY(14,80);$this->MultiCell(163,5,$tmp_cm_espa,'0','J');
		$this->SetFont('courier','',9);
		$y+=4;
		//$tmp_cm_espa = "Santa Maria Goretti, 2, Piso 1, Fila 4, Numero 520";
		if($tmp_obs==true){
			//
			$this->setXY(14,$y);
			if(isset($items["observ"])){
				$this->MultiCell(168,3,$items["observ"],'0','J');
				$y=$this->GetY();
			}
		}else{
			if($tmp_cm_espa==''){
				$this->setXY(14,$y);$this->MultiCell(163,3,$items["observ"],'0','J');
				$y=$this->GetY();
			}else{
				$this->setXY(14,$y);
				$this->MultiCell(163,3,$tmp_cm_espa,'0','J');
				$y=$this->GetY();
				$this->setXY(14,$y);
				$this->MultiCell(168,3,$items["observ"],'0','J');
				$y=$this->GetY();
			}
		}
		if($observ_dif==true){
			$this->setXY(14,$y);
			$this->MultiCell(168,3,$observ_dif,'0','J');
			$y=$this->GetY();
		}
		if($tmp_pabe==true){
			$this->setXY(14,$y);
			$this->MultiCell(168,3,$tmp_pabe_glosa,'0','J');
		}
		$this->SetFont('courier','',12);
		$this->setXY(172,101);$this->Cell(25,5,number_format($items["total"],2),0,0,'R');
		//$this->Rect(15, $y, 20, 10);$this->setXY(15,$y);$this->MultiCell(20,5,"Tipo de Persona",'0','C');
	}
}
//$pdf=new doc('P','mm',array(210,148));
$pdf=new doc('P','mm','A4');
$pdf->SetMargins(15,39.5,0);
$pdf->SetTitle("Recibo de Caja");
$pdf->SetAutoPageBreak(false,0);
$pdf->Open();
$pdf->head($items);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
if($print==true){
	$pdf->AutoPrint(true);
}
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();
?>