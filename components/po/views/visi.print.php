<?php
global $f;
$f->library('pdf');
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
	function Publicar($cheque){
		global $f;
		$this->SetFont('Helvetica', 'B', 11);
		//$this->setXY(72,1);$this->Cell(25,5,date('d  m  Y',$cheque['fec']->sec),0,0,'L');
		$this->setXY(60,1);$this->Cell(20,5,date('d  m  Y',$cheque['fec']->sec),0,0,'L');
		//$this->setXY(114,2);$this->Cell(25,5,'x.x.x.'.number_format($cheque['monto'],2),0,0,'L');
		$this->setXY(80,2);$this->Cell(20,5,'      '.number_format($cheque['monto'],2),0,0,'L');
		if(isset($cheque['entidad'])){
			$nomb = $cheque['entidad']['nomb'];
			if($cheque['entidad']['tipo_enti']=='P')
				$nomb .= ' '.$cheque['entidad']['appat'].' '.$cheque['entidad']['apmat'];
		}else{
			$nomb = $cheque['descr'];
		}
		$nomb = strtoupper($nomb);
		//while(strlen($nomb)<90){
		while(strlen($nomb)<50){
			$nomb .= ' . x';
		}
		//$this->setXY(18,21);$this->Cell(25,5,utf8_decode($nomb),0,0,'L');
		$this->setXY(18,19);$this->Cell(25,5,utf8_decode($nomb),0,0,'L');
		$tmp_num = round((($cheque['monto']-((int)$cheque['monto']))*100),0);
		if($tmp_num!=0&&$tmp_num<10) $tmp_num .= '0'.$tmp_num;
		if($tmp_num==0) $tmp_num = '00';
		$num = Number::lit($cheque['monto']).' Y '.$tmp_num.'/100 NUEVOS SOLES';
		//while(strlen($num)<80){
		while(strlen($num)<60){
			$num .= ' . x';
		}
		//$this->setXY(12,31);$this->Cell(25,5,$num,0,0,'L');
		$this->setXY(12,29);$this->Cell(20,5,$num,0,0,'L');
	}
}
$pdf=new doc('P','mm','A4');
$pdf->AddPage();
$pdf->Publicar($cheque);
$pdf->AutoPrint(true);
$pdf->Output();
?>