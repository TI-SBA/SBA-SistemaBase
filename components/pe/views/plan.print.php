<?php
global $f;
$f->library('pdf');

class plani extends FPDF
{
	function Header(){
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
	}
		
	function Publicar($items,$mes,$tipo,$ano){
		$meses = array("0","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		$tipos = array("I"=>"Ingresos","G"=>"Gastos");
		$this->SetFont('courier','',8);
		$x=0;
		$y_marg = 4.8;
		$index=0;
		$this->SetFont('arial','',18);	
		$this->Cell(300,$y_marg,"PLANILLA ".$tipo." ".$meses[$mes]." ".$ano,'0',0,'L',0);
		$this->SetFont('courier','',8);
		$this->ln();
		$x_lab=62;
		$this->Line(0,20, 297, 20);
		$this->setXY(5,20);$this->MultiCell(50,$y_marg,"Apellidos y Nombres",'0','C');
		for($i=0;$i<count($items[0]["items"][0]["conceptos"]);$i++){
			$this->setXY($x_lab,20);$this->MultiCell(15,$y_marg,$items[0]["items"][0]["conceptos"][$i]["concepto"]["nomb"],'0','C');
			$x_lab=15+$x_lab;
		}
		$this->Line(0,41, 297, 41);
		$this->Line(0,43, 297, 43);
		$this->ln();
		$nro=1;
		$array_sum = array();
		if ($items !=null){
			foreach($items as $r => $dataRow){
			if ($y>=265){
				$this->AddPage();$y=0;
				$x=0;
			}
			$this->ln();
			$this->SetFont('arial','U',13);	
			$this->Cell(30,$y_marg,$dataRow["orga"][0]["nomb"],'0',0,'L',0);
			$this->SetFont('courier','',8);	
			for($a=0;$a<count($dataRow["items"][0]["conceptos"]);$a++){
					$array_sum[$a]=array();
				}
			for($i=0;$i<count($dataRow["items"]);$i++){
				$this->ln();
				$this->Cell(55,$y_marg,$nro.".- ".$dataRow["items"][$i]["trabajador"]["appat"]." ".$dataRow["items"][$i]["trabajador"]["apmat"].", ".$dataRow["items"][$i]["trabajador"]["nomb"],'0',0,'L',0);
				for($j=0;$j<count($dataRow["items"][$i]["conceptos"]);$j++){
					$this->Cell(15,$y_marg,$dataRow["items"][$i]["conceptos"][$j]["subtotal"],'0',0,'R',0);
					array_push($array_sum[$j], $dataRow["items"][$i]["conceptos"][$j]["subtotal"]);
				}
				$y_fin=$this->getY();
				$nro++;
			}
			$this->SetFont('courier','B',10);	
			$this->setXY(0,$y_fin+4.5);$this->Cell(60,$y_marg,"SUBTOTAL POR PROGRAMA",'0',0,'R',0);
			$this->SetFont('courier','',8);
			for($k=0;$k<count($array_sum);$k++){
				$this->Cell(15,$y_marg,number_format((float)array_sum($array_sum[$k]), 2, '.', ''),'0',0,'R',0);
			}
			$this->Line(0,$y_fin+4.5, 297, $y_fin+4.5);		
			$y=$this->GetY();
			$index++;
		}
			
			
		}
		
	}
	function Footer()
	{
    	//Footer de la pagina
    	$this->SetXY(170,-21);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,$this->PageNo(),0,0,'C');
    	
    	$this->SetXY(28,-21);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,date("d-m-Y"),0,0,'L');
	} 
	 
}

$pdf=new plani('P','mm',array(377.698,279.4));
$pdf->SetMargins(5,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($items,$mes,$tipo,$ano);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>