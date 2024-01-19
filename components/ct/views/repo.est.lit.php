<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $mes;
	var $ano;
	function Filter($filtros){
		$this->mes = $filtros["mes"];
		$this->ano = $filtros["ano"];
	}
	function Header(){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',15);
		$this->setY(15);$this->Cell(0,10,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",0,0,'C');
		$this->SetFont('Arial','I',13);
		$this->setY(20);$this->Cell(0,10,"NOTAS A LOS ESTADOS FINANCIEROS LITERALES ".strtoupper($meses[$this->mes])." - ".$this->ano,0,0,'C');
	}		
	function Publicar($items){
		$x=0;
		$y=35;
		foreach($items as $item){
			$str = strlen($item["descr"]);
			$filas = ceil($str/150)*5;
			if(($filas+$y)>185){
				$this->AddPage();
				$y = 35;
			}
			$this->SetFont('Arial','B',12);
			$this->SetXY(15,$y);$this->MultiCell(180,5,"NOTA Nro. ".$item["num"]." ".strtoupper($item["nomb"]),'0','L');			
			$y=$y+5;		
			$this->SetFont('Arial','',11);
			$this->SetXY(20,$y);$this->MultiCell(175,5,$item["descr"],'0','L');
			$y=$y+10+$filas;
		}
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
$pdf->Open();
$pdf->Filter($filtros);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>