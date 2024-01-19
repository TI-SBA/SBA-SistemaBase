<?php
global $f;
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
	function Publicar($recibo){
		global $f;
		$f->library('helpers');
		$helper=new helper();
		$this->SetFont('courier','',12);
		$this->setXY(110,6);$this->Cell(25,5,number_format($recibo["monto"],2),0,0,'R');
		$this->SetFont('courier','',10);
		$this->setXY(36,28);$this->MultiCell(110,4,Number::lit($recibo["monto"]).' Y '.round((($recibo["monto"]-((int)$recibo["monto"]))*100),0).'/100 NUEVOS SOLES',0,'L');
		$this->setXY(12,36);$this->MultiCell(125,5, "              ".strtoupper($recibo["concepto"]),'0','L');
		$this->SetFont('courier','',12);
		$this->setXY(22,78);$this->Cell(163,5,strtoupper($recibo["descr"]),0,0,'L');
		$this->setXY(110,97);$this->Cell(70,5,$recibo["dni"],0,0,'L');
		$this->setXY(69,66);$this->Cell(70,5,date('d',$recibo["fec"]->sec),0,0,'L');
		$this->setXY(87,66);$this->Cell(70,5,strftime("%B",$recibo['fec']->sec),0,0,'L');
		$this->setXY(129,66);$this->Cell(70,5,date('y',$recibo["fec"]->sec),0,0,'L');
	}
	 
}
//$pdf=new doc('P','mm',array(210,148));
$pdf=new doc('P','mm','A4');
$pdf->SetTitle("Recibo de Caja");
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($recibo);
$pdf->AutoPrint(true);
$pdf->Output();
?>