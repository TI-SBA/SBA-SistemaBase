<?php
global $f;
$f->library('pdf');
//setlocale(LC_ALL,"esp");
setlocale(LC_ALL,"esp");
class doc extends FPDF{
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
//		$this->Image(IndexPath.DS.'templates/ts/Recibo.jpg',1,1,200,150);
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
		if($this->d["cliente"]["tipo_enti"]=="P"){
			$cliente = $this->d["cliente"]["appat"]." ".$this->d["cliente"]["apmat"].', '.$cliente;
		}
		$this->setXY(40,46);$this->Cell(100,5,$cliente,0,0,'L');
		$this->SetFont('courier','',10);
		/*if(isset($this->d["cliente"]["domic ilios"])){
			$this->setXY(30,51);$this->Cell(100,5,$this->d["cliente"]["domicilios"][0]["direccion"],0,0,'L');
		}*/
      	$this->setXY(40,53);$this->Cell(100,5,"HC".$this->d["paciente"]["his_cli"],0,0,'L');
		//$this->setXY(42,62);$this->Cell(100,5,$doc_tipo." - ".$doc_num,0,0,'L');
		//$this->setXY(147,62);$this->Cell(8,5,Date::format($this->d["fecreg"]->sec, 'd'),0,0,'L');
		//$this->setXY(160,62);$this->Cell(25,5,Date::format($this->d["fecreg"]->sec, 'm'),0,0,'L');
		//$this->setXY(190,62);$this->Cell(10,5,Date::format($this->d["fecreg"]->sec, 'Y'),0,0,'L');
		$this->SetFont('courier','B',10);
		//$this->setXY(140,40);$this->Cell(30,5,$this->d["serie"]." - ".$this->d["num"],0,0,'C');
	}
	function Publicar($items){
		$meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
		global $f;
		$f->library('helpers');
		$helper=new helper();
		$monedas = array(
			"S"=>array("nomb"=>"SOLES","simb"=>"S/."),
			"D"=>array("nomb"=>"DOLARES","simb"=>"US$")
		);
		$y = 78;
		$this->SetFont('courier','',12);
        $categoria = '';
		if(isset($items['hospitalizacion'])){
			$modalidad = array(
				'M'=>'mensual',
				'D'=>'diario'
			);
			$modalidad_tipo = array(
				'M'=>'mes(es)',
				'D'=>'dia(s)'
			);
			$tipo_hosp = array(
				'C'=>'completa',
				'P'=>'parcial'
			);
            $categorias = array(
				"10"=>"Nuevo",
				"11"=>"Continuador",
				"8"=>"D",
				"9"=>"Privado",
				"12"=>"Categoría B",
				"13"=>"Categoría C",
				"14"=>"Categoría A"
            );

			//$paciente = trim($this->d["cliente"]["nomb"]);
			$fecini = date('d/m/Y', $items['hospitalizacion']['fecini']->sec);
			$fecfin = date('d/m/Y', $items['hospitalizacion']['fecfin']->sec);
			/*if($this->d["cliente"]["tipo_enti"]=="P"){
				$paciente = trim($this->d["cliente"]["appat"])." ".trim($this->d["cliente"]["apmat"]).', '.$paciente;
			}*/
			$categoria = "Categoria: ".$categorias[$items['hospitalizacion']['categoria']];
            		//$categoria = 'Categoria A';
			$this->setXY(12,$y);$this->Cell(13,5,'1',0,0,'L');
			//$this->setXY(30,$y);$this->MultiCell(120,7,$helper->replace_acc($modalidad.' de Hospitalizacion Cat. '.$items['hospitalizacion']['categoria'].' para el paciente '.$paciente),'0','L');
			$this->setXY(30,$y);$this->MultiCell(120,7,$helper->replace_acc('Hosp. '.$tipo_hosp[$items['hospitalizacion']['tipo_hosp']].' x'.$items['hospitalizacion']['cant'].' '.$modalidad_tipo[$items['hospitalizacion']['modalidad']].' del '.$fecini.' al '.$fecfin),'0','L');
			$this->setXY(172,$y);$this->Cell(25,7,number_format($items["total"],2),0,0,'R');
		}else{
			foreach($items["items"] as $row){
				$after = "";
				$this->setXY(180,$y);$this->Cell(25,7,number_format($row["costo"],2),0,0,'R');
				if(!isset($row['tari'])){
                    $servicio = $helper->replace_acc($row["servicio"]["nomb"]);
                    if(strpos($servicio,'Categor')>-1) {
                        $categoria = substr($servicio, strpos($servicio,'Categor'), strlen($servicio));
                    	$servicio = substr($servicio, 0, strpos($servicio,'Categor'));
                    }
                    $this->setXY(20,$y);$this->MultiCell(13,7,"1",'0','L');
                    $this->setXY(160,$y);$this->MultiCell(20,7,number_format($row["costo"],2),'0','R');
                    $this->setXY(40,$y);$this->MultiCell(120,7,$servicio,'0','L');
				}else{
					$this->setXY(12,$y);$this->MultiCell(13,7,number_format($row["tari"]["cant"],0).$after,'0','L');
					$this->setXY(160,$y);$this->MultiCell(20,7,number_format($row["tari"]["precio_base"],2).$after,'0','R');
                    $this->setXY(25,$y);$this->MultiCell(120,7,$helper->replace_acc($row["tari"]["nomb"]).$after,'0','L');
                    //$this->setXY(170,$y);$this->MultiCell(25,5,number_format($row["tari"]["precio_base"],2).$after,'0','R');
				}
				$y=$this->GetY();
				/*foreach($row["conceptos"] as $con){
					if(floatval($con["monto"])==0)continue;
					$this->setXY(50,$y);$this->Cell(110,5,$helper->replace_acc($con["concepto"]["nomb"]),0,0,'L');
					$this->setXY(162,$y);$this->Cell(25,5,number_format($con["monto"],2),0,0,'R');
					$y+=5;
				}*/
			}
		}
		$this->SetFont('courier','',12);
        $this->setXY(150,46);$this->MultiCell(40,5,$categoria,'0','L');
		//$this->setXY(10,103);$this->MultiCell(100,5,strtoupper(strftime("    %d      %B           %y",$items['fecreg']->sec)),'0','L');

        $this->setXY(30,126);$this->MultiCell(10,5,strtoupper(strftime("%d",$items['fecreg']->sec)),'0','L');
        $this->setXY(40,126);$this->MultiCell(40,5,strtoupper($meses[intval(Date::format($items["fecreg"]->sec, 'n'))-1]),'0','L');
        $this->setXY(97,126);$this->MultiCell(16,5,strftime("%Y",$items['fecreg']->sec),'0','L');
        $this->SetFont('courier','',10);
		$this->setXY(100,135);$this->MultiCell(150,5,strtoupper($items["autor"]["nomb"]." ".$items["autor"]["appat"]." ".$items["autor"]["apmat"]),'0','L');
		$decimal = round((($items["total"]-((int)$items["total"]))*100),0);
		if($decimal==0) $decimal = '0'.$decimal;
		//$this->setXY(10,115);$this->Cell(120,5,strtoupper('SON '.Number::lit($items["total"]).' CON '.$decimal.'/100 '.$monedas[$items["moneda"]]["nomb"]),0,0,'L');
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
		$this->setXY(175,125);$this->Cell(25,5,number_format($items["total"],2),0,0,'R');
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
