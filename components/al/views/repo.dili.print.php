<?php
global $f;
$f->library('pdf');

class diliprog extends FPDF
{
	var $periodo;
	var $estado;
	function Filter($filtros){
		$this->periodo = $filtros["periodo"];
		$this->estado = $filtros["estado"];			
	}
	function mayorValor( $array ){
		$a = array_unique( $array );
		$s = 0;
		if( is_array( $a ) )
			foreach( $a as $v )
				$s = intval( $v ) > $s ? $v : $s;
		return $s;
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(277,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','B',13);
		//$title = "";
		//if($this->filtros[""])
		$this->SetXY(10,15);$this->MultiCell(277,5,"DILIGENCIAS ".strtoupper($this->estado)." PERIODO ".$this->periodo,'0','C');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Asesoria Legal",'0','C');
		
		/*
		$this->SetFont('Arial','B',15);
		$this->setY(15);$this->Cell(0,10,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",0,0,'C');
		$this->SetFont('Arial','I',13);
		$this->setY(20);$this->Cell(0,10,"DILIGENCIAS ".strtoupper($this->estado)." PERIODO ".$this->periodo,0,0,'C');*/
		$this->SetFont('Arial','B',8);
		$this->setXY(5,35);$this->MultiCell(10,10,"Nº",'1','C');
		$this->setXY(15,35);$this->MultiCell(30,10,"Nº Expediente",'1','C');
		$this->setXY(45,35);$this->MultiCell(50,10,"Abog. Responsable",'1','C');
		$this->setXY(95,35);$this->MultiCell(40,10,"Asunto",'1','C');
		$this->setXY(135,35);$this->MultiCell(50,10,"Ddte/Ddo",'1','C');
		$this->setXY(185,35);$this->MultiCell(30,10,"Fecha",'1','C');
		$this->setXY(215,35);$this->MultiCell(25,10,"Lugar",'1','C');
		$this->setXY(240,35);$this->MultiCell(50,10,"Observaciones",'1','C');
	}		
	function Publicar($items){
		$this->SetFont('courier','B',8);
		$x=0;
		$y=45;
		$y_marg = 4.8;
		$index=0;
		$this->SetY($y);
		$this->SetFont('courier','',8);
		foreach($items as $row){
			if ($y>=180){//265
				$this->AddPage();$y=45;				
			}
			$abogado = "No se ha encontrado Información";
			if(isset($row["expediente"]["trabajador_autor"])){
				$abogado = $row["expediente"]["trabajador_autor"]["appat"]." ".$row["expediente"]["trabajador_autor"]["apmat"].", ".$row["expediente"]["trabajador_autor"]["nomb"];	
			}
			$autor = strlen($abogado);
			$asunto = strlen($row["asunto"]);
			$demandante = "No se ha encontrado Información";
			if(isset($row["expediente"]["demandante"])){
				$demandante = $row["expediente"]["demandante"];
			}
			$demandado = "No se ha encontrado Información";
			if(isset($row["expediente"]["demandado"])){
				$demandado = $row["expediente"]["demandado"];				
			}
			$deman = strlen($demandante."/".$demandado);
			$lugar = strlen($row["lugar"]);
			$observ = strlen($row["observ"]);
			$values = array($autor,$asunto,$deman,$lugar,$observ);
			$alto = ceil($this->mayorValor($values)/50)*5;
			$this->SetFont('arial','',8);	
			/*$this->Rect(5, $y, 10, $alto);*/$this->SetXY(5,$y);$this->MultiCell(10,5,$index+1,'0','L');	
			/*$this->Rect(15, $y, 30, $alto);*/$this->SetXY(15,$y);$this->MultiCell(30,5,$row["expediente"]["numero"],'0','L');	
			/*$this->Rect(45, $y, 50, $alto);*/$this->SetXY(45,$y);$this->MultiCell(50,5,$abogado,'0','L');	
			/*$this->Rect(95, $y, 40, $alto);*/$this->SetXY(95,$y);$this->MultiCell(40,5,$row["asunto"],'0','L');	
			/*$this->Rect(135, $y, 50, $alto);*/$this->SetXY(135,$y);$this->MultiCell(50,5,$demandante."/".$demandado,'0','L');	
			/*$this->Rect(185, $y, 30, $alto);*/$this->SetXY(185,$y);$this->MultiCell(30,5,Date::format($row["fecha"]->sec, 'd/m/Y'),'0','C');		
			/*$this->Rect(240, $y, 50, $alto);*/$this->SetXY(240,$y);$this->MultiCell(50,5,$row["observ"],'0','L');
			/*$this->Rect(215, $y, 25, $alto);*/$this->SetXY(215,$y);$this->MultiCell(25,5,$row["lugar"],'0','L');
			$index++;
			$this->Line(5, $y, 290, $y);
			$y=$this->GetY()+$alto;
		}
	}
	function Footer()
	{
    	//Footer de la pagina
    	/*$this->SetXY(170,-21);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,"Página: ".$this->PageNo(),0,0,'C');
    	
    	$this->SetXY(28,-21);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,"Fecha de Impresión: ".date("d-m-Y"),0,0,'L');*/
	} 
	 
}

$pdf=new diliprog('L','mm','A4');
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