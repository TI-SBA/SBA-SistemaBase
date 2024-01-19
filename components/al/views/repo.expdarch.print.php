<?php
global $f;
$f->library('pdf');

class repo extends FPDF
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
		$this->SetFont('Arial','B',15);
		$this->setY(15);$this->Cell(0,10,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",0,0,'C');
		$this->SetFont('Arial','I',13);
		$this->setY(20);$this->Cell(0,10,"EXPEDIENTES ARCHIVADOS",0,0,'C');
		$this->SetFont('Arial','B',8);
		$this->setXY(10,35);$this->MultiCell(30,10,"Nº Expediente",'1','C');
		$this->setXY(40,35);$this->MultiCell(60,10,"Demandante",'1','C');
		$this->setXY(100,35);$this->MultiCell(60,10,"Demandado",'1','C');
		$this->setXY(160,35);$this->MultiCell(50,10,"Materia",'1','C');
		$this->setXY(210,35);$this->MultiCell(70,10,"Ubicación del Archivo",'1','C');		
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
			$demandante = $row["demandante"]["nomb"];
			if($row["demandante"]["tipo_enti"]=="P"){
				$demandante = $row["demandante"]["appat"]." ".$row["demandante"]["apmat"].", ".$row["demandante"]["nomb"];
			}
			$demandado = $row["demandado"]["nomb"];
			if($row["demandado"]["tipo_enti"]=="P"){
				$demandado = $row["demandado"]["appat"]." ".$row["demandado"]["apmat"].", ".$row["demandado"]["nomb"];
			}
			$demandante_str = strlen($demandante);
			$demandado_str = strlen($demandado);
			$materia = strlen($row["materia"]);
			$ubic = strlen($row["ubicacion"]);
			$values = array($demandante_str,$demandado_str,$materia,$ubic);
			$alto = ceil($this->mayorValor($values)/40)*5;
			$this->SetFont('arial','',8);	
			$this->Rect(10, $y, 30, $alto);$this->SetXY(10,$y);$this->MultiCell(30,5,$row["numero"],'0','L');		
			$this->Rect(40, $y, 60, $alto);$this->SetXY(40,$y);$this->MultiCell(60,5,$demandante,'0','L');	
			$this->Rect(100, $y, 60, $alto);$this->SetXY(100,$y);$this->MultiCell(60,5,$demandado,'0','C');	
			$this->Rect(160, $y, 50, $alto);$this->SetXY(160,$y);$this->MultiCell(50,5,$row["materia"],'0','L');
			$this->Rect(210, $y, 70, $alto);$this->SetXY(210,$y);$this->MultiCell(70,5,$row["ubicacion"],'0','L');
			$index++;
			$y=$this->GetY()+$alto-10;
		}
	}
	function Footer()
	{
    	//Footer de la pagina
    	$this->SetXY(170,-21);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,"Página: ".$this->PageNo(),0,0,'C');
    	
    	$this->SetXY(28,-21);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,"Fecha de Impresión: ".date("d-m-Y"),0,0,'L');
	} 
	 
}

$pdf=new repo('L','mm','A4');
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