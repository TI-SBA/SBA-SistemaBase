<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $ano;
	var $organizacion;
	var $fuente;
	var $clasificador;
	function Filter($filtros){
		$this->ano = $filtros["ano"];
		$this->organizacion = $filtros["organizacion"];
		$this->fuente = $filtros["fuente"];
		$this->clasificador = $filtros["clasificador"];
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',15);
		$this->setXY(75,15);$this->Cell(60,10,"AUXILIAR ESTANDAR",0,0,'C');
		$this->SetFont('Arial','I',13);
		$this->setXY(75,20);$this->Cell(60,10,"CUENTAS PRESUPUESTARIAS DE GASTO",0,0,'C');
		$this->SetFont('Arial','',8);
		$this->Rect(170, 10, 40, 10);$this->SetXY(170,10);$this->MultiCell(40,5,"CODIGO DE LA DEPENDENCIA",'0','C');
		$this->Rect(210, 10, 80, 10);$this->SetXY(210,10);$this->MultiCell(80,5,"DENOMINACION",'0','C');
		$this->Rect(170, 20, 40, 5);$this->SetXY(170,20);$this->MultiCell(40,5,substr($this->organizacion,0,20),'0','C');
		$this->Rect(210, 20, 80, 5);
		$this->Rect(170, 25, 40, 5);$this->SetXY(170,25);$this->MultiCell(40,5,substr("FUENTE DE FINANCIAMIENTO",0,20),'0','C');
		$this->Rect(210, 25, 80, 5);$this->SetXY(210,25);$this->MultiCell(80,5,substr($this->fuente,0,20),'0','C');
		$this->Rect(170, 30, 40, 5);$this->SetXY(170,30);$this->MultiCell(40,5,"COD. CLASIFICADOR",'0','C');
		$this->Rect(210, 30, 80, 5);$this->SetXY(210,30);$this->MultiCell(80,5,"DENIMINACIÓN DE LA ESPECIFICA",'0','C');
		$this->Rect(170, 35, 40, 5);
		$this->Rect(210, 35, 80, 5);
		$this->Rect(170, 40, 40, 5);$this->SetXY(170,40);$this->MultiCell(40,5,"AÑO : ".$this->ano,'0','C');
		$this->Rect(210, 40, 80, 5);
		
		/** Header Tabs */
		$this->SetFont('Arial','B',7);
		$this->SetXY(5,50);$this->MultiCell(20,5,"MES",'1','C');
		$this->SetXY(5,55);$this->MultiCell(10,5,"MES",'1','C');
		$this->SetXY(15,55);$this->MultiCell(10,5,"DIA",'1','C');
		$this->SetXY(25,50);$this->MultiCell(35,5,"COMPROBANTE",'1','C');
		$this->SetXY(25,55);$this->MultiCell(20,5,"CLASE",'1','C');
		$this->SetXY(45,55);$this->MultiCell(15,5,"Nro",'1','C');
		$this->SetXY(60,50);$this->MultiCell(50,10,"DESCRIPCIÓN",'1','C');
		
		$this->SetXY(110,50);$this->MultiCell(60,5,"EJECUCION DE PRESUPUESTO",'1','C');
		$this->SetXY(110,55);$this->MultiCell(20,5,"DEBE",'1','C');
		$this->SetXY(130,55);$this->MultiCell(20,5,"HABER",'1','C');
		$this->SetXY(150,55);$this->MultiCell(20,5,"SALDO",'1','C');
		
		$this->SetXY(170,50);$this->MultiCell(60,5,"ASIGNACIONES COMPROMETIDAS",'1','C');
		$this->SetXY(170,55);$this->MultiCell(20,5,"DEBE",'1','C');
		$this->SetXY(190,55);$this->MultiCell(20,5,"HABER",'1','C');
		$this->SetXY(210,55);$this->MultiCell(20,5,"SALDO",'1','C');
		
		$this->SetXY(230,50);$this->MultiCell(60,5,"ASIGNACIONES COMPROMETIDAS",'1','C');
		$this->SetXY(230,55);$this->MultiCell(20,5,"DEBE",'1','C');
		$this->SetXY(250,55);$this->MultiCell(20,5,"HABER",'1','C');
		$this->SetXY(270,55);$this->MultiCell(20,5,"SALDO",'1','C');
	}		
	function Publicar($items){
		$x=0;
		$y=35;
		foreach($items as $item){
			/*$str = strlen($item["descr"]);
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
			$y=$y+10+$filas;*/ 
		}
	}
	function Footer()
	{
    	//Footer de la pagina
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