<?php
global $f;
$f->library('pdf');

class prop extends FPDF
{
	function Header(){
		$this->Image(IndexPath.DS.'templates/pe/legajo.gif',15,15,180,276);
	}		
	function Publicar($ficha,$enti){
		$this->SetFont('courier','',9);
		$x=0;
		$y=49;//41
		$y_marg = 5;
		$this->ln();
		$this->SetFont('courier','',10);	
		$meses = array("ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
		$this->SetXY(38,32.5);$this->Cell(80,$y_marg,$enti["appat"]." ".$enti["apmat"].", ".$enti["nomb"],'0',0,'L',0);
		$this->SetXY(130,32.5);$this->Cell(60,$y_marg,$enti["docident"][0]["num"],'0',0,'L',0);
		$this->SetXY(48,37.5);$this->Cell(60,$y_marg,$enti["roles"]["trabajador"]["cargo"]["organizacion"]["nomb"],'0',0,'L',0);
		$this->SetXY(134,37.5);$this->Cell(60,$y_marg,$enti["roles"]["trabajador"]["cargo"]["nomb"],'0',0,'L',0);
		if(isset($ficha["fec_ing_sbpa"])){
			$this->SetXY(46,42.5);$this->Cell(60,$y_marg,Date::format($ficha["fec_ing_sbpa"]->sec, 'Y-m-d'),'0',0,'L',0);
		}
		$this->SetY($y);
		$index=1;
		$y_ini=47.5;
		$this->setY($y_ini);
		if(isset($ficha["vacaciones"])){
			$this->SetFont('arial','',10);
			$this->Cell(50,$y_marg,"VACACIONES",'0',0,'L',0);
			$this->ln();
			$this->SetFont('courier','',10);			
			for($i=0;$i<count($ficha["vacaciones"]);$i++){	
				if($y_pag>245){
					$this->addPage();
					$this->SetXY(38,32.5);$this->Cell(80,$y_marg,$enti["appat"]." ".$enti["apmat"].", ".$enti["nomb"],'0',0,'L',0);
					$this->SetXY(130,32.5);$this->Cell(60,$y_marg,$enti["docident"][0]["num"],'0',0,'L',0);
					$this->SetXY(48,37.5);$this->Cell(60,$y_marg,$enti["roles"]["trabajador"]["cargo"]["organizacion"]["nomb"],'0',0,'L',0);
					$this->SetXY(134,37.5);$this->Cell(60,$y_marg,$enti["roles"]["trabajador"]["cargo"]["nomb"],'0',0,'L',0);
					$this->SetXY(46,42.5);$this->Cell(60,$y_marg,Date::format($ficha["fec_ing_sbpa"]->sec, 'Y-m-d'),'0',0,'L',0);
					$this->setY($y_ini);
				}		
				$this->Cell(30,$y_marg,Date::format($ficha["vacaciones"][$i]["fec"]->sec, 'Y-m-d'),'0',0,'L',0);
				$this->MultiCell(160,$y_marg,$ficha["vacaciones"][$i]["descr"],'0','L');
				$y_pag=$this->getY();
			}
			$this->ln();
		}
		if(isset($ficha["licencias"])){
			$this->SetFont('arial','',10);
			$this->Cell(50,$y_marg,"LICENCIAS",'0',0,'L',0);
			$this->ln();
			$this->SetFont('courier','',10);			
			for($i=0;$i<count($ficha["licencias"]);$i++){	
				if($y_pag>245){
					$this->addPage();
					$this->SetXY(38,32.5);$this->Cell(80,$y_marg,$enti["appat"]." ".$enti["apmat"].", ".$enti["nomb"],'0',0,'L',0);
					$this->SetXY(130,32.5);$this->Cell(60,$y_marg,$enti["docident"][0]["num"],'0',0,'L',0);
					$this->SetXY(48,37.5);$this->Cell(60,$y_marg,$enti["roles"]["trabajador"]["cargo"]["organizacion"]["nomb"],'0',0,'L',0);
					$this->SetXY(134,37.5);$this->Cell(60,$y_marg,$enti["roles"]["trabajador"]["cargo"]["nomb"],'0',0,'L',0);
					$this->SetXY(46,42.5);$this->Cell(60,$y_marg,Date::format($ficha["fec_ing_sbpa"]->sec, 'Y-m-d'),'0',0,'L',0);
					$this->setY($y_ini);
				}
				$this->Cell(30,$y_marg,Date::format($ficha["licencias"][$i]["fec"]->sec, 'Y-m-d'),'0',0,'L',0);
				$this->MultiCell(150,$y_marg,$ficha["licencias"][$i]["descr"],'0','L');
				$y_pag=$this->getY();
			}
			$this->ln();
		}
		if(isset($ficha["meritos"])){
			$this->SetFont('arial','',10);
			$this->Cell(50,$y_marg,"MERITOS",'0',0,'L',0);
			$this->ln();
			$this->SetFont('courier','',10);			
			for($i=0;$i<count($ficha["meritos"]);$i++){		
				if($y_pag>245){
					$this->addPage();
					$this->SetXY(38,32.5);$this->Cell(80,$y_marg,$enti["appat"]." ".$enti["apmat"].", ".$enti["nomb"],'0',0,'L',0);
					$this->SetXY(130,32.5);$this->Cell(60,$y_marg,$enti["docident"][0]["num"],'0',0,'L',0);
					$this->SetXY(48,37.5);$this->Cell(60,$y_marg,$enti["roles"]["trabajador"]["cargo"]["organizacion"]["nomb"],'0',0,'L',0);
					$this->SetXY(134,37.5);$this->Cell(60,$y_marg,$enti["roles"]["trabajador"]["cargo"]["nomb"],'0',0,'L',0);
					$this->SetXY(46,42.5);$this->Cell(60,$y_marg,Date::format($ficha["fec_ing_sbpa"]->sec, 'Y-m-d'),'0',0,'L',0);
					$this->setY($y_ini);
				}	
				$this->Cell(30,$y_marg,Date::format($ficha["meritos"][$i]["fec"]->sec, 'Y-m-d'),'0',0,'L',0);
				$this->MultiCell(150,$y_marg,$ficha["meritos"][$i]["descr"],'0','L');
				$y_pag=$this->getY();
			}
			$this->ln();
		}
		if(isset($ficha["demeritos"])){
			$this->SetFont('arial','',10);
			$this->Cell(50,$y_marg,"DEMERITOS",'0',0,'L',0);
			$this->ln();
			$this->SetFont('courier','',10);
			for($i=0;$i<count($ficha["demeritos"]);$i++){
				if($y_pag>245){
					$this->addPage();
					$this->SetXY(38,32.5);$this->Cell(80,$y_marg,$enti["appat"]." ".$enti["apmat"].", ".$enti["nomb"],'0',0,'L',0);
					$this->SetXY(130,32.5);$this->Cell(60,$y_marg,$enti["docident"][0]["num"],'0',0,'L',0);
					$this->SetXY(48,37.5);$this->Cell(60,$y_marg,$enti["roles"]["trabajador"]["cargo"]["organizacion"]["nomb"],'0',0,'L',0);
					$this->SetXY(134,37.5);$this->Cell(60,$y_marg,$enti["roles"]["trabajador"]["cargo"]["nomb"],'0',0,'L',0);
					$this->SetXY(46,42.5);$this->Cell(60,$y_marg,Date::format($ficha["fec_ing_sbpa"]->sec, 'Y-m-d'),'0',0,'L',0);
					$this->setY($y_ini);
				}
				$this->Cell(30,$y_marg,Date::format($ficha["demeritos"][$i]["fec"]->sec, 'Y-m-d'),'0',0,'L',0);
				$this->MultiCell(150,$y_marg,$ficha["demeritos"][$i]["descr"],'0','L');
				$y_pag=$this->getY();
			}
			$this->ln();
		}
		if(isset($ficha["comisiones"])){
			$this->SetFont('arial','',10);
			$this->Cell(50,$y_marg,"COMISIONES",'0',0,'L',0);
			$this->ln();
			$this->SetFont('courier','',10);			
			for($i=0;$i<count($ficha["comisiones"]);$i++){		
				if($y_pag>245){
					$this->addPage();
					$this->SetXY(38,32.5);$this->Cell(80,$y_marg,$enti["appat"]." ".$enti["apmat"].", ".$enti["nomb"],'0',0,'L',0);
					$this->SetXY(130,32.5);$this->Cell(60,$y_marg,$enti["docident"][0]["num"],'0',0,'L',0);
					$this->SetXY(48,37.5);$this->Cell(60,$y_marg,$enti["roles"]["trabajador"]["cargo"]["organizacion"]["nomb"],'0',0,'L',0);
					$this->SetXY(134,37.5);$this->Cell(60,$y_marg,$enti["roles"]["trabajador"]["cargo"]["nomb"],'0',0,'L',0);
					$this->SetXY(46,42.5);$this->Cell(60,$y_marg,Date::format($ficha["fec_ing_sbpa"]->sec, 'Y-m-d'),'0',0,'L',0);
					$this->setY($y_ini);
				}	
				$this->Cell(30,$y_marg,Date::format($ficha["comisiones"][$i]["fec"]->sec, 'Y-m-d'),'0',0,'L',0);
				$this->MultiCell(150,$y_marg,$ficha["comisiones"][$i]["descr"],'0','L');
				$y_pag=$this->getY();
			}
			$this->ln();
		}
		if(isset($ficha["declaraciones"])){
			$this->SetFont('arial','',10);
			$this->Cell(50,$y_marg,"DECLARACIONES",'0',0,'L',0);
			$this->ln();
			$this->SetFont('courier','',10);
			for($i=0;$i<count($ficha["declaraciones"]);$i++){
				if($y_pag>245){
					$this->addPage();
					$this->SetXY(38,32.5);$this->Cell(80,$y_marg,$enti["appat"]." ".$enti["apmat"].", ".$enti["nomb"],'0',0,'L',0);
					$this->SetXY(130,32.5);$this->Cell(60,$y_marg,$enti["docident"][0]["num"],'0',0,'L',0);
					$this->SetXY(48,37.5);$this->Cell(60,$y_marg,$enti["roles"]["trabajador"]["cargo"]["organizacion"]["nomb"],'0',0,'L',0);
					$this->SetXY(134,37.5);$this->Cell(60,$y_marg,$enti["roles"]["trabajador"]["cargo"]["nomb"],'0',0,'L',0);
					$this->SetXY(46,42.5);$this->Cell(60,$y_marg,Date::format($ficha["fec_ing_sbpa"]->sec, 'Y-m-d'),'0',0,'L',0);
					$this->setY($y_ini);
				}
				$this->Cell(30,$y_marg,Date::format($ficha["declaraciones"][$i]["fec"]->sec, 'Y-m-d'),'0',0,'L',0);
				$this->MultiCell(150,$y_marg,$ficha["declaraciones"][$i]["descr"],'0','L');
				$y_pag=$this->getY();
			}
			$this->ln();
		}
	}
	function Footer()
	{
		$y_marg = 5;
		$this->SetXY(152,268);$this->Cell(60,$y_marg,date("d-m-Y"),'0',0,'L',0);
		$this->SetXY(15,268);$this->Cell(60,$y_marg,"Página: ".$this->PageNo(),'0',0,'L',0);
	} 
	 
}

$pdf=new prop('P','mm','A4');
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("boleta");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($ficha,$enti);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>