<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $filtros;
	function  filtros($filtros){
		$this->filtros = $filtros;
	}
	function Header(){
		$meses = array("TODOS","ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
		$this->SetFont('Arial','B',13);
		$this->SetXY(10,5);$this->MultiCell(190,5,"PLANILLA C.A.S. ".$meses[$this->filtros["mes"]]." ".$this->filtros["ano"]."\nSISTEMA NACIONAL DE PENSIONES",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(190,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Personal",'0','C');
			
		$this->SetFont('Arial','B',10);
		$this->SetXY(15,30);$this->MultiCell(150,5,"PROGRAMAS",'1','C');
		$this->SetXY(165,30);$this->MultiCell(30,5,"IMPORTE",'1','C');
	}		
	function Publicar($items){
		$y=35;
		$t=0;
		foreach($items as $orga){
			$t_par = 0;
			$this->SetFont('Arial','BU',9);	
			$this->SetXY(15,$y);$this->MultiCell(180,5,$orga["organizacion"]["nomb"],'0','L');
			$y+=5;
			$this->SetFont('Arial','',9);	
			foreach($orga["trabajadores"] as $trab){
				if($y>277){
					$this->AddPage();
					$y=35;
				}
				$t_par+=$trab["monto"];
				$t+=$t_par;
				$this->SetXY(15,$y);$this->MultiCell(150,5,$trab["appat"]." ".$trab["apmat"]." ".$trab["apmat"],'0','L');
				$this->SetXY(165,$y);$this->MultiCell(30,5,number_format($trab["monto"],2),'0','R');
				$y=$this->GetY();
			}
			$this->Line(15, $y, 195, $y);
			$this->SetXY(15,$y);$this->MultiCell(150,5,"SUB-TOTAL",'0','C');
			$this->SetXY(165,$y);$this->MultiCell(30,5,number_format($t_par,2),'0','R');
			$y+=5;
		}
		$this->Line(15, $y, 195, $y);
		$this->SetXY(15,$y);$this->MultiCell(150,5,"TOTAL",'0','C');
		$this->SetXY(165,$y);$this->MultiCell(30,5,number_format($t,2),'0','R');
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