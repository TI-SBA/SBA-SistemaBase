<?php
global $f;
$f->library('pdf');

class presaperrepo extends FPDF
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
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(277,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(5,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Planificacion y Presupuestos",'0','C');	
		$this->SetFont('Arial','B',14);
		$this->setY(10);$this->Cell(0,10,"PROGRAMACIÓN MENSUAL DE ACTIVIDADES DEL PLAN OPERATIVO",0,0,'C');
		$this->SetFont('Arial','',10);
		$this->SetXY(10,20);$this->MultiCell(277,5,"AÑO FISCAL ".$this->periodo,'0','C');
		$this->SetFont('courier','B',8);
		$this->setXY(5,37);$this->MultiCell(10,12,"Nº",'1','C');
		$this->setXY(15,37);$this->MultiCell(54,12,"Actividades Programadas",'1','C');
		$this->setXY(69,37);$this->MultiCell(17,4,"Unidad de Medida",'1','C');
		$this->setXY(86,37);$this->MultiCell(184,6,"Cronograma y Metas Mensuales",'1','C');
		$this->setXY(86,43);$this->MultiCell(11,6,"ENE",'1','C');
		$this->setXY(97,43);$this->MultiCell(11,6,"FEB",'1','C');
		$this->setXY(108,43);$this->MultiCell(11,6,"MAR",'1','C');
		$this->SetFont('courier','B',7);
		$this->setXY(119,43);$this->MultiCell(12,6,"I TRIM",'1','C');
		$this->SetFont('courier','B',8);
		$this->setXY(131,43);$this->MultiCell(11,6,"ABR",'1','C');
		$this->setXY(142,43);$this->MultiCell(11,6,"MAY",'1','C');
		$this->setXY(153,43);$this->MultiCell(11,6,"JUN",'1','C');
		$this->SetFont('courier','B',7);
		$this->setXY(164,43);$this->MultiCell(13,6,"II TRIM",'1','C');
		$this->SetFont('courier','B',8);
		$this->setXY(177,43);$this->MultiCell(11,6,"JUL",'1','C');
		$this->setXY(188,43);$this->MultiCell(11,6,"AGO",'1','C');
		$this->setXY(199,43);$this->MultiCell(11,6,"SET",'1','C');
		$this->SetFont('courier','B',7);
		$this->setXY(210,43);$this->MultiCell(14,6,"III TRIM",'1','C');
		$this->SetFont('courier','B',8);
		$this->setXY(224,43);$this->MultiCell(11,6,"OCT",'1','C');
		$this->setXY(235,43);$this->MultiCell(11,6,"NOV",'1','C');
		$this->setXY(246,43);$this->MultiCell(11,6,"DIC",'1','C');
		$this->SetFont('courier','B',7);
		$this->setXY(257,43);$this->MultiCell(13,6,"IV TRIM",'1','C');
		$this->SetFont('courier','B',8);
		$this->setXY(270,37);$this->MultiCell(18,4,"TOTAL META ANUAL",'1','C');
	}		
	function Publicar($items){
		$this->SetFont('courier','',8);
		$x=0;
		$y=49;//41
		$y_marg = 5;
		$this->SetFont('courier','',8);
		$i_org = 1;
		foreach($items as $row){		
			//$y = $y + 18;
			$this->SetFont('arial','B',8);
			$this->Rect(5, $y, 10, 6);$this->SetXY(5,$y);$this->MultiCell(10,6,$i_org,'0','L');
			$this->Rect(15, $y, 273, 6);$this->SetXY(15,$y);$this->MultiCell(273,6,$row["cod"]." ".strtoupper($row["nomb"]).": ".$row["objetivo"],'0','L');
			$this->SetFont('courier','',8);
			$y = $y + 6;
			$i_acti = 1;
			foreach($row["items"] as $row1){
				if ($y>=180){
					$this->AddPage();$y=49;
					$this->SetFont('arial','B',8);
					$this->Rect(5, $y, 10, 6);$this->SetXY(5,$y);$this->MultiCell(10,6,$i_org,'0','L');
					$this->Rect(15, $y, 273, 6);$this->SetXY(15,$y);$this->MultiCell(273,6,strtoupper($row["nomb"]),'0','L');
					$y = $y + 6;
				}
				$str = strlen($row1["actividad"]);
				$str = ceil($str/30);
				$alto = $str*6;
				$this->SetFont('arial','',8);
				$this->Rect(5, $y, 10, $alto);$this->SetXY(5,$y);$this->MultiCell(10,6,$i_org.".".$i_acti,'0','L');
				$this->Rect(15, $y, 54, $alto);$this->SetXY(15,$y);$this->MultiCell(54,6,$row1["actividad"],'0','L');
				$this->SetFont('arial','',7);
				$this->Rect(69, $y, 17, $alto);$this->SetXY(69,$y);$this->MultiCell(17,6,$row1["unidad"]["nomb"],'0','C');
				$this->SetFont('arial','',8);
				$this->Rect(86, $y, 11, $alto);$this->SetXY(86,$y);$this->MultiCell(11,$alto,$row1["metas"]["programadas"][0],'0','C');
				$this->Rect(97, $y, 11, $alto);$this->SetXY(97,$y);$this->MultiCell(11,$alto,$row1["metas"]["programadas"][1],'0','C');
				$this->Rect(108, $y, 11, $alto);$this->SetXY(108,$y);$this->MultiCell(11,$alto,$row1["metas"]["programadas"][2],'0','C');
				$this->Rect(119, $y, 12, $alto);$this->SetXY(119,$y);$this->MultiCell(12,$alto,$row1["metas"]["programadas"][0] + $row1["metas"]["programadas"][1] + $row1["metas"]["programadas"][2],'0','C');
				$this->Rect(131, $y, 11, $alto);$this->SetXY(131,$y);$this->MultiCell(11,$alto,$row1["metas"]["programadas"][3],'0','C');
				$this->Rect(142, $y, 11, $alto);$this->SetXY(142,$y);$this->MultiCell(11,$alto,$row1["metas"]["programadas"][4],'0','C');
				$this->Rect(153, $y, 11, $alto);$this->SetXY(153,$y);$this->MultiCell(11,$alto,$row1["metas"]["programadas"][5],'0','C');
				$this->Rect(164, $y, 13, $alto);$this->SetXY(164,$y);$this->MultiCell(13,$alto,$row1["metas"]["programadas"][3] + $row1["metas"]["programadas"][4] + $row1["metas"]["programadas"][5],'0','C');
				$this->Rect(177, $y, 11, $alto);$this->SetXY(177,$y);$this->MultiCell(11,$alto,$row1["metas"]["programadas"][6],'0','C');
				$this->Rect(188, $y, 11, $alto);$this->SetXY(188,$y);$this->MultiCell(11,$alto,$row1["metas"]["programadas"][7],'0','C');
				$this->Rect(199, $y, 11, $alto);$this->SetXY(199,$y);$this->MultiCell(11,$alto,$row1["metas"]["programadas"][8],'0','C');
				$this->Rect(210, $y, 14, $alto);$this->SetXY(210,$y);$this->MultiCell(14,$alto,$row1["metas"]["programadas"][6] + $row1["metas"]["programadas"][7] + $row1["metas"]["programadas"][8],'0','C');
				$this->Rect(224, $y, 11, $alto);$this->SetXY(224,$y);$this->MultiCell(11,$alto,$row1["metas"]["programadas"][9],'0','C');
				$this->Rect(235, $y, 11, $alto);$this->SetXY(235,$y);$this->MultiCell(11,$alto,$row1["metas"]["programadas"][10],'0','C');
				$this->Rect(246, $y, 11, $alto);$this->SetXY(246,$y);$this->MultiCell(11,$alto,$row1["metas"]["programadas"][11],'0','C');
				$this->Rect(257, $y, 13, $alto);$this->SetXY(257,$y);$this->MultiCell(13,$alto,$row1["metas"]["programadas"][9] + $row1["metas"]["programadas"][10] + $row1["metas"]["programadas"][11],'0','C');
				$this->Rect(270, $y, 18, $alto);$this->SetXY(270,$y);$this->MultiCell(18,$alto,array_sum($row1["metas"]["programadas"]),'0','C');
				$y = $this->GetY();
				$i_acti++;
			}	
			$i_org++;		
		}
	}
	function Footer()
	{
    	
	} 
	 
}

$pdf=new presaperrepo('L','mm','A4');
$pdf->AliasNbPages();
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