<?php
global $f;
$f->library('pdf');

class presaper extends FPDF
{
	var $organomb;
	var $periodo;
	var $estadonomb;
	function Filter($filtros){
		$this->organomb = $filtros["organomb"];
		$this->periodo = $filtros["periodo"];	
		$this->estadonomb = $filtros["estadonomb"];	
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/pr/poi_prog.gif',15,15,267,180);
		$this->SetFont('Arial','B',15);
		$this->setY(15);$this->Cell(0,10,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",0,0,'C');
		$this->SetFont('Arial','I',13);
		$this->setY(20);$this->Cell(0,10,"REPORTE/INFORME PROGRAMACION DEL PLAN OPERATIVO INSTITUCIONAL",0,0,'C');
		$this->SetFont('courier','',8);
		$this->setXY(15,37);$this->MultiCell(180,6,"Organización: ".$this->organomb,'1','L');
		$this->setXY(195,37);$this->MultiCell(43,6,"Periodo: ".$this->periodo,'1','L');
		$this->setXY(238,37);$this->MultiCell(43,6,"Estado: ".$this->estadonomb,'1','L');
		$this->SetFont('courier','B',8);
		$this->setXY(15,43);$this->MultiCell(54,12,"Actividades Programadas",'1','C');
		$this->setXY(69,43);$this->MultiCell(17,4,"Unidad de Medida",'1','C');
		$this->setXY(86,43);$this->MultiCell(184,6,"Cronograma y Metas Mensuales",'1','C');
		$this->setXY(86,49);$this->MultiCell(11,6,"ENE",'1','C');
		$this->setXY(97,49);$this->MultiCell(11,6,"FEB",'1','C');
		$this->setXY(108,49);$this->MultiCell(11,6,"MAR",'1','C');
		$this->SetFont('courier','B',7);
		$this->setXY(119,49);$this->MultiCell(12,6,"I TRIM",'1','C');
		$this->SetFont('courier','B',8);
		$this->setXY(131,49);$this->MultiCell(11,6,"ABR",'1','C');
		$this->setXY(142,49);$this->MultiCell(11,6,"MAY",'1','C');
		$this->setXY(153,49);$this->MultiCell(11,6,"JUN",'1','C');
		$this->SetFont('courier','B',7);
		$this->setXY(164,49);$this->MultiCell(13,6,"II TRIM",'1','C');
		$this->SetFont('courier','B',8);
		$this->setXY(177,49);$this->MultiCell(11,6,"JUL",'1','C');
		$this->setXY(188,49);$this->MultiCell(11,6,"AGO",'1','C');
		$this->setXY(199,49);$this->MultiCell(11,6,"SET",'1','C');
		$this->SetFont('courier','B',7);
		$this->setXY(210,49);$this->MultiCell(14,6,"III TRIM",'1','C');
		$this->SetFont('courier','B',8);
		$this->setXY(224,49);$this->MultiCell(11,6,"OCT",'1','C');
		$this->setXY(235,49);$this->MultiCell(11,6,"NOV",'1','C');
		$this->setXY(246,49);$this->MultiCell(11,6,"DIC",'1','C');
		$this->SetFont('courier','B',7);
		$this->setXY(257,49);$this->MultiCell(13,6,"IV TRIM",'1','C');
		$this->SetFont('courier','B',8);
		$this->setXY(270,43);$this->MultiCell(11,12,"TOTAL",'1','C');
	}		
	function Publicar($items){
		$this->SetFont('courier','',8);
		$x=0;
		$y=37;//41
		$y_marg = 5;
		$this->SetFont('courier','',8);
		foreach($items as $row){
			if ($y>=180){
				$this->AddPage();$y=37;
			}
			$y = $y + 18;
			$this->Rect(15, $y, 54, 18);$this->SetXY(15,$y);$this->MultiCell(54,4.5,$row["actividad"],'0','L');
			$this->Rect(69, $y, 17, 18);$this->SetXY(69,$y);$this->MultiCell(17,6,$row["unidad"]["nomb"],'0','C');
			$this->Rect(86, $y, 11, 18);$this->SetXY(86,$y);$this->MultiCell(11,18,$row["metas"]["programadas"][0],'0','R');
			$this->Rect(97, $y, 11, 18);$this->SetXY(97,$y);$this->MultiCell(11,18,$row["metas"]["programadas"][1],'0','R');
			$this->Rect(108, $y, 11, 18);$this->SetXY(108,$y);$this->MultiCell(11,18,$row["metas"]["programadas"][2],'0','R');
			$this->Rect(119, $y, 12, 18);$this->SetXY(119,$y);$this->MultiCell(12,18,$row["metas"]["programadas"][0] + $row["metas"]["programadas"][1] + $row["metas"]["programadas"][2],'0','R');
			$this->Rect(131, $y, 11, 18);$this->SetXY(131,$y);$this->MultiCell(11,18,$row["metas"]["programadas"][3],'0','R');
			$this->Rect(142, $y, 11, 18);$this->SetXY(142,$y);$this->MultiCell(11,18,$row["metas"]["programadas"][4],'0','R');
			$this->Rect(153, $y, 11, 18);$this->SetXY(153,$y);$this->MultiCell(11,18,$row["metas"]["programadas"][5],'0','R');
			$this->Rect(164, $y, 13, 18);$this->SetXY(164,$y);$this->MultiCell(13,18,$row["metas"]["programadas"][3] + $row["metas"]["programadas"][4] + $row["metas"]["programadas"][5],'0','R');
			$this->Rect(177, $y, 11, 18);$this->SetXY(177,$y);$this->MultiCell(11,18,$row["metas"]["programadas"][6],'0','R');
			$this->Rect(188, $y, 11, 18);$this->SetXY(188,$y);$this->MultiCell(11,18,$row["metas"]["programadas"][7],'0','R');
			$this->Rect(199, $y, 11, 18);$this->SetXY(199,$y);$this->MultiCell(11,18,$row["metas"]["programadas"][8],'0','R');
			$this->Rect(210, $y, 14, 18);$this->SetXY(210,$y);$this->MultiCell(14,18,$row["metas"]["programadas"][6] + $row["metas"]["programadas"][7] + $row["metas"]["programadas"][8],'0','R');
			$this->Rect(224, $y, 11, 18);$this->SetXY(224,$y);$this->MultiCell(11,18,$row["metas"]["programadas"][9],'0','R');
			$this->Rect(235, $y, 11, 18);$this->SetXY(235,$y);$this->MultiCell(11,18,$row["metas"]["programadas"][10],'0','R');
			$this->Rect(246, $y, 11, 18);$this->SetXY(246,$y);$this->MultiCell(11,18,$row["metas"]["programadas"][11],'0','R');
			$this->Rect(257, $y, 13, 18);$this->SetXY(257,$y);$this->MultiCell(13,18,$row["metas"]["programadas"][9] + $row["metas"]["programadas"][10] + $row["metas"]["programadas"][11],'0','R');
			$this->Rect(270, $y, 11, 18);$this->SetXY(270,$y);$this->MultiCell(11,18,array_sum($row["metas"]["programadas"]),'0','R');
		}
	}
	function Footer()
	{
    	//Footer de la pagina
    	$this->SetXY(220,-21.5);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,"Página: ".$this->PageNo(),0,0,'C');
    	
    	$this->SetXY(29,-21.5);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,"Fecha de Impresión: ".date("d-m-Y"),0,0,'L');
	} 
	 
}

$pdf=new presaper('L','mm','A4');
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filter($filtros);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>