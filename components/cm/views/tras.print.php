<?php
global $f;
$f->library('pdf');
class tras extends FPDF
{
	var $ano;
	var $mes;
	
	function Filter($filter){
		$this->ano = $filter["ano"];
		$this->mes = $filter["mes"];		
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/cm/o.gif',15,15,180,276);
		$meses = array('Todos','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre');
		$this->SetFont('courier','B',12);
		$this->SetXY(50,5);$this->Cell(54,15,"Reporte de Traslados Internos y Externos",'0',0,'L',0);	
		$this->SetXY(3,15);$this->Cell(20,15,"Periodo: ".$meses[$this->mes]." - ".$this->ano,'0',0,'L',0);	
		
		$this->SetFont('courier','',8);
		$this->SetXY(3,25);$this->MultiCell(8,10,"Nro",'1','C');
		$this->SetFont('courier','',12);
		$this->SetXY(11,25);$this->MultiCell(25,5,"DIA REALIZADO",'1','C');
		$this->SetXY(36,25);$this->MultiCell(20,10,"RECIBO",'1','C');
		$this->SetXY(56,25);$this->MultiCell(45,10,"DIFUNTO",'1','C');
		$this->SetXY(101,25);$this->MultiCell(30,10,"PABELLON",'1','C');
		$this->SetXY(131,25);$this->MultiCell(15,10,"NICHO",'1','C');
		$this->SetFont('courier','',9);
		$this->SetXY(146,25);$this->MultiCell(10,10,"FILA",'1','C');
		$this->SetFont('courier','',12);
		$this->SetXY(156,25);$this->MultiCell(50,10,"DESTINO",'1','C');
	}
	function Publicar($items){	
		$y = 25;
		$this->SetY($y);
		if(count($items)>0){
			$this->SetFont('courier','',8);
			foreach($items as $i=>$item){
				if($y>270){
					$this->AddPage();$y=25;
				}
				$y = $y + 10;
				$this->Rect(3, $y, 8, 10);
				$this->SetXY(3,$y);$this->MultiCell(8,10,$i+1,'0','C');	
				$this->Rect(11, $y, 25, 10);		
				$this->SetXY(11,$y);$this->MultiCell(25,10,Date::format($item["ejecucion"]["fecfin"], 'd/m/Y'),'0','C');
				$recibos = "";
				foreach($item["recibos"] as $recibo){
					$recibos = $recibos.$recibo."\n";
				}
				$this->Rect(36, $y, 20, 10);$this->SetXY(36,$y);$this->MultiCell(20,10,$recibos,'0','C');
				$this->Rect(56, $y, 45, 10);$this->SetXY(56,$y);$this->MultiCell(45,5,$item["ocupante"]["nomb"]." ".$item["ocupante"]["appat"]." ".$item["ocupante"]["apmat"]."    ",'0','L');
				$this->Rect(101, $y, 30, 10);$this->SetXY(101,$y);$this->MultiCell(30,5,$item["espacio"]["pabellon"],'0','C');
				$this->Rect(131, $y, 15, 10);$this->SetXY(131,$y);$this->MultiCell(15,10,$item["espacio"]["nicho"],'0','C');
				$this->Rect(146, $y, 10, 10);$this->SetXY(146,$y);$this->MultiCell(15,10,$item["espacio"]["fila"],'0','C');
				if($item["traslado"]["espacio_destino"]){
					$destino = $item["traslado"]["espacio_destino"]["nomb"];
				}elseif($item["traslado"]["cementerio"]){
					$destino = $item["traslado"]["cementerio"]["nomb"];
				}
				$this->Rect(156, $y, 50, 10);$this->SetXY(156,$y);$this->MultiCell(50,5,$destino,'0','L');
			}
		}
	}
	function Footer()
	{
    	//Footer de la pagina
    	$this->SetXY(220,-15);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,$this->PageNo(),0,0,'C');
    	$this->SetXY(29,-15);
    	$this->SetFont('Arial','I',8);
    	$this->SetTextColor(128);
    	$this->Cell(0,10,date("d-m-Y"),0,0,'L');
	} 
	 
}

$pdf=new tras('P','mm','A4');
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filter($filter);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>