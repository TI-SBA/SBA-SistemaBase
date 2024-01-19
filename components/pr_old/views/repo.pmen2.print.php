<?php
global $f;
$f->library('pdf');

class pmen extends FPDF
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
	}
	function Header(){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		$tipos = array("I"=>"Ingresos","G"=>"Gastos");
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(277,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(5,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Planificacion y Presupuestos",'0','C');	
		$this->SetFont('Arial','B',14);
		$this->setY(10);$this->Cell(0,10,"PROGRAMACIÓN MENSUALIZADA DE SERVICIOS PRODUCTIVOS ",0,0,'C');
		$this->SetFont('Arial','',10);
		$this->SetXY(10,20);$this->MultiCell(277,5,"AÑO FISCAL ".$this->periodo,'0','C');
		$this->SetFont('arial','',6);
		$this->setXY(5,37);$this->MultiCell(65,3.3,"TIPO DE TRANSACCION \n GENERICA \n   SUB GENERICA - NIVEL 1 \n     SUB GENERICA - NIVEL 2 \n      ESPECIFICA - NIVEL 1 \n       ESPECIFICA - NIVEL 2",'1','L');
		$this->setXY(70,37);$this->MultiCell(220,5,"",'1','C');
		$this->SetFont('arial','',6);
		$this->Rect(70, 42, 20, 15);$this->setXY(70,42);$this->MultiCell(20,5,"PRESUPUESTO AUTORIZADO (PIA) ".$this->periodo,'0','C');
		$this->Rect(90, 42, 20, 15);$this->setXY(90,42);$this->MultiCell(20,5,"PRESUPUESTO AUTORIZADO (PIM) ".$this->periodo,'0','C');
		$this->SetFont('arial','',6);
		$this->Rect(110, 42, 15, 15);$this->setXY(110,42);$this->MultiCell(15,15,"ENERO",'0','C');
		$this->Rect(125, 42, 15, 15);$this->setXY(125,42);$this->MultiCell(15,15,"FEBRERO",'0','C');
		$this->Rect(140, 42, 15, 15);$this->setXY(140,42);$this->MultiCell(15,15,"MARZO",'0','C');
		$this->Rect(155, 42, 15, 15);$this->setXY(155,42);$this->MultiCell(15,15,"ABRIL",'0','C');
		$this->Rect(170, 42, 15, 15);$this->setXY(170,42);$this->MultiCell(15,15,"MAYO",'0','C');
		$this->Rect(185, 42, 15, 15);$this->setXY(185,42);$this->MultiCell(15,15,"JUNIO",'0','C');
		$this->Rect(200, 42, 15, 15);$this->setXY(200,42);$this->MultiCell(15,15,"JULIO",'0','C');
		$this->Rect(215, 42, 15, 15);$this->setXY(215,42);$this->MultiCell(15,15,"AGOSTO",'0','C');
		$this->Rect(230, 42, 15, 15);$this->setXY(230,42);$this->MultiCell(15,15,"SETIEMBRE",'0','C');
		$this->Rect(245, 42, 15, 15);$this->setXY(245,42);$this->MultiCell(15,15,"OCTUBRE",'0','C');
		$this->Rect(260, 42, 15, 15);$this->setXY(260,42);$this->MultiCell(15,15,"NOVIEMBRE",'0','C');
		$this->Rect(275, 42, 15, 15);$this->setXY(275,42);$this->MultiCell(15,15,"DICIEMBRE",'0','C');
		//$this->setXY(161,47);$this->MultiCell(34,15,"TOTAL",'1','C');
	}		
	function Publicar($items){
		$x=0;
		$y=57;
		$y_marg = 4.8;
		$this->SetY($y);		
		$this->SetFont('arial','',6);
		$alto = 5;
		foreach($items as $row){
			if($y>190){
				$this->AddPage();
				$y=57;
			}						
			$this->Rect(5, $y, 19, $alto);$this->SetXY(5,$y);$this->MultiCell(19,5,$row["cod"],'0','L');	
			$this->Rect(24, $y, 46, $alto);$this->SetXY(24,$y);$this->MultiCell(46,5,substr($row["nomb"],0,30),'0','L');	
			$tot_pia = array_sum($row["tot_pia"]);
			$this->Rect(70, $y, 20, $alto);$this->SetXY(70,$y);$this->MultiCell(20,5,number_format($tot_pia,2,".", ","),'0','R');
			$this->Rect(90, $y, 20, $alto);$this->SetXY(90,$y);$this->MultiCell(20,5,number_format(array_sum($row["tot_pim"])+$tot_pia,2,".", ","),'0','R');	
			$x_i = 110;
			for($i=1;$i<=12;$i++){
				$suma = array_sum($row["importes"][$i]);
				if($suma!=0){
					$suma = number_format($suma,2);
				}else{
					$suma = "";
				}
				$this->SetXY($x_i,$y);$this->MultiCell(15,5,$suma,'1','R');										
				$x_i = $x_i + 15;
			}
			$y=$y+5;
		}		
	}
	function Footer()
	{
    	
	} 
	 
}

$pdf=new pmen('L','mm','A4');
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