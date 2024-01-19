<?php
global $f;
$f->library('pdf');

class presaper extends FPDF
{
	var $organizacion;
	var $organomb;
	var $periodo;
	var $mes;
	var $tipo;
	var $estadonomb;
	var $clasnomb;
	var $clasificador;
	var $total_fuen;
	function Filter($filtros,$p_fuentes){
		$this->organizacion = $filtros["organizacion"];
		$this->organomb = $filtros["organomb"];
		$this->periodo = $filtros["periodo"];	
		$this->mes = $filtros["mes"];	
		$this->tipo = $filtros["tipo"];	
		$this->estadonomb = $filtros["estadonomb"];	
		$this->clasnomb = $filtros["clasnomb"];	
		$this->clasificador = $filtros["clasificador"];	
		$this->total_fuen = count($p_fuentes);
	}
	function Header(){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		$tipos = array("I"=>"Ingresos","G"=>"Gastos");	
		$this->SetFont('Arial','BU',10);
		if($this->organizacion==""){
			$this->setY(5);$this->MultiCell(190,5,"ANEXO Nº 2",'0','C');
		}else{
			$this->setY(5);$this->MultiCell(190,5,"ANEXO Nº 2-A",'0','C');
		}
		$titulo = "";
		if($this->tipo=="I"){
			$titulo = "PROCESO PRESUPUESTARIO DE LAS SOCIEDADES DE BENEFICENCIA AÑO FISCAL ".$this->periodo." APROBACIÓN INSTITUCIONAL DE LOS INGRESOS PRESUPUESTARIOS";
			if($this->organizacion=="") $titulo .="-CONSOLIDADO";
		}elseif($this->tipo=="G"){
			$titulo = "PROCESO PRESUPUESTARIO DE LAS SOCIEDADES DE BENEFICENCIA AÑO FISCAL ".$this->periodo." APROBACIÓN INSTITUCIONAL DE LOS GASTOS PRESUPUESTARIOS";
			if($this->organizacion=="") $titulo .="-CONSOLIDADO";
		}
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',9);
		//$this->SetXY(5,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,280);$this->MultiCell(190,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		//$this->SetXY(5,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Planificacion y Presupuestos",'0','C');	
		$this->SetFont('Arial','B',10);
		$this->setY(10);$this->MultiCell(180,5,$titulo,'0','C');
		$this->SetFont('Arial','',8);
		$this->setY(20);$this->MultiCell(180,5,"(Nuevos Soles)",'0','C');
		$this->setXY(15,25);$this->MultiCell(113,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','L');
		$this->setXY(15,28.5);$this->MultiCell(113,5,"PROVINCIA: AREQUIPA",'0','L');
		if($this->organizacion==null){
			//$this->setXY(15,32);$this->MultiCell(113,5,"Organización: Todos",'1','L');
		}else{
			$this->setXY(15,32);$this->MultiCell(180,5,"ORGANIZACIÓN: ".$this->organizacion["cod"]." ".strtoupper($this->organizacion["nomb"]),'0','L');
		}		
		$this->SetFont('Arial','',8);
		//$this->setXY(128,32);$this->MultiCell(33,5,"Periodo: ".$this->periodo,'1','L');
		if($this->mes!="0") {
			$this->setXY(161,32);$this->MultiCell(34,5,"MES: ".$meses[$this->mes],'0','L');
		}
		//$this->setXY(15,37);$this->MultiCell(38,5,"Tipo: ".$tipos[$this->tipo],'1','L');
		if($this->clasificador==null){
			//$this->setXY(15,37);$this->MultiCell(142,5,"Clasificador: Todos",'1','L');
		}else{
			$this->setXY(15,37);$this->MultiCell(180,5,"CLASIFICADOR: ".$this->clasificador["cod"]." - ".$this->clasificador["nomb"],'1','L');
		}
		$this->SetFont('Arial','B',8);
		$this->setXY(15,42);$this->MultiCell(90,5,"Tipo de Transacción \n Genérica \n  Subgenérica \n   Específica",'1','L');
		$this->setXY(105,42);$this->MultiCell(28*$this->total_fuen+34,5,"FUENTES DE FINANCIAMIENTO",'1','C');
		$x_i = 105;
		for($i=0;$i<$this->total_fuen;$i++){
			$this->Rect($x_i, 47, 28, 15);
			$x_i +=28;
		}
		//$this->Rect(105, 47, 28, 15);
		//$this->Rect(133, 47, 28, 15);
		$this->setXY($x_i,47);$this->MultiCell(34,15,"TOTAL",'1','C');
	}		
	function Publicar($items,$importes,$p_fuentes){
		$this->SetFont('courier','B',8);
		$x=0;
		$y=62;
		$x_i=105;
		foreach($p_fuentes as $i=>$fuen){
				$this->SetFont('arial','B',8);	
				//$this->setXY($x_i,48);$this->MultiCell(30,4,$fuen["rubro"]."-".$fuen["cod"],'0','C');
				$this->setXY($x_i,48);$this->MultiCell(30,4,$fuen["rubro"],'0','C');
				$x_i+=28;	
				$x++;		
		}
		$y_marg = 4.8;
		$index=0;
		$this->SetY($y);
		$this->SetFont('arial','',7);
		$total_f_1 = 0;
		$total_f_2 = 0;
		foreach($items as $row){			
			$x_i = 105;
			if ($y>=265){//265
				$this->AddPage();$y=62;				
				$x_i=105;
				$this->SetFont('arial','B',8);	
				foreach($p_fuentes as $f=>$fuen){
					//$this->setXY($x_i,48);$this->MultiCell(30,4,$fuen["rubro"]."-".$fuen["cod"],'0','C');
					$this->setXY($x_i,48);$this->MultiCell(30,4,$fuen["rubro"],'0','C');
					$x_i+=28;		
				}		
				$this->setY($y);
			}
			$strlen = strlen($row["nomb"]);
			$alto = 4;
			if(strlen($row["cod"])==1||strlen($row["cod"])==3||strlen($row["cod"])==5){
				$this->SetFont('arial','B',7);	
			}else{
				$this->SetFont('arial','',7);	
			}
			
			$this->Rect(15, $y, 25, $alto);$this->SetXY(15,$y);$this->MultiCell(25,4,$row["cod"],'0','L');	
			$this->Rect(40, $y, 65, $alto);$this->SetXY(40,$y);$this->MultiCell(65,4,substr($row["nomb"],0,35),'0','L');
			$x_i = 105;
			$tot_p = 0;
			foreach($importes[$index]["importe"] as $k=>$imp){
				$monto = array_sum($imp);
				if($monto==0)$monto = "";
				else $monto = number_format($monto,2);
				$this->Rect($x_i, $y, 28, $alto);$this->SetXY($x_i,$y);$this->MultiCell(28,4,$monto,'0','R');
				if(strlen($row["cod"])==1){
					$total[$k] = array_sum($imp);
				}
				$tot_p += array_sum($imp);
				$x_i +=28;				
			}			
			//$suma = array_sum($importes[$index]["importe"][0]) + array_sum($importes[$index]["importe"][1]);
			$this->Rect($x_i, $y, 34, $alto);$this->SetXY($x_i,$y);$this->MultiCell(34,4,number_format($tot_p,2),'0','R');
			$index++;
			$y+=4;
		}
		$this->SetFont('arial','B',7);	
		$this->Rect(15, $y, 90, 5);$this->SetXY(15,$y);$this->MultiCell(90,5,"TOTAL",'0','C');	
		$x_i = 105;
		$total_all = 0;
		foreach($total as $t){
			$this->Rect($x_i, $y, 28, 5);$this->SetXY($x_i,$y);$this->MultiCell(28,5,number_format($t,2),'0','R');
			$total_all+=$t;
			$x_i +=28;	
		}
		$this->Rect($x_i, $y, 34, 5);$this->SetXY($x_i,$y);$this->MultiCell(34,5,number_format($total_all,2,".", " "),'0','R');
	}
	function Footer()
	{
    	
	} 
	 
}

$pdf=new presaper('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filter($filtros,$p_fuentes);
$pdf->AddPage();
$pdf->Publicar($items,$importes,$p_fuentes);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>