<?php
global $f;
$f->library('pdf');

class presmodi extends FPDF
{
	var $organizacion;
	var $organomb;
	var $periodo;
	var $mes;
	var $tipo;
	var $estadonomb;
	var $clasnomb;
	var $clasificador;
	function Filter($filtros){
		$this->organizacion = $filtros["organizacion"];
		$this->organomb = $filtros["organomb"];
		$this->periodo = $filtros["periodo"];	
		$this->mes = $filtros["mes"];	
		$this->tipo = $filtros["tipo"];	
		$this->estadonomb = $filtros["estadonomb"];	
		$this->clasnomb = $filtros["clasnomb"];	
		$this->clasificador = $filtros["clasificador"];	
	}
	function Header(){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		$tipos = array("I"=>"Ingresos","G"=>"Gastos");
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(190,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(5,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Planificacion y Presupuestos",'0','C');	
		$this->SetFont('Arial','B',14);
		$this->setY(10);$this->Cell(0,10,"PRESUPUESTO INSTITUCIONAL MODIFICADO",0,0,'C');
		$this->SetFont('arial','',8);
		if($this->organizacion==""){
			$this->setXY(15,32);$this->MultiCell(113,5,"Organización: Todos",'1','L');
		}else{
			$this->setXY(15,32);$this->MultiCell(113,5,"Organización: ".$this->organomb,'1','L');
		}		
		$this->setXY(128,32);$this->MultiCell(33,5,"Periodo: ".$this->periodo,'1','L');
		$this->setXY(161,32);$this->MultiCell(34,5,"Mes: ".$meses[$this->mes],'1','L');
		$this->setXY(15,37);$this->MultiCell(38,5,"Tipo: ".$tipos[$this->tipo],'1','L');
		if($filtros["clasificador"]==""){
			$this->setXY(53,37);$this->MultiCell(142,5,"Clasificador: Todos",'1','L');
		}else{
			$this->setXY(53,37);$this->MultiCell(142,5,"Clasificador: ".$this->clasnomb,'1','L');
		}
		$this->SetFont('arial','B',8);
		$this->setXY(15,42);$this->MultiCell(90,5,"Tipo de Transacción \n Genérica \n  Subgenérica \n   Específica",'1','L');
		$this->setXY(105,42);$this->MultiCell(90,5,"Fuentes de Financiamiento",'1','C');
		$this->Rect(105, 47, 28, 15);
		$this->Rect(133, 47, 28, 15);
		$this->setXY(161,47);$this->MultiCell(34,15,"TOTAL",'1','C');
	}		
	function Publicar($items,$importes,$p_fuentes){
		$this->SetFont('arial','B',8);
		$x=0;
		$y=62;
		foreach($p_fuentes as $fuen){
				if($x==0){
					$this->setXY(104,48);$this->MultiCell(30,4,$fuen["rubro"]."-".$fuen["cod"],'0','C');
				}elseif($x==1){
					$this->setXY(133,48);$this->MultiCell(30,4,$fuen["rubro"]."-".$fuen["cod"],'0','C');
				}
				$x++;		
		}
		$y_marg = 4.8;
		$index=0;
		$this->SetY($y);
		$this->SetFont('arial','',8);
		foreach($items as $row){
			if ($y>=265){//265
				$this->AddPage();$y=62;				
				$x=0;
				foreach($p_fuentes as $fuen){
					$this->SetFont('arial','B',8);	
					if($x==0){
						$this->setXY(104,48);$this->MultiCell(30,4,$fuen["rubro"]."-".$fuen["cod"],'0','C');
					}elseif($x==1){
						$this->setXY(133,48);$this->MultiCell(30,4,$fuen["rubro"]."-".$fuen["cod"],'0','C');
					}
					$x++;		
				}
				$this->setY($y);
			}
			$strlen = strlen($row["nomb"]);
			$alto = 5;
			$this->SetFont('arial','',8);	
			$this->Rect(15, $y, 25, $alto);$this->SetXY(15,$y);$this->MultiCell(25,5,$row["cod"],'0','L');	
			$this->Rect(40, $y, 65, $alto);$this->SetXY(40,$y);$this->MultiCell(65,5,substr($row["nomb"],0,35),'0','L');	
			$this->Rect(105, $y, 28, $alto);$this->SetXY(105,$y);$this->MultiCell(28,5,number_format(array_sum($importes[$index]["importe"][0]),2,".", " "),'0','R');
			$this->Rect(133, $y, 28, $alto);$this->SetXY(133,$y);$this->MultiCell(28,5,number_format(array_sum($importes[$index]["importe"][1]),2,".", " "),'0','R');	
			$suma = array_sum($importes[$index]["importe"][0]) + array_sum($importes[$index]["importe"][1]);
			$this->Rect(161, $y, 34, $alto);$this->SetXY(161,$y);$this->MultiCell(34,5,number_format($suma,2,".", " "),'0','R');
			$index++;
			$y=$this->GetY()+$alto-5;
		}
	}
	function Footer()
	{
    	
	} 
	 
}

$pdf=new presmodi('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filter($filtros);
$pdf->AddPage();
$pdf->Publicar($items,$importes,$p_fuentes);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>