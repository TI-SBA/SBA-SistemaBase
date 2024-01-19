<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $nro;
	var $fecreg;
	function filtros($filtros){
		$this->nro = $filtros["nro"];
		$this->fecreg = $filtros["fecreg"];
	}
	function Header(){
		//$this->Image(IndexPath.DS.'templates/ts/comprobante.gif',10,10,190,275);	
		$y=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');		
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Logistica",'0','C');
		$this->SetFont('Arial','B',10);
		$y+=10;
		$this->SetXY(5,$y);$this->MultiCell(200,5,"ESPECIFICACIONES TECNICAS PARA EL REQUERIMIENTO DE BIENES",'0','C');
		$y+=5;
		$this->SetXY(5,$y);$this->MultiCell(200,5,"REQUERIMIENTO NRO. ".$this->nro,'0','C');
		$y+=10;
		$this->SetFont('Arial','B',9);
		/*$this->SetXY(140,$y);$this->MultiCell(20,5,"Nº",'1','C');
		$this->SetXY(160,$y);$this->MultiCell(15,5,"DÍA",'1','C');
		$this->SetXY(175,$y);$this->MultiCell(15,5,"MES",'1','C');
		$this->SetXY(190,$y);$this->MultiCell(15,5,"AÑO",'1','C');
		$y=$y+5;
		$this->SetXY(140,$y);$this->MultiCell(20,8,$this->nro,'1','C');
		$this->SetXY(160,$y);$this->MultiCell(15,8,Date::format($this->fecreg->sec, 'd'),'1','C');
		$this->SetXY(175,$y);$this->MultiCell(15,8,Date::format($this->fecreg->sec, 'm'),'1','C');
		$this->SetXY(190,$y);$this->MultiCell(15,8,Date::format($this->fecreg->sec, 'Y'),'1','C');
		$y=$y+5;*/
	}		
	function Publicar($items){
		$monedas = array(
			"S"=>array("simb"=>"S/.","nomb"=>"NUEVO SOL","plu"=>"NUEVOS SOLES"),
			"D"=>array("simb"=>"USSD $.","nomb"=>"DOLAR","plu"=>"DOLARES")
		);
		$estados = array(
			"P"=>"PEDIENTE",
			"A"=>"APROBADO",
			"X"=>"ANULADO"
		);
		$x=5;
		$y=35;
		$y_ini = $y;
		$y_temp = $y;
		$page_b = 275;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(50,5,"Area Usuaria:",'1','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(60,$y);$this->MultiCell(140,5,$items["oficina"]["nomb"],'1','L');
		$y+=5;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(50,5,"Actividad:",'1','L');
		$this->SetFont('Arial','',9);
		$programa = "--";
		if(isset($items["programa"]["actividad"])) $programa = $items["programa"]["actividad"]['cod'];
		$this->SetXY(60,$y);$this->MultiCell(140,5,$programa,'1','L');
		$y+=5;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(50,5,"Meta presupuestaria:",'1','L');
		$this->SetFont('Arial','',9);
		$meta = "--";
		if(isset($items["oficina"]["meta"])) $meta = $items["oficina"]["meta"]["cod"];
		$this->SetXY(60,$y);$this->MultiCell(140,5,$meta,'1','L');
		$y+=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(190,5,"ESPECIFICACIONES TENICAS",'1','C');
		$y+=5;
		$y_start = $y;
		$this->Line(10,$y,200,$y);
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(60,5,"1. DENOMINACION DE LA CONTRATACION",'0','L');
		$y_temp = $this->getY();
		$this->SetFont('Arial','',9);
		$this->SetXY(70,$y);$this->MultiCell(130,5,$items["bien"]["denominacion"],'0','L');
		$y=$this->getY();
		if($y_temp>$y) $y = $y_temp;
		$this->Line(10,$y,200,$y);
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(60,10,"2. ANTECEDENTES Y JUSTIFICACION",'0','L');
		$y_temp = $this->getY();
		$this->SetFont('Arial','',9);
		$this->SetXY(70,$y);$this->MultiCell(130,5,$items["bien"]["antecedentes"],'0','L');
		$y=$this->getY();
		if($y_temp>$y) $y = $y_temp;
		$this->Line(10,$y,200,$y);
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(60,5,"3. ESPECIFICACIONES TECNICAS DE LOS BIENES A CONTRATAR",'0','L');
		$y_temp = $this->getY();
		$this->SetFont('Arial','',9);
		$this->SetXY(70,$y);$this->MultiCell(130,5,$items["bien"]["especificaciones"],'0','L');
		$y=$this->getY();
		if($y_temp>$y) $y = $y_temp;
		$this->Line(10,$y,200,$y);
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(60,10,"3.1 Caracteristicas Tecnicas",'0','L');
		$y_temp = $this->getY();
		$this->SetFont('Arial','',9);
		$this->SetXY(70,$y);$this->MultiCell(130,5,$items["bien"]["caracteristicas"],'0','L');
		$y=$this->getY();
		if($y_temp>$y) $y = $y_temp;
		$this->Line(10,$y,200,$y);
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(60,10,"3.2 Normas Tecnicas",'0','L');
		$y_temp = $this->getY();
		$this->SetFont('Arial','',9);
		$this->SetXY(70,$y);$this->MultiCell(130,5,$items["bien"]["normas"],'0','L');
		$y=$this->getY();
		if($y_temp>$y) $y = $y_temp;
		$this->Line(10,$y,200,$y);
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(60,5,"3.3 Acondicionamiento, montaje o instalación",'0','L');
		$y_temp = $this->getY();
		$this->SetFont('Arial','',9);
		$this->SetXY(70,$y);$this->MultiCell(130,5,$items["bien"]["acondicionamiento"],'0','L');
		$y=$this->getY();
		if($y_temp>$y) $y = $y_temp;
		$this->Line(10,$y,200,$y);
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(60,10,"4. LUGAR DE ENTREGA",'0','L');
		$y_temp = $this->getY();
		$this->SetFont('Arial','',9);
		$this->SetXY(70,$y);$this->MultiCell(130,5,$items["bien"]["lugar_entrega"],'0','L');
		$y=$this->getY();
		if($y_temp>$y) $y = $y_temp;
		$this->Line(10,$y,200,$y);
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(60,10,"5. PLAZO DE ENTREGA",'0','L');
		$y_temp = $this->getY();
		$this->SetFont('Arial','',9);
		$this->SetXY(70,$y);$this->MultiCell(130,5,$items["bien"]["plazo_entrega"],'0','L');
		$y=$this->getY();
		if($y_temp>$y) $y = $y_temp;
		$this->Line(10,$y,200,$y);
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(60,10,"6. FORMA DE PAGO",'0','L');
		$y_temp = $this->getY();
		$this->SetFont('Arial','',9);
		$this->SetXY(70,$y);$this->MultiCell(130,5,$items["bien"]["forma_pago"],'0','L');
		$y=$this->getY();
		if($y_temp>$y) $y = $y_temp;
		$this->Line(10,$y,200,$y);
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(60,10,"7. GARANTIA (solo de ser necesario)",'0','L');
		$y_temp = $this->getY();
		$this->SetFont('Arial','',9);
		$this->SetXY(70,$y);$this->MultiCell(130,5,$items["bien"]["garantia"],'0','L');
		$y=$this->getY();
		if($y_temp>$y) $y = $y_temp;
		$this->Line(10,$y,200,$y);
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(60,10,"8. VISITAS Y MUESTRAS",'0','L');
		$y_temp = $this->getY();
		$this->SetFont('Arial','',9);
		$this->SetXY(70,$y);$this->MultiCell(130,5,$items["bien"]["visitas"],'0','L');
		$y=$this->getY();
		if($y_temp>$y) $y = $y_temp;
		$this->Line(10,$y,200,$y);
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(60,10,"9. CAPACITACIÓN",'0','L');
		$y_temp = $this->getY();
		$this->SetFont('Arial','',9);
		$this->SetXY(70,$y);$this->MultiCell(130,5,$items["bien"]["capacitacion"],'0','L');
		$y=$this->getY();
		if($y_temp>$y) $y = $y_temp;
		$this->Line(10,$y,200,$y);


		$this->Line(10,$y_start,10,$y);
		$this->Line(70,$y_start,70,$y);
		$this->Line(200,$y_start,200,$y);
		$y+=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(50,5,"Expediente: ".$items['expediente']['num'],'0','L');
	}
	function Footer()
	{
		
	} 
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(10,10,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->filtros($filtros);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>