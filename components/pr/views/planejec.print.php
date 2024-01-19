<?php
global $f;
$f->library('pdf');

class presaper extends FPDF
{
	var $organomb;
	var $periodo;
	var $trimestre;
	function Filter($filtros){
		$this->organomb = $filtros["organomb"];
		$this->periodo = $filtros["periodo"];	
		$this->trimestre = $filtros["trimestre"];	
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/pr/poi_ejec.gif',15,15,267,180);
		$trimestres = array("1"=>"I","2"=>"II","3"=>"III","4"=>"IV");
		$this->SetFont('Arial','B',15);
		$this->setY(15);$this->Cell(0,10,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",0,0,'C');
		$this->SetFont('Arial','I',13);
		$this->setY(20);$this->Cell(0,10,"REPORTE/INFORME EJECUCION DEL PLAN OPERATIVO INSTITUCIONAL",0,0,'C');
		$this->SetFont('courier','',8);
		$this->setXY(15,35);$this->MultiCell(172,7,"Organización: ".$this->organomb,'1','L');
		$this->setXY(187,35);$this->MultiCell(50,7,"Periodo:".$this->periodo,'1','L');
		$this->setXY(237,35);$this->MultiCell(45,7,"Trimestre: ".$trimestres[$this->trimestre],'1','L');
		$this->SetFont('courier','B',8);
		$this->setXY(15,42);$this->MultiCell(103,14,"Actividades Programadas",'1','C');
		$this->setXY(118,42);$this->MultiCell(31,14,"Unidad de Medida",'1','C');
		$this->setXY(149,42);$this->MultiCell(19,7,"Meta Anual Programada",'1','C');
		$this->setXY(168,42);$this->MultiCell(19,4.7,"Meta Programada Trimestral",'1','C');
		$this->setXY(187,42);$this->MultiCell(56,7,"Ejecución de Metas Mensuales",'1','C');
		if($this->trimestre=="1"){
			$this->setXY(187,49);$this->MultiCell(14,7,"Ene",'1','C');
			$this->setXY(201,49);$this->MultiCell(14,7,"Feb",'1','C');
			$this->setXY(215,49);$this->MultiCell(14,7,"Mar",'1','C');
			$this->setXY(229,49);$this->MultiCell(14,7,"I TRIM",'1','C');
		}elseif($this->trimestre=="2"){
			$this->setXY(187,49);$this->MultiCell(14,7,"Abr",'1','C');
			$this->setXY(201,49);$this->MultiCell(14,7,"May",'1','C');
			$this->setXY(215,49);$this->MultiCell(14,7,"Jun",'1','C');
			$this->setXY(229,49);$this->MultiCell(14,7,"II TRIM",'1','C');
		}elseif($this->trimestre=="3"){
			$this->setXY(187,49);$this->MultiCell(14,7,"Jul",'1','C');
			$this->setXY(201,49);$this->MultiCell(14,7,"Ago",'1','C');
			$this->setXY(215,49);$this->MultiCell(14,7,"Set",'1','C');
			$this->SetFont('courier','B',7);
			$this->setXY(229,49);$this->MultiCell(14,7,"III TRIM",'1','C');
		}elseif($this->trimestre=="4"){
			$this->setXY(187,49);$this->MultiCell(14,7,"Oct",'1','C');
			$this->setXY(201,49);$this->MultiCell(14,7,"Nov",'1','C');
			$this->setXY(215,49);$this->MultiCell(14,7,"Dic",'1','C');
			$this->SetFont('courier','B',7);
			$this->setXY(229,49);$this->MultiCell(14,7,"IV TRIM",'1','C');
		}
		$this->SetFont('courier','B',8);
		$this->setXY(243,42);$this->MultiCell(19,7,"Porcentaje Trimestral",'1','C');
		$this->setXY(262,42);$this->MultiCell(20,7,"Avance Anual %",'1','C');
	}
		
	function Publicar($items,$trim_data){
		$trimestres = array("1"=>"I","2"=>"II","3"=>"III","4"=>"IV");
		$this->SetFont('courier','',8);
		$x=0;
		$y=46;//41
		$y_marg = 5;
		$this->SetY($y);
		$this->SetFont('courier','',8);
		foreach($items as $row){
			if ($y>=180){
				$this->AddPage();$y=46;
			}
			$y = $y + 10;
			$this->ln();
			$this->SetFont('courier','',8);
			$this->Rect(15, $y, 103, 10);$this->SetXY(15,$y);$this->MultiCell(103,5,$row["actividad"],'0','L');
			$this->Rect(118, $y, 31, 10);$this->SetXY(118,$y);$this->MultiCell(31,5,$row["unidad"]["nomb"],'0','C');
			$this->Rect(149, $y, 19, 10);$this->SetXY(149,$y);$this->MultiCell(19,10,array_sum($row["metas"]["programadas"]),'0','R');
			if($trim_data=="1"){
				$sum_prog = $row["metas"]["programadas"][0] + $row["metas"]["programadas"][1] + $row["metas"]["programadas"][2];
				$sum_ejec = $row["metas"]["ejecutadas"][0] + $row["metas"]["ejecutadas"][1] + $row["metas"]["ejecutadas"][2];
				$this->Rect(168, $y, 19, 10);$this->SetXY(168,$y);$this->MultiCell(19,10,$sum_prog,'0','R');
				$this->Rect(187, $y, 14, 10);$this->SetXY(187,$y);$this->MultiCell(14,10,$row["metas"]["ejecutadas"][0],'0','R');
				$this->Rect(201, $y, 14, 10);$this->SetXY(201,$y);$this->MultiCell(14,10,$row["metas"]["ejecutadas"][1],'0','R');
				$this->Rect(215, $y, 14, 10);$this->SetXY(215,$y);$this->MultiCell(14,10,$row["metas"]["ejecutadas"][2],'0','R');
				$this->Rect(229, $y, 14, 10);$this->SetXY(229,$y);$this->MultiCell(14,10,$sum_ejec,'0','R');
				if($sum_prog!="0"){
					$this->Rect(243, $y, 19, 10);$this->SetXY(243,$y);$this->MultiCell(19,10,round($sum_ejec*100/$sum_prog,2)."%",'0','R');
					$this->Rect(262, $y, 20, 10);$this->SetXY(262,$y);$this->MultiCell(20,10,round($sum_ejec*100/array_sum($row["metas"]["programadas"]),2)."%",'0','R');
				}else{
					$this->Rect(243, $y, 19, 10);$this->SetXY(243,$y);$this->MultiCell(19,10,"--",'0','C');
					$this->Rect(262, $y, 20, 10);$this->SetXY(262,$y);$this->MultiCell(20,10,"--",'0','C');
				}
			}elseif($trim_data=="2"){
				$sum_prog = $row["metas"]["programadas"][3] + $row["metas"]["programadas"][4] + $row["metas"]["programadas"][5];
				$sum_ejec = $row["metas"]["ejecutadas"][3] + $row["metas"]["ejecutadas"][4] + $row["metas"]["ejecutadas"][5];
				$this->Rect(168, $y, 19, 10);$this->SetXY(168,$y);$this->MultiCell(19,10,$sum_prog,'0','R');
				$this->Rect(187, $y, 14, 10);$this->SetXY(187,$y);$this->MultiCell(14,10,$row["metas"]["ejecutadas"][3],'0','R');
				$this->Rect(201, $y, 14, 10);$this->SetXY(201,$y);$this->MultiCell(14,10,$row["metas"]["ejecutadas"][4],'0','R');
				$this->Rect(215, $y, 14, 10);$this->SetXY(215,$y);$this->MultiCell(14,10,$row["metas"]["ejecutadas"][5],'0','R');
				$this->Rect(229, $y, 14, 10);$this->SetXY(229,$y);$this->MultiCell(14,10,$sum_ejec,'0','R');
				if($sum_prog!="0"){
					$this->Rect(243, $y, 19, 10);$this->SetXY(243,$y);$this->MultiCell(19,10,round($sum_ejec*100/$sum_prog,2)."%",'0','R');
					$this->Rect(262, $y, 20, 10);$this->SetXY(262,$y);$this->MultiCell(20,10,round($sum_ejec*100/array_sum($row["metas"]["programadas"]),2)."%",'0','R');
				}else{
					$this->Rect(243, $y, 19, 10);$this->SetXY(243,$y);$this->MultiCell(19,10,"--",'0','C');
					$this->Rect(262, $y, 20, 10);$this->SetXY(262,$y);$this->MultiCell(20,10,"--",'0','C');
				}
			}elseif($trim_data=="3"){
				$sum_prog = $row["metas"]["programadas"][6] + $row["metas"]["programadas"][7] + $row["metas"]["programadas"][8];
				$sum_ejec = $row["metas"]["ejecutadas"][6] + $row["metas"]["ejecutadas"][7] + $row["metas"]["ejecutadas"][8];
				$this->Rect(168, $y, 19, 10);$this->SetXY(168,$y);$this->MultiCell(19,10,$sum_prog,'0','R');
				$this->Rect(187, $y, 14, 10);$this->SetXY(187,$y);$this->MultiCell(14,10,$row["metas"]["ejecutadas"][6],'0','R');
				$this->Rect(201, $y, 14, 10);$this->SetXY(201,$y);$this->MultiCell(14,10,$row["metas"]["ejecutadas"][7],'0','R');
				$this->Rect(215, $y, 14, 10);$this->SetXY(215,$y);$this->MultiCell(14,10,$row["metas"]["ejecutadas"][8],'0','R');
				$this->Rect(229, $y, 14, 10);$this->SetXY(229,$y);$this->MultiCell(14,10,$sum_ejec,'0','R');
				if($sum_prog!="0"){
					$this->Rect(243, $y, 19, 10);$this->SetXY(243,$y);$this->MultiCell(19,10,round($sum_ejec*100/$sum_prog,2)."%",'0','R');
					$this->Rect(262, $y, 20, 10);$this->SetXY(262,$y);$this->MultiCell(20,10,round($sum_ejec*100/array_sum($row["metas"]["programadas"]),2)."%",'0','R');
				}else{
					$this->Rect(243, $y, 19, 10);$this->SetXY(243,$y);$this->MultiCell(19,10,"--",'0','C');
					$this->Rect(262, $y, 20, 10);$this->SetXY(262,$y);$this->MultiCell(20,10,"--",'0','C');
				}
			}elseif($trim_data=="4"){
				$sum_prog = $row["metas"]["programadas"][9] + $row["metas"]["programadas"][10] + $row["metas"]["programadas"][11];
				$sum_ejec = $row["metas"]["ejecutadas"][9] + $row["metas"]["ejecutadas"][10] + $row["metas"]["ejecutadas"][11];
				$this->Rect(168, $y, 19, 10);$this->SetXY(168,$y);$this->MultiCell(19,10,$sum_prog,'0','R');
				$this->Rect(187, $y, 14, 10);$this->SetXY(187,$y);$this->MultiCell(14,10,$row["metas"]["ejecutadas"][9],'0','R');
				$this->Rect(201, $y, 14, 10);$this->SetXY(201,$y);$this->MultiCell(14,10,$row["metas"]["ejecutadas"][10],'0','R');
				$this->Rect(215, $y, 14, 10);$this->SetXY(215,$y);$this->MultiCell(14,10,$row["metas"]["ejecutadas"][11],'0','R');
				$this->Rect(229, $y, 14, 10);$this->SetXY(229,$y);$this->MultiCell(14,10,$sum_ejec,'0','R');
				if($sum_prog!="0"){
					$this->Rect(243, $y, 19, 10);$this->SetXY(243,$y);$this->MultiCell(19,10,round($sum_ejec*100/$sum_prog,2)."%",'0','R');
					$this->Rect(262, $y, 20, 10);$this->SetXY(262,$y);$this->MultiCell(20,10,round($sum_ejec*100/array_sum($row["metas"]["programadas"]),2)."%",'0','R');
				}else{
					$this->Rect(243, $y, 19, 10);$this->SetXY(243,$y);$this->MultiCell(19,10,"--",'0','C');
					$this->Rect(262, $y, 20, 10);$this->SetXY(262,$y);$this->MultiCell(20,10,"--",'0','C');
				}
			}
		}
	}
	function Footer()
	{
    	$this->SetXY(232,-22.5);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,"Página: ".$this->PageNo(),0,0,'C');
    	
    	$this->SetXY(32,-22.5);
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
$pdf->Publicar($items,$trim_data);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>