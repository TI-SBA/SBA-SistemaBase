<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $filtros;
	function  filtros($filtros){
		$this->filtros = $filtros;
	}
	function Header(){
		$this->SetFont('Arial','B',10);
		$this->SetXY(10,5);$this->MultiCell(277,5,"CIERRE Y CONCILIACION DEL PRESUPUESTO DEL SECTOR PUBLICO AÑO FISCAL ".$this->filtros["ano"],'0','C');
		$this->SetXY(10,10);$this->MultiCell(277,5,"FORMATO C-4\nEJECUCION DE GASTOS (NETO DE ANULACIONES) A NIVEL INSTITUCIONAL\n(En Nuevos Soles)",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(277,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Planificación y Presupuesto",'0','C');
			
		$this->SetFont('Arial','B',10);
		$fuente = "TODAS LAS FUENTES DE FINANCIAMIENTO";
		if(isset($this->filtros["fuente"])){
			$fuente = $this->filtros["fuente"]["cod"]." - ".$this->filtros["fuente"]["rubro"];
		}
		$this->SetXY(10,30);$this->MultiCell(277,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','L');
		$this->SetXY(10,35);$this->MultiCell(277,5,"FUENTE DE FINANCIAMIENTO: ".$fuente,'0','L');
		$this->SetXY(10,40);$this->MultiCell(75,20,"GENERICA DEL INGRESO",'1','C');
		$this->SetXY(85,40);$this->MultiCell(40,5,"Presupuesto Inicial de Apertura \n(PIA)\n ",'1','C');
		$this->SetXY(125,40);$this->MultiCell(40,5,"Presupuesto Institucional Modificado (PIM)\n(1)",'1','C');
		$this->SetXY(165,40);$this->MultiCell(40,10,"Captación AÑO ".$this->filtros["ano"]."\n(2)",'1','C');
		$this->SetXY(205,40);$this->MultiCell(40,10,"SALDO\n(3)=(2)-(1)",'1','C');
		$this->SetXY(245,40);$this->MultiCell(40,10,"Entidad y/o Organismo",'1','C');
	}		
	function Publicar($items){
		$this->SetFont('Arial','',9);
		$y=60;
		$total_0 = 0;
		$total_1 = 0;
		$total_2 = 0;
		$total_3 = 0;
		foreach($items as $item){
			$pim = $item["pia"]+$item["pim"];
			if(strlen($item["clasificador"]["cod"])==1){
				$total_0+=$item["pia"];
				$total_1+=$pim;
				$total_2+=$item["eje"];
				$total_3+=$item["eje"]-$pim;
			}
			$this->SetXY(85,$y);$this->MultiCell(40,5,number_format($item["pia"],2),'0','R');
			$this->SetXY(125,$y);$this->MultiCell(40,5,number_format($pim,2),'0','R');
			$this->SetXY(165,$y);$this->MultiCell(40,5,number_format($item["eje"],2),'0','R');
			$this->SetXY(205,$y);$this->MultiCell(40,5,number_format($item["eje"]-$pim,2),'0','R');
			$this->SetXY(10,$y);$this->MultiCell(75,5,$item["clasificador"]["cod"]." ".$item["clasificador"]["nomb"],'0','L');
			$y = $this->GetY();
		}
		$this->Line(10, 50, 10, $y);
		$this->Line(85, 50, 85, $y);
		$this->Line(125, 50, 125, $y);
		$this->Line(165, 50, 165, $y);
		$this->Line(205, 50, 205, $y);
		$this->Line(245, 50, 245, $y);
		$this->Line(285, 50, 285, $y);
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(75,5,"TOTAL",'1','C');
		$this->SetXY(85,$y);$this->MultiCell(40,5,number_format($total_0,2),'1','R');
		$this->SetXY(125,$y);$this->MultiCell(40,5,number_format($total_1,2),'1','R');
		$this->SetXY(165,$y);$this->MultiCell(40,5,number_format($total_2,2),'1','R');
		$this->SetXY(205,$y);$this->MultiCell(40,5,number_format($total_3,2),'1','R');
		$this->SetXY(245,$y);$this->MultiCell(40,5,"",'1','C');
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
$pdf->filtros($filtros);
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();
?>