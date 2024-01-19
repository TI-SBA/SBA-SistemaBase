<?php
global $f;
$tmp_obs = false;
$tmp_paralosrestos = false;
$tmp_cm_espa = '';
$f->library('pdf');
//setlocale(LC_ALL,"esp");
setlocale(LC_ALL,"esp");
class doc extends FPDF
{
	var $javascript;
	var $n_js;
	function IncludeJS($script) {
		$this->javascript=$script;
	}
	function _putjavascript() {
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
	function _putresources() {
		parent::_putresources();
		if (!empty($this->javascript)) {
			$this->_putjavascript();
		}
	}
	function _putcatalog() {
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
					elseif($dident["tipo"]=="RUC"){
						$doc_tipo = "RUC";
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
		if($this->d["cliente"]["tipo_enti"]=="P"){
			$cliente = $this->d["cliente"]["appat"]." ".$this->d["cliente"]["apmat"].', '.$cliente;
		}
		//$this->setXY(30,44);$this->Cell(100,5,$cliente,0,0,'L');
		$this->setXY(30,39);$this->MultiCell(140,4,$cliente,'0','L');
		//$this->setXY(30,44);$this->MultiCell(140,4,$cliente,'0','L');
		$this->SetFont('courier','',10);
		$direccion = '';
		if(isset($this->d["cliente"]["domicilios"])){
			if(count($this->d["cliente"]["domicilios"])>0){
				foreach($this->d["cliente"]["domicilios"] as $dire){
					if(isset($dire['tipo'])){
						if($dire['tipo']=='PERSONAL'){
							$direccion = $dire['direccion'];
						}
					}else{
						$direccion = $dire['direccion'];
					}
				}
			}
		}
		//print_r($this->d);die();
		//$this->setXY(30,51);$this->Cell(100,5,$this->d["cliente"]["domicilios"][0]["direccion"],0,0,'L');
		//$this->setXY(30,51);$this->MultiCell(100,5,$this->d["cliente"]["domicilios"][0]["direccion"],'0','L');
		if(isset($this->d["cliente"]["domicilios"])){
			$this->setXY(30,46);$this->MultiCell(150,5,$this->d["cliente"]["domicilios"][0]["direccion"],'0','L');
		}
		$this->setXY(130,39);$this->Cell(100,5,$doc_tipo." - ".$doc_num,0,0,'L');
		//$this->setXY(147,62);$this->Cell(8,5,Date::format($this->d["fecreg"]->sec, 'd'),0,0,'L');
		//$this->setXY(160,62);$this->Cell(25,5,Date::format($this->d["fecreg"]->sec, 'm'),0,0,'L');
		//$this->setXY(190,62);$this->Cell(10,5,Date::format($this->d["fecreg"]->sec, 'Y'),0,0,'L');
		$this->SetFont('courier','B',10);
		//$this->setXY(140,40);$this->Cell(30,5,$this->d["serie"]." - ".$this->d["num"],0,0,'C');
	}
	function Publicar($items){
		global $f;
		$f->library('helpers');
		$helper=new helper();
		$monedas = array(
			"S"=>array("nomb"=>"SOLES","simb"=>"S/."),
			"D"=>array("nomb"=>"DOLARES","simb"=>"US$")
		);
		$y=70;
		//$y = 68;
		$this->SetFont('courier','',12);
		$tmp_block_asig = false;
		$tmp_pabe = false;
		$tmp_pabe_glosa = '';
		if($items['observ']!=''){
			//print_r($items);die();
			if(isset($items["servicio"])){
				//$this->setXY(50,$y);$this->MultiCell(110,3,$helper->replace_acc($items["servicio"]["nomb"]),'0','L');
				$this->setXY(45,$y);$this->MultiCell(110,3,$helper->replace_acc($items["servicio"]["nomb"]),'0','L');
				$y = $y + 5;
				$this->setXY(30,$y);$this->MultiCell(110,3,$helper->replace_acc($items['observ']),'0','L');
				$y_observ = $this->GetY();
				$this->SetFont('courier','U',12);
				if(isset($items['inmueble'])){
					$this->setXY(50,$y_observ+2);$this->MultiCell(110,3,trim($items['observ']."\n".$items['inmueble']['tipo']['nomb']." ".$items['inmueble']['direccion']),'0','L');
				}
			}else{
				$this->setXY(30,$y);$this->MultiCell(110,3,$helper->replace_acc($items['observ']),'0','L');
			}
			$this->SetFont('courier','',12);
			$this->setXY(162,$y);$this->Cell(25,5,number_format($items["total"],2),0,0,'R');
		}else{
			if(isset($items['items'])){
				foreach($items['items'] as $item){
					//foreach($item["cuenta_cobrar"] as $row){
						$after = "";
						//$this->setXY(162,$y);$this->Cell(25,5,number_format($items["total"],2),0,0,'R');
						$this->setXY(162,$y);$this->Cell(25,5,number_format($items["total"],2),0,0,'R');
						$this->setXY(50,$y);$this->MultiCell(110,5,$helper->replace_acc($item["cuenta_cobrar"]["servicio"]["nomb"]).$after,'0','L');
						//$this->setXY(45,$y);$this->MultiCell(110,5,$helper->replace_acc($item["cuenta_cobrar"]["servicio"]["nomb"]).$after,'0','L');
						$y=$this->GetY();
						/*foreach($row["conceptos"] as $con){
							if(floatval($con["monto"])==0)continue;
							$this->setXY(50,$y);$this->Cell(110,5,$helper->replace_acc($con["concepto"]["nomb"]),0,0,'L');
							$this->setXY(162,$y);$this->Cell(25,5,$monedas[$items["moneda"]]["simb"].number_format($con["monto"],2),0,0,'R');
							$y+=5;
						}*/
					//}
				}
			}
		if(isset($items['inmueble'])){
			$this->setXY(50,$y);$this->MultiCell(110,3,"(".$items['inmueble']['tipo']['nomb'].") ".$items['inmueble']['direccion'],'0','L');
            }
		}
		$meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
		$this->SetFont('courier','',11);
		//$this->setXY(12,112);$this->MultiCell(200,5,strtoupper(strftime("    %d      %B           %y",$items['fecreg']->sec)." \n\n\n\n ".$items["autor"]["nomb"]." ".$items["autor"]["appat"]." ".$items["autor"]["apmat"]),'0','C');
		//$this->setXY(12,112);$this->MultiCell(200,5,strtoupper(strftime("    %d      %B           %y",$items['fecreg']->sec)." \n\n\n\n "),'0','C');
		//$this->setXY(12,112);$this->MultiCell(200,5,'    '.Date::format($this->d["fecreg"]->sec, 'd').'      '.$meses[intval(Date::format($this->d["fecreg"]->sec, 'n'))-1].'           '.Date::format($this->d["fecreg"]->sec, 'y'),0,'C');
		$this->setXY(12,116);
		$this->MultiCell(185,5,'    '.Date::format($this->d["fecreg"]->sec, 'd').'      '.$meses[intval(Date::format($this->d["fecreg"]->sec, 'n'))-1].'      '.Date::format($this->d["fecreg"]->sec, 'y'),0,'C');
		//$this->setXY(25,103);$this->Cell(120,5,strtoupper(Number::lit($items["total"]).' CON '.round((($items["total"]-((int)$items["total"]))*100),0).'/100 '.$monedas[$items["moneda"]]["nomb"]),0,0,'L');
		$this->setXY(20,106);$this->Cell(120,5,strtoupper(Number::lit($items["total"]).' CON '.round((($items["total"]-((int)$items["total"]))*100),0).'/100 '.$monedas[$items["moneda"]]["nomb"]),0,0,'L');
		$this->SetFont('courier','',9);
		$y+=4;
		/*if($tmp_obs==true){
			$this->setXY(14,$y);
			$this->MultiCell(168,3,$items["observ"],'0','J');
			$y+=6;
		}else{
			if($tmp_cm_espa==''){
				$this->setXY(14,$y);$this->MultiCell(163,3,$items["observ"],'0','J');
				$y+=6;
			}else{
				$this->setXY(14,$y);
				$this->MultiCell(163,3,$tmp_cm_espa,'0','J');
				$y+=6;
				$this->setXY(14,$y);
				$this->MultiCell(168,3,$items["observ"],'0','J');
				$y+=6;
			}
		}
		if($observ_dif==true){
			$this->setXY(14,$y);
			$this->MultiCell(168,3,$observ_dif,'0','J');
		}
		if($tmp_pabe==true){
			$this->setXY(14,$y);
			$this->MultiCell(168,3,$tmp_pabe_glosa,'0','J');
		}*/
		$this->SetFont('courier','',12);
		//$this->setXY(162,100);$this->Cell(25,5,'S/ '.number_format($items["total"],2),0,0,'R');
		//$this->setXY(162,106);$this->Cell(25,5,'S/ '.number_format($items["total"],2),0,0,'R');
		$this->setXY(162,106);
		//print_r($items);
		//die();
		if($items["moneda"]=="D")
			$this->Cell(28,5,'US$ '.number_format($items["total"],2),0,0,'R');
		else
			$this->Cell(28,5,'S/ '.number_format($items["total"],2),0,0,'R');
	}
	function Footer()
	{

	}
	 
}
//$pdf=new doc('P','mm',array(210,148));
$pdf=new doc('L','mm','A5');
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