<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $periodo;
	var $clasificacion;
	function Filter($filtros){
		$this->periodo = $filtros["periodo"];
		$this->clasificacion = $filtros["clasificacion"];			
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
		$this->SetXY(10,15);$this->MultiCell(277,5,"CONTINGENCIAS ".$this->clasificacion." PERIODO ".$this->periodo,'0','C');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Asesoria Legal",'0','C');
		
		/*
		$this->SetFont('Arial','B',15);
		$this->setY(15);$this->Cell(0,10,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",0,0,'C');
		$this->SetFont('Arial','I',13);
		$this->setY(20);$this->Cell(0,10,"CONTINGENCIAS ".$this->clasificacion." PERIODO ".$this->periodo,0,0,'C');*/
		$this->SetFont('Arial','B',8);
		$this->setXY(5,35);$this->MultiCell(25,10,"Nº Expediente",'1','C');
		$this->setXY(30,35);$this->MultiCell(35,10,"Demandante",'1','C');
		$this->setXY(65,35);$this->MultiCell(35,10,"Demandado",'1','C');
		$this->setXY(100,35);$this->MultiCell(30,10,"Materia",'1','C');
		$this->setXY(130,35);$this->MultiCell(20,10,"Monto S/.",'1','C');		
		$this->setXY(150,35);$this->MultiCell(20,10,"Monto $",'1','C');	
		$this->setXY(170,35);$this->MultiCell(25,5,"Estimación del Gasto",'1','C');	
		$this->setXY(195,35);$this->MultiCell(25,10,"Fecha Probable",'1','C');	
		$this->setXY(220,35);$this->MultiCell(35,10,"Principio Juridico",'1','C');	
		$this->setXY(255,35);$this->MultiCell(35,10,"Observaciones",'1','C');	
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
			if ($y>=180){//180
				$this->AddPage();$y=45;				
			}
			$demandante = $row["demandante"];
			$demandado = $row["demandado"];
			$demandante_str = strlen($demandante);
			$demandado_str = strlen($demandado);
			$materia = strlen($row["materia"]);
			$observ = strlen($row["observ"]);
			$princ_j = strlen($row["principio"]);
			$values = array($demandante_str,$demandado_str,$materia,$observ,$princ_j);
			$alto = ceil($this->mayorValor($values)/20)*5;
			$this->SetFont('arial','',8);	
			$this->Rect(5, $y, 25, $alto);$this->SetXY(5,$y);$this->MultiCell(25,5,$row["numero"],'0','L');		
			$this->Rect(30, $y, 35, $alto);$this->SetXY(30,$y);$this->MultiCell(35,5,$demandante,'0','L');	
			$this->Rect(65, $y, 35, $alto);$this->SetXY(65,$y);$this->MultiCell(35,5,$demandado,'0','L');	
			$this->Rect(100, $y, 30, $alto);$this->SetXY(100,$y);$this->MultiCell(30,5,$row["materia"],'0','L');
			$this->Rect(130, $y, 20, $alto);$this->SetXY(130,$y);$this->MultiCell(20,5,$row["monto"]["soles"],'0','L');
			$this->Rect(150, $y, 20, $alto);$this->SetXY(150,$y);$this->MultiCell(20,5,$row["monto"]["dolares"],'0','L');
			$this->Rect(170, $y, 25, $alto);$this->SetXY(170,$y);$this->MultiCell(25,5,$row["costo"],'0','L');
			$this->Rect(195, $y, 25, $alto);$this->SetXY(195,$y);$this->MultiCell(25,5,$row["fecha"],'0','L');
			$this->Rect(220, $y, 35, $alto);$this->SetXY(220,$y);$this->MultiCell(35,5,$row["principio"],'0','L');
			$this->Rect(255, $y, 35, $alto);$this->SetXY(255,$y);$this->MultiCell(35,5,$row["observ"],'0','L');
			$index++;
			$y=$this->GetY()+$alto-10;
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

$pdf=new repo('L','mm','A4');
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