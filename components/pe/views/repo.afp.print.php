<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $filtros;
	var $is_last = false;
	function  filtros($filtros){
		$this->filtros = $filtros;
	}
	function Header(){
		$meses = array("TODOS","ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
		$this->SetFont('Arial','B',13);
		//print_r($total_pages);die();
		if(!$this->is_last){
			$this->SetXY(10,5);$this->MultiCell(190,5,"PLANILLA C.A.S. ".$meses[$this->filtros["mes"]]." ".$this->filtros["ano"]."\nA.F.P. x PROGRAMAS",'0','C');
		}else{
			$this->SetXY(10,5);$this->MultiCell(190,5,"PLANILLA C.A.S. ".$meses[$this->filtros["mes"]]." ".$this->filtros["ano"]."\nA.F.P. x A.F.P.",'0','C');
		}
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(190,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Personal",'0','C');
			
		$this->SetFont('Arial','B',8);
		$this->SetXY(5,30);$this->MultiCell(50,10,"A.F.P.",'1','C');
		$this->SetXY(55,30);$this->MultiCell(25,5,"APORTE OBLIGATORIO",'1','C');
		$this->SetXY(80,30);$this->MultiCell(50,5,"COMISION",'1','C');
		$this->SetXY(80,35);$this->MultiCell(25,5,"FLUJO",'1','C');
		$this->SetXY(105,35);$this->MultiCell(25,5,"MIXTA",'1','C');
		$this->SetXY(130,30);$this->MultiCell(25,5,"PRIMA DE SEGURO",'1','C');
		$this->SetXY(155,30);$this->MultiCell(25,10,"SUBTOTAL",'1','C');
		$this->SetXY(180,30);$this->MultiCell(25,10,"TOTAL",'1','C');
	}		
	function Publicar($items){
		$y=40;
		foreach($items as $conc){
			//$this->AddPage();
				
			$this->SetFont('Arial','BU',9);
			$this->SetXY(5,$y);$this->MultiCell(190,5,$conc["pension"]["nomb"],'0','L');
			$y+=5;
			$t_1 = 0;
			$t_2 = 0;
			$t_3 = 0;
			$t_4 = 0;
			$t_5 = 0;
			$t_6 = 0;
			foreach($conc["organizaciones"] as $orga){
				$this->SetFont('Arial','B',8);
				$this->SetXY(5,$y);$this->MultiCell(190,5,$orga["organizacion"]["nomb"],'0','L');
				$y+=5;
				$tot_1 = 0;
				$tot_2 = 0;
				$tot_3 = 0;
				$tot_4 = 0;
				$tot_5 = 0;
				$tot_6 = 0;
				foreach($orga["trabajadores"] as $trab){
					if($y>277){
						$this->AddPage();
						$y=40;
					}
					$tot_1+=$trab["ap"];
					$tot_2+=$trab["com"];
					$tot_3+=$trab["mix"];
					$tot_4+=$trab["pr"];
					$tot_5+=$trab["ap"]+$trab["com"]+$trab["mix"]+$trab["pr"];
					$tot_6+=$trab["ap"]+$trab["com"]+$trab["mix"]+$trab["pr"];
					$t_1+=$trab["ap"];
					$t_2+=$trab["com"];
					$t_3+=$trab["mix"];
					$t_4+=$trab["pr"];
					$t_5+=$trab["ap"]+$trab["com"]+$trab["mix"]+$trab["pr"];
					$t_6+=$trab["ap"]+$trab["com"]+$trab["mix"]+$trab["pr"];
					$this->SetFont('Arial','',7);
					$this->SetXY(55,$y);$this->MultiCell(25,5,number_format($trab["ap"],2),'0','R');
					$this->SetXY(80,$y);$this->MultiCell(25,5,number_format($trab["com"],2),'0','R');
					$this->SetXY(105,$y);$this->MultiCell(25,5,number_format($trab["mix"],2),'0','R');
					$this->SetXY(130,$y);$this->MultiCell(25,5,number_format($trab["pr"],2),'0','R');
					$this->SetXY(155,$y);$this->MultiCell(25,5,number_format($trab["ap"]+$trab["com"]+$trab["mix"]+$trab["pr"],2),'0','R');
					$this->SetXY(180,$y);$this->MultiCell(25,5,number_format($trab["ap"]+$trab["com"]+$trab["mix"]+$trab["pr"],2),'0','R');
					//$this->SetXY(5,$y);$this->MultiCell(50,5,$trab["roles"]["trabajador"]["cod_aportante"]." - ".$trab["appat"]." ".$trab["apmat"]." ".$trab["nomb"],'0','L');
					$this->SetXY(5,$y);$this->MultiCell(70,5,strtoupper($trab["appat"]." ".$trab["apmat"]." ".$trab["nomb"]),'0','L');
					$y=$this->GetY();
				}
				$this->Line(5, $y, 205, $y);
				$this->SetFont('Arial','B',8);
				$this->SetXY(5,$y);$this->MultiCell(50,5,"SUB-TOTAL >>>",'0','C');	
				$this->SetXY(55,$y);$this->MultiCell(25,5,number_format($tot_1,2),'0','R');
				$this->SetXY(80,$y);$this->MultiCell(25,5,number_format($tot_2,2),'0','R');
				$this->SetXY(105,$y);$this->MultiCell(25,5,number_format($tot_3,2),'0','R');
				$this->SetXY(130,$y);$this->MultiCell(25,5,number_format($tot_4,2),'0','R');
				$this->SetXY(155,$y);$this->MultiCell(25,5,number_format($tot_5,2),'0','R');
				$this->SetXY(180,$y);$this->MultiCell(25,5,number_format($tot_6,2),'0','R');
				$y+=5;
			}
			$this->Line(5, $y, 205, $y);
			$this->SetFont('Arial','B',8);
			$this->SetXY(5,$y);$this->MultiCell(50,5,"TOTAL >>>",'0','C');	
			$this->SetXY(55,$y);$this->MultiCell(25,5,number_format($t_1,2),'0','R');
			$this->SetXY(80,$y);$this->MultiCell(25,5,number_format($t_2,2),'0','R');
			$this->SetXY(105,$y);$this->MultiCell(25,5,number_format($t_3,2),'0','R');
			$this->SetXY(130,$y);$this->MultiCell(25,5,number_format($t_4,2),'0','R');
			$this->SetXY(155,$y);$this->MultiCell(25,5,number_format($t_5,2),'0','R');
			$this->SetXY(180,$y);$this->MultiCell(25,5,number_format($t_6,2),'0','R');
			$y+=5;
		}
		$this->is_last = true;
		$this->AddPage();
		$y=40;
		$tot_1 = 0;
		$tot_2 = 0;
		$tot_3 = 0;
		$tot_4 = 0;
		$tot_5 = 0;
		$tot_6 = 0;
		foreach($items as $conc){
			$this->SetFont('Arial','BU',9);
			$this->SetXY(5,$y);$this->MultiCell(190,5,$conc["pension"]["nomb"],'0','L');
			$t_1 = 0;
			$t_2 = 0;
			$t_3 = 0;
			$t_4 = 0;
			$t_5 = 0;
			$t_6 = 0;
			foreach($conc["organizaciones"] as $orga){
				foreach($orga["trabajadores"] as $trab){
					
				}
			}
			$this->SetFont('Arial','',7);
			$this->SetXY(55,$y);$this->MultiCell(25,5,number_format($t_1,2),'0','R');
			$this->SetXY(80,$y);$this->MultiCell(25,5,number_format($t_2,2),'0','R');
			$this->SetXY(105,$y);$this->MultiCell(25,5,number_format($t_3,2),'0','R');
			$this->SetXY(130,$y);$this->MultiCell(25,5,number_format($t_4,2),'0','R');
			$this->SetXY(155,$y);$this->MultiCell(25,5,number_format($t_5,2),'0','R');
			$this->SetXY(180,$y);$this->MultiCell(25,5,number_format($t_6,2),'0','R');
			$y+=5;
		}
		$this->Line(5, $y, 205, $y);
		$this->SetFont('Arial','B',8);
		$this->SetXY(5,$y);$this->MultiCell(50,5,"TOTAL >>>",'0','C');	
		$this->SetXY(55,$y);$this->MultiCell(25,5,number_format($tot_1,2),'0','R');
		$this->SetXY(80,$y);$this->MultiCell(25,5,number_format($tot_2,2),'0','R');
		$this->SetXY(105,$y);$this->MultiCell(25,5,number_format($tot_3,2),'0','R');
		$this->SetXY(130,$y);$this->MultiCell(25,5,number_format($tot_4,2),'0','R');
		$this->SetXY(155,$y);$this->MultiCell(25,5,number_format($tot_5,2),'0','R');
		$this->SetXY(180,$y);$this->MultiCell(25,5,number_format($tot_6,2),'0','R');
	}
	function Footer()
	{
    	//Footer de la pagina
	}  
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->filtros($filtros);
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();
?>