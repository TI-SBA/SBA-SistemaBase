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
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		//$this->Image(IndexPath.DS.'templates/td/expd.gif',15,15,347,249);
		$this->SetFont('Arial','B',14);
		$this->SetXY(10,10);$this->MultiCell(190,5,"GASTOS DE SERVICIO TELEFONICO",'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." - Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Logistica",'0','C');
		$this->SetFont('arial','B',10);
		$this->SetXY(10,25);$this->MultiCell(190,5,"Organización: ".$this->filter["organizacion"]["nomb"],'0','L');
		$this->SetXY(10,25);$this->MultiCell(190,5,"Periodo: ".$meses[$this->filter["mes"]]." - ".$this->filter["ano"],'0','R');
		$this->SetXY(10,30);$this->MultiCell(40,5,"Número",'1','C');
		$this->SetXY(50,30);$this->MultiCell(60,5,"Responsable",'1','C');
		$this->SetXY(110,30);$this->MultiCell(50,5,"Operador",'1','C');
		$this->SetXY(160,30);$this->MultiCell(40,5,"Organización",'1','C');
	}
	function Publicar($items){
		global $f;
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		$this->SetFont('arial','',10);
		$y=35;
		$y_ini = $y;
		foreach($items as $i=>$item){
			if($y>270){
				$this->AddPage();
				$y=$y_ini;
			}
			$this->SetXY(10,$y);$this->MultiCell(40,5,$item["numero"],'0','L');
			$this->SetXY(50,$y);$this->MultiCell(60,5,$item["responsable"]["nomb"]." ".$item["responsable"]["appat"]." ".$item["responsable"]["apmat"],'0','L');
			$this->SetXY(110,$y);$this->MultiCell(50,5,$item["operador"]["nomb"],'0','L');
			$this->SetXY(160,$y);$this->MultiCell(40,5,$item["organizacion"]["nomb"],'0','L');
			$y=$this->GetY();
			if($y+10>270){
				$this->AddPage();
				$y=$y_ini;
			}
			if($item["pagos"]!=null){
				$this->SetFont('arial','B',10);
				$this->SetXY(10,$y);$this->MultiCell(190,5,"***PAGOS***",'0','C');
				$y+=5;
				$this->SetXY(10,$y);$this->MultiCell(38,5,"Periodo",'0','C');
				$this->SetXY(48,$y);$this->MultiCell(38,5,"Exceso de Llamadas",'0','C');
				$this->SetXY(86,$y);$this->MultiCell(38,5,"Total Sin IGV",'0','C');
				$this->SetXY(124,$y);$this->MultiCell(38,5,"Total IGV",'0','C');
				$this->SetXY(162,$y);$this->MultiCell(38,5,"Total Facturado",'0','C');
				$y+=5;
				$this->SetFont('arial','',10);
				$tot_0 = 0;
				$tot_1 = 0;
				$tot_2 = 0;
				$tot_3 = 0;
				foreach($item["pagos"] as $pago){
					if($y>270){
						$this->AddPage();
						$y=$y_ini;
					}
					$this->SetXY(10,$y);$this->MultiCell(38,5,$meses[$pago["periodo"]["mes"]]." - ".$pago["periodo"]["ano"],'0','L');
					$this->SetXY(48,$y);$this->MultiCell(38,5,$pago["exceso"],'0','R');
					$this->SetXY(86,$y);$this->MultiCell(38,5,number_format($pago["subtotal"],2),'0','R');
					$this->SetXY(124,$y);$this->MultiCell(38,5,number_format($pago["igv"],2),'0','R');
					$this->SetXY(162,$y);$this->MultiCell(38,5,number_format($pago["total"],2),'0','R');
					$tot_0+=$pago["exceso"];
					$tot_1+=$pago["subtotal"];
					$tot_2+=$pago["igv"];
					$tot_3+=$pago["total"];
					$y+=5;
				}
				$this->SetXY(10,$y);$this->MultiCell(38,5,"TOTAL ==>",'0','R');
				$this->SetXY(48,$y);$this->MultiCell(38,5,$tot_0,'0','R');
				$this->SetXY(86,$y);$this->MultiCell(38,5,number_format($tot_1,2),'0','R');
				$this->SetXY(124,$y);$this->MultiCell(38,5,number_format($tot_2,2),'0','R');
				$this->SetXY(162,$y);$this->MultiCell(38,5,number_format($tot_3,2),'0','R');
				$y+=5;
			}else{
				$this->SetXY(10,$y);$this->MultiCell(190,5,"No se encotrarón pagos a este número",'0','C');
				$y+=5;
			}
			$this->Line(10, $y, 200, $y);
			$y+=5;
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
$pdf->Filtros($filtros);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>