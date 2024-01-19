<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $concepto;
	var $mes;
	var $ano;
	function Filter($filtros){
		$this->concepto = $filtros["concepto"];
		$this->mes = $filtros["mes"];
		$this->ano = $filtros["ano"];
	}
	function Header(){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(190,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','B',11);
		$this->SetXY(10,20);$this->MultiCell(190,5,"CONCEPTOS POR PROGRAMA",'0','C');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Personal",'0','C');
		$this->SetFont('Arial','B',8);
		$this->SetXY(10,30);$this->MultiCell(190,5,"CONCEPTO  : ".$this->concepto["cod_sunat"]." ".$this->concepto["nomb"],'0','L');
		$this->SetXY(10,35);$this->MultiCell(190,5,"PERIODO      : ".strtoupper($meses[$this->mes])." - ".$this->ano,'0','L');
		$this->SetXY(10,40);$this->MultiCell(20,5,"Nº",'1','C');
		$this->SetXY(30,40);$this->MultiCell(130,5,"APELLIDOS Y NOMBRES",'1','C');
		$this->SetXY(160,40);$this->MultiCell(40,5,"MONTO",'1','C');
	}		
	function Publicar($items){
		$x=0;
		$y=45;
		$y_ini = 45;
		$this->SetFont('arial','',10);
		$total = 0;
		foreach($items as $orga){
			if($y>280){
				$this->AddPage();
				$y=$y_ini;
			}
			$this->SetFont('arial','',10);
			$this->SetXY(10,$y);$this->MultiCell(190,5,$orga["organizacion"]["nomb"],'1','L');
			$y+=5;
			foreach($orga["contratos"] as $cont){
				if($y>280){
					$this->AddPage();
					$y=$y_ini;
				}
				$this->SetFont('arial','',10);
				$this->SetXY(10,$y);$this->MultiCell(20,5,$cont["contrato"]["cod"],'1','C');
				$this->SetXY(30,$y);$this->MultiCell(170,5,$cont["contrato"]["nomb"],'1','L');
				$y+=5;
				$sub_total = 0;
				foreach($cont["trabajadores"] as $i=>$trab){
					if($y>280){
						$this->AddPage();
						$y=$y_ini;
					}
					$this->SetXY(10,$y);$this->MultiCell(20,5,($i+1),'1','R');
					$this->SetXY(30,$y);$this->MultiCell(130,5,$trab["trabajador"]["appat"]." ".$trab["trabajador"]["apmat"]." ".$trab["trabajador"]["nomb"],'1','L');
					$this->SetXY(160,$y);$this->MultiCell(40,5,number_format($trab["monto"],2),'1','R');
					$sub_total+=$trab["monto"];
					$y+=5;
				}
				$total+=$sub_total;
				$this->SetFont('arial','B',10);
				$this->SetXY(10,$y);$this->MultiCell(150,5,"SUBTOTAL",'1','C');
				$this->SetXY(160,$y);$this->MultiCell(40,5,number_format($sub_total,2),'1','R');
				$y+=5;
			}
		}
		$this->SetFont('arial','B',10);
		$this->SetXY(10,$y);$this->MultiCell(150,5,"TOTAL GENERAL",'1','C');
		$this->SetXY(160,$y);$this->MultiCell(40,5,number_format($total,2),'1','R');
		//$this->Rect(180, $y, 25, $alto);$this->SetXY(180,$y);$this->MultiCell(25,5,number_format($total_sald,2,".", ","),'0','R');
	}
	function Footer(){
  
	} 
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filter($filtros);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>