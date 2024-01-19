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
		$this->SetXY(10,10);$this->MultiCell(277,5,"FORMATO C-5\nPRESUPUESTO AUTORIZADO Y EJECUCION DE GASTOS\n(En Nuevos Soles)",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(277,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Planificación y Presupuesto",'0','C');
			
		$this->SetFont('Arial','B',10);
		$this->SetXY(10,30);$this->MultiCell(277,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','L');

	}		
	function Publicar($items){
		$this->SetFont('Arial','B',9);
		$y=35;
		$x=10;
		$this->SetXY($x,$y);$this->MultiCell(97,15,"GENERICA DEL GASTO",'1','C');
		$x+=97;
		$total = array();
		foreach($this->filtros["fuentes"] as $fuente){
			$this->SetXY($x,$y);$this->MultiCell(90,5,$fuente["cod"]." ".$fuente["rubro"],'1','C');
			$this->SetXY($x,$y+5);$this->MultiCell(30,5,"PRESUPUESTO INICIAL",'1','C');
			$x+=30;
			$this->SetXY($x,$y+5);$this->MultiCell(30,5,"PRESUPUESTO MODIFICADO",'1','C');
			$x+=30;
			$this->SetXY($x,$y+5);$this->MultiCell(30,5,"EJECUCION AL AÑO",'1','C');
			$x+=30;
			$total[$fuente["_id"]->{'$id'}]["pia"] = 0;
			$total[$fuente["_id"]->{'$id'}]["pim"] = 0;
			$total[$fuente["_id"]->{'$id'}]["eje"] = 0;
		}
		$y+=15;
		$x=10;
		$this->SetFont('Arial','',9);
		foreach($items as $item){
			$x=107;
			foreach($item["fuentes"] as $fuente){
				$pia = $fuente["pia"];
				$pim = $fuente["pim"];
				$eje = $fuente["eje"];
				if(strlen($item["clasificador"]["cod"])==1){
					$total[$fuente["fuente"]["_id"]->{'$id'}]["pia"]+=$pia;
					$total[$fuente["fuente"]["_id"]->{'$id'}]["pim"]+=$pim;
					$total[$fuente["fuente"]["_id"]->{'$id'}]["eje"]+=$eje;
				}
				$this->SetXY($x,$y);$this->MultiCell(30,5,number_format($pia,2),'1','R');
				$x+=30;
				$this->SetXY($x,$y);$this->MultiCell(30,5,number_format($pia+$pim,2),'1','R');
				$x+=30;
				$this->SetXY($x,$y);$this->MultiCell(30,5,number_format($eje,2),'1','R');
				$x+=30;
			}
			$this->SetXY(10,$y);$this->MultiCell(97,5,$item["clasificador"]["cod"]." ".$item["clasificador"]["nomb"],'1','L');
			$y=$this->GetY();
		}
		$x=107;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(97,5,"TOTAL",'1','C');
		foreach($this->filtros["fuentes"] as $fuente){
			$pia = $total[$fuente["_id"]->{'$id'}]["pia"];
			$pim = $total[$fuente["_id"]->{'$id'}]["pim"];
			$eje = $total[$fuente["_id"]->{'$id'}]["eje"];
			$this->SetXY($x,$y);$this->MultiCell(30,5,number_format($pia,2),'1','R');
			$x+=30;
			$this->SetXY($x,$y);$this->MultiCell(30,5,number_format($pia+$pim,2),'1','R');
			$x+=30;
			$this->SetXY($x,$y);$this->MultiCell(30,5,number_format($eje,2),'1','R');
			$x+=30;
		}
		//$this->SetXY(245,$y);$this->MultiCell(40,5,"",'1','C');
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