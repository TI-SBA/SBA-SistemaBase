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
		$this->SetXY(10,10);$this->MultiCell(190,5,"ESTADO DE DEUDORES",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." - Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Cementerio",'0','C');
	}
	function Publicar($items){
		global $f;
		$f->library('helpers');
		$helper=new helper();
		$monedas = array(
			"S"=>"S/.",
			"D"=>"$"
		);
		$this->SetFont('arial','',8);
		$y=40;
		$y_ini = $y;
		foreach($items as $serv){
			$this->SetFont('arial','BU',12);
			$this->SetXY(10,$y);$this->MultiCell(190,5,$serv["nomb"],'0','L');
			$y+=5;
			foreach($serv["clientes"] as $clie){
				$cliente = $clie["nomb"];
				if($clie["tipo_enti"]=="P"){
					$cliente .= " ".$clie["appat"]." ".$clie["apmat"];
				}
				$this->SetFont('arial','B',10);
				$this->SetXY(10,$y);$this->MultiCell(190,5,$cliente,'0','L');
				$y+=5;
				foreach($clie["espacios"] as $espa){
					$this->SetFont('arial','',10);
					$this->SetXY(10,$y);$this->MultiCell(190,5,$espa["nomb"],'0','L');
					$y+=5;
					$this->SetFont('arial','',8);
					$col = 10;
					foreach($espa["items"] as $r=>$item){
						if(($r%4==0)&($r!=0)){
							$y+=5;
							$col=10;
							if($y>275){
								$this->AddPage();
								$y=$y_ini;
							}
						}
						$this->SetXY($col,$y);$this->MultiCell(25,5,Date::format($item["fecven"]->sec,"d/m/Y")."   ==>",'0','L');
						$this->SetXY($col+25,$y);$this->MultiCell(190,5,$monedas[$item["moneda"]]." ".number_format($item["total"],2),'0','L');
						$col+=50;
					}
				}
			}
		}	
	}
	function Footer()
	{
    	//Footer de la pagina
	} 
}
$pdf=new expdientes('P','mm','A4');
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