<?php
global $f;
$f->library('pdf');
class hosp extends FPDF{
	function Header(){
		//$this->Image(IndexPath.DS.'templates/td/expd.gif',15,15,347,249);
		$this->SetFont('Arial','B',14);
		$this->SetXY(10,10);$this->MultiCell(357,5,"ESTADO HOSPITALIZACIÓN",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(357,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Hospitalizaciones",'0','C');
		$this->SetFont('arial','B',10);
		$this->SetXY(15,26);$this->MultiCell(360,10,"CAT.    N°H.C.               Documento                  Fecha                                               Apellidos y Nombres",'1','L');//28
		$this->SetXY(15,36);$this->MultiCell(110,20,"",'1','L');//55
		$this->SetXY(125,36);$this->Cell(125,10,'Hospitalización Completa',1,0,'C');
		$this->SetXY(250,36);$this->Cell(125,10,'Hospitalización Parcial',1,0,'C');
		$this->SetXY(125,46);$this->Cell(125,10,'           Del                 Al                        días/meses                          Fec.Alta',1,0,'L');
		$this->SetXY(250,46);$this->Cell(125,10,'           Del                 Al                        días/meses                          Fec.Alta',1,0,'L');
	}
	function Publicar($data){
		$categorias= array(
			"1"=>"S/E",
			"2"=>"PP",
			"3"=>"P",
			"4"=>"A",
			"5"=>"B",
			"6"=>"C",
			"7"=>"E",
			"10"=>"Nue.",
			"11"=>"Cont.",
			"8"=>"D"

		);
		$this->SetFont('arial','',10);
		$y_marg = 10;
		$y=56;
		$this->SetY($y);
		if(count($data)>0){
			foreach($data as $i=>$item){
				if($this->GetY()>255){
					$this->AddPage();
					$y=56;
				}
				$this->SetXY(15,$y);$this->MultiCell(10,5,$categorias[$item['categoria']],'0','C');
				$this->SetXY(25,$y);$this->MultiCell(17,5,$item['hist_cli'],'0','C');
				$this->SetXY(50,$y);$this->MultiCell(35,5,'R '.$item['recibo']['num'],'0','L');
				$this->SetXY(88,$y);$this->MultiCell(20,5,Date::format($item['fecpag']->sec, 'd/m/Y'),'0','C');
				$enti = $item['paciente']['nomb'];
				if(isset($item['paciente']['appat'])){
					$enti .= ' '.$item['paciente']['appat'].' '.$item['paciente']['apmat'];
				}
				if($item['tipo_hosp']=='C'){
					$this->SetXY(125,$y);$this->MultiCell(250,5,$enti,'0','L');
					$y += 5;
					$this->SetXY(125,$y);$this->MultiCell(20,5,Date::format($item['fecini']->sec, 'd/m/Y'),'0','L');
					$this->SetXY(150,$y);$this->MultiCell(20,5,Date::format($item['fecfin']->sec, 'd/m/Y'),'0','L');
					switch ($item['modalidad']) {
						case 'D': $mod = 'días'; break;
						case 'M': $mod = 'meses'; break;
					}
					$this->SetXY(185,$y);$this->MultiCell(30,5,$item['cant'].' '.$mod,'0','L');
					if(isset($item['fecalt'])){
						$this->SetXY(225,$y);$this->MultiCell(20,5,Date::format($item['fecalt']->sec, 'd/m/Y'),'0','L');
					}
				}elseif($item['tipo_hosp']=='P'){
					$this->SetXY(250,$y);$this->MultiCell(250,5,$enti,'0','L');
					$y += 5;
					$this->SetXY(250,$y);$this->MultiCell(20,5,Date::format($item['fecini']->sec, 'd/m/Y'),'0','L');
					$this->SetXY(275,$y);$this->MultiCell(20,5,Date::format($item['fecfin']->sec, 'd/m/Y'),'0','L');
					switch ($item['modalidad']) {
						case 'D': $mod = 'días'; break;
						case 'M': $mod = 'meses'; break;
					}
					$this->SetXY(300,$y);$this->MultiCell(30,5,$item['cant'].' '.$mod,'0','L');
					if(isset($item['fecalt'])){
						$this->SetXY(340,$y);$this->MultiCell(20,5,Date::format($item['fecalt']->sec, 'd/m/Y'),'0','L');
					}
				}
				$y = $this->getY();
				$this->Line(15, $y, 372, $y);
			}
		}
	}
	function Footer(){
    	$this->SetXY(220,-15);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,$this->PageNo(),0,0,'C');
    	$this->SetXY(29,-15);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,date("d/m/Y"),0,0,'L');
	} 
}
$pdf=new hosp('P','mm',array(387,279));
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($data);
$pdf->SetLeftMargin(25);
$pdf->Output();
?>