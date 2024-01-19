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
		$this->SetXY(5,$y);$this->MultiCell(200,5,"TERMINOS DE REFERENCIA PARA LA CONTRATACION DE SERVICIOS EN GENERAL",'0','C');
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
		$this->SetXY(10,$y);$this->MultiCell(190,8,"I. UNIDAD ORGANIZA Y/O DEPENDENCIA QUE REQUIERE EL SERVICIO",'1','L');
		$y+=8;
		$this->SetFont('Arial','',9);
		$this->SetXY(10,$y);$this->MultiCell(190,5,$items["oficina"]["nomb"],'1','L');
		$y+=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(190,8,"II. JUSTIFICACION DE LA CONTRATACION",'1','L');
		$y+=8;
		$this->SetFont('Arial','',9);
		$this->SetXY(10,$y);$this->MultiCell(190,5,$items["locacion"]["justificacion_contrato"],'1','L');
		$y=$this->getY()+5;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(190,8,"III. DESCRIPCION BASICA DEL SERVICIO O CARACTERISTICA DEL SERVICIO",'1','L');
		$y+=8;
		$this->SetFont('Arial','',9);
		$this->SetXY(10,$y);$this->MultiCell(190,5,$items["locacion"]["descripcion"],'1','L');
		$y=$this->getY()+5;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(190,8,"IV. GRADO DE CALIFICACION",'1','L');
		$y+=8;
		$this->SetFont('Arial','',9);
		$this->SetXY(10,$y);$this->MultiCell(190,5,"1. Profesional: ".$items["locacion"]["grado_profesional"],'1','L');
		$y+=5;
		$this->SetXY(10,$y);$this->MultiCell(190,5,"2. Tecnico: ".$items["locacion"]["grado_tecnico"],'1','L');
		$y+=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(190,8,"V. REQUISITOS",'1','L');
		$y+=8;
		$this->SetFont('Arial','',9);
		$this->SetXY(10,$y);$this->MultiCell(190,5,"1. Profesion y/o especialidad: ".$items["locacion"]["profesion"],'1','L');
		$y=$this->getY();
		$this->SetXY(10,$y);$this->MultiCell(190,5,"2. Capacitacion y/o conocimientos: ".$items["locacion"]["capacitacion"],'1','L');
		$y=$this->getY();
		$this->SetXY(10,$y);$this->MultiCell(190,5,"3. Experiencia en años: ".$items["locacion"]["experiencia"],'1','L');
		$y+=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(190,8,"VI. DURACION DEL SERVICIO",'1','L');
		$y+=8;
		$this->SetFont('Arial','',9);
		$this->SetXY(10,$y);$this->MultiCell(190,5,"INICIO: ".$items["locacion"]["duracion"]["fecini"],'1','L');
		$this->SetXY(10,$y);$this->MultiCell(190,5,"TERMINO: ".$items["locacion"]["duracion"]["fecfin"],'0','R');
		$y+=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(190,8,"VII. VALOR REFERENCIAL",'1','L');
		$y+=8;
		$this->SetFont('Arial','',9);
		$this->SetXY(10,$y);$this->MultiCell(190,5,"MONTO TOTAL: S/. ".$items["locacion"]["valor_referencial"]["monto_total"],'1','L');
		$this->SetXY(10,$y);$this->MultiCell(190,5,"MONTO MENSUAL: S/. ".$items["locacion"]["valor_referencial"]["monto_mensual"],'0','R');
		$y+=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,$y);$this->MultiCell(190,8,"VIII. META PRESUPUESTARIA",'1','L');
		$y+=8;
		$this->SetFont('Arial','',9);
		$meta = "--";
		if(isset($items["oficina"]["meta"])) $meta = $items["oficina"]["meta"]["cod"];
		$this->SetXY(10,$y);$this->MultiCell(190,5,$meta,'1','L');
		
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