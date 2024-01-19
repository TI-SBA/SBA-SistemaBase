<?php
global $f;
$f->library('pdf');
class expdientes extends FPDF
{
	var $filter;
	function Filtros($filtros){
		$this->filter = $filtros;
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/td/expd.gif',15,15,347,249);
		$this->SetFont('Arial','B',14);
		$this->SetXY(10,10);$this->MultiCell(277,5,"MARGESI DE INMUEBLES",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(277,5,date("d/m/Y")." - Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Inmuebles",'0','C');
		
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,20);$this->MultiCell(10,20,"Nº",'1','C');
		$this->SetXY(20,20);$this->MultiCell(145,5,"DATOS DEL INMUEBLE",'1','C');
		
		$this->SetXY(20,25);$this->MultiCell(45,15,"DIRECCION",'1','C');
		
		$this->SetXY(65,25);$this->MultiCell(60,5,"UBICACION",'1','C');
		$this->SetXY(65,30);$this->MultiCell(20,10,"DISTRITO",'1','C');
		$this->SetXY(85,30);$this->MultiCell(20,10,"PROVINCIA",'1','C');
		$this->SetXY(105,30);$this->MultiCell(20,10,"DPTO.",'1','C');
		
		$this->SetXY(125,25);$this->MultiCell(40,5,"AREA (m^2)",'1','C');
		$this->SetXY(125,30);$this->MultiCell(20,10,"TERRENO",'1','C');
		$this->SetXY(145,30);$this->MultiCell(20,5,"CONSTRUIDO",'1','C');
		
		$this->SetXY(165,20);$this->MultiCell(30,20,"TIPO",'1','C');
		
		$this->SetXY(195,20);$this->MultiCell(40,10,"ESTADO",'1','C');
		$this->SetXY(195,30);$this->MultiCell(20,10,"OCUPADO",'1','C');
		$this->SetXY(215,30);$this->MultiCell(20,10,"DESOCUP.",'1','C');
		
		$this->SetXY(235,20);$this->MultiCell(52,20,"OBSERVACIONES",'1','C');
	}
	function Publicar($items){
		global $f;
		$f->library('helpers');
		$helper=new helper();
		$monedas = array(
			"S"=>"S/.",
			"D"=>"$"
		);
		$tipo_local = array(
			"CH"=>"COMP. HABIT.",
			"ED"=>"EDIFICIO",
			"PG"=>"PROGRAMA",
			"OT"=>"OTROS"
		);
		$this->SetFont('arial','',8);
		$y=40;
		$y_ini = $y;
		foreach($items as $i=>$item){
			if($y>190){
				$this->AddPage();
				$y=$y_ini;
			}
			$this->SetXY(10,$y);$this->MultiCell(10,5,$i+1,'1','C');			
			$this->SetXY(20,$y);$this->MultiCell(45,5,$item["ubic"]["direc"],'1','C');
			$this->SetXY(65,$y);$this->MultiCell(20,5,"AREQUIPA",'1','C');
			$this->SetXY(85,$y);$this->MultiCell(20,5,"AREQUIPA",'1','C');
			$this->SetXY(105,$y);$this->MultiCell(20,5,"AREQUIPA.",'1','C');			
			$this->SetXY(125,$y);$this->MultiCell(20,5,$item["area"]["terreno"],'1','C');
			$this->SetXY(145,$y);$this->MultiCell(20,5,$item["area"]["constr"],'1','C');			
			$this->SetXY(165,$y);$this->MultiCell(30,5,$tipo_local[$item["tipo"]],'1','C');
			$this->SetXY(195,$y);$this->MultiCell(20,5,"",'1','C');
			$this->SetXY(215,$y);$this->MultiCell(20,5,"",'1','C');
			$this->SetXY(235,$y);$this->MultiCell(52,5,"",'1','C');
			$y=$this->GetY();
		}	
	}
	function Footer()
	{
    	//Footer de la pagina
	} 
}
$pdf=new expdientes('L','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
//$pdf->Filtros($filter);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>