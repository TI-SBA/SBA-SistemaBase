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
		$this->setY(20);$this->Cell(0,10,"EXPEDIENTES ACTIVOS",0,0,'C');
		$this->SetFont('Arial','B',8);
		$this->setXY(5,35);$this->MultiCell(45,10,"Registrador",'1','C');
		$this->setXY(50,35);$this->MultiCell(25,10,"Nº Ubicación",'1','C');
		$this->setXY(75,35);$this->MultiCell(30,10,"Nº Expediente",'1','C');
		$this->setXY(105,35);$this->MultiCell(45,10,"Demandante",'1','C');
		$this->setXY(150,35);$this->MultiCell(45,10,"Demandado",'1','C');
		$this->setXY(195,35);$this->MultiCell(25,10,"Juzgado",'1','C');
		$this->setXY(220,35);$this->MultiCell(40,10,"Materia",'1','C');
		$this->setXY(260,35);$this->MultiCell(30,10,"Estado",'1','C');
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
			$abogado = $row["trabajador_autor"]["appat"]." ".$row["trabajador_autor"]["apmat"].", ".$row["trabajador_autor"]["nomb"];
			$autor = strlen($abogado);
			$juzgado = strlen($row["juzgado"]);
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
			$estado = strlen($row["estado"]);
			$values = array($autor,$juzgado,$demandante_str,$demandado_str,$materia,$estado);
			$alto = ceil($this->mayorValor($values)/20)*5;
			$this->SetFont('arial','',8);	
			$this->Rect(5, $y, 45, $alto);$this->SetXY(5,$y);$this->MultiCell(45,5,$abogado,'0','L');	
			$this->Rect(50, $y, 25, $alto);$this->SetXY(50,$y);$this->MultiCell(25,5,$row["ubicacion"],'0','L');	
			$this->Rect(75, $y, 30, $alto);$this->SetXY(75,$y);$this->MultiCell(30,5,$row["numero"],'0','L');	
			$this->Rect(105, $y, 45, $alto);$this->SetXY(105,$y);$this->MultiCell(45,5,$demandante,'0','L');	
			$this->Rect(150, $y, 45, $alto);$this->SetXY(150,$y);$this->MultiCell(45,5,$demandado,'0','C');	
			$this->Rect(195, $y, 25, $alto);$this->SetXY(195,$y);$this->MultiCell(25,5,$row["juzgado"],'0','L');
			$this->Rect(220, $y, 40, $alto);$this->SetXY(220,$y);$this->MultiCell(40,5,$row["materia"],'0','L');
			$this->Rect(260, $y, 30, $alto);$this->SetXY(260,$y);$this->MultiCell(30,5,$row["estado"],'0','L');
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