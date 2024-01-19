<?php
global $f;
$f->library('pdf');
setlocale(LC_ALL,"esp");
class repo extends FPDF
{
	var $filtros;
	function Filter($items){
		$this->filtros = $items;
	}
	function Header(){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(180,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','B',11);
		//$title = "";
		//if($this->filtros[""])
		$this->SetXY(10,25);$this->MultiCell(180,5,"ASIGNACION POR 20 AÑOS DE SERVICIO",'0','C');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Personal",'0','C');
	}		
	function Publicar($items){
		$x=0;
		$y=40;
		$y_ini = 40;
		$this->SetFont('arial','BU',10);		
		$this->SetXY(15,$y);$this->MultiCell(60,5,"DATOS PERSONALES",'0','L');
		$y+=5;
		$this->SetFont('arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(60,5,"NOMBRES Y APELLIDOS",'0','L');
		$this->SetFont('arial','',9);
		$dni = "";
		foreach($items["trabajador"]["docident"] as $doc){
			if($doc["tipo"]=="DNI")$dni=$doc["num"];
		}
		$this->SetXY(75,$y);$this->MultiCell(120,5,$items["trabajador"]["nomb"]." ".$items["trabajador"]["appat"]." ".$items["trabajador"]["apmat"],'0','L');
		$y+=5;
		$this->SetFont('arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(60,5,"CARGO",'0','L');
		$this->SetFont('arial','',9);
		$this->SetXY(75,$y);$this->MultiCell(120,5,$items["trabajador"]["roles"]["trabajador"]["cargo"]["nomb"],'0','L');
		$y+=5;
		$this->SetFont('arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(60,5,"NIVEL REMUNERATIVO",'0','L');
		$this->SetFont('arial','',9);
		$this->SetXY(75,$y);$this->MultiCell(120,5,$items["trabajador"]["roles"]["trabajador"]["nivel"]["abrev"],'0','L');
		$y+=5;
		$this->SetFont('arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(60,5,"PROGRAMA",'0','L');
		$this->SetFont('arial','',9);
		$this->SetXY(75,$y);$this->MultiCell(120,5,"falta",'0','L');
		$y+=5;
		$this->SetFont('arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(60,5,"TIEMPO DE SERVICIOS",'0','L');
		$this->SetFont('arial','',9);
		$this->SetXY(75,$y);$this->MultiCell(120,5,"25 AÑOS",'0','L');
		$y+=5;
		$this->SetFont('arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(60,5,"RECONOCIMIENTO",'0','L');
		$this->SetFont('arial','',9);
		$this->SetXY(75,$y);$this->MultiCell(120,5,"----",'0','L');
		$y+=5;
		$this->SetFont('arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(60,5,"REGIMEN LABORAL",'0','L');
		$this->SetFont('arial','',9);
		$this->SetXY(75,$y);$this->MultiCell(120,5,"D.LEG. 279",'0','L');
		$y+=5;
		$this->SetFont('arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(60,5,"REGIMEN PENSIONARIO",'0','L');
		$this->SetFont('arial','',9);
		$this->SetXY(75,$y);$this->MultiCell(120,5,$items["trabajador"]["roles"]["trabajador"]["pension"]["tipo"],'0','L');
		$y+=5;
		$this->Line(15, $y, 195, $y);
		$y+=5;
		$this->SetFont('arial','BU',9);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"LIQUIDACION DE ASIGNACION:",'0','L');
		$y+=5;
		
		$this->SetFont('arial','',9);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"De conformidad con lo dispuesto en el Art.54 inc. a) del D.Leg Nº 276, que establece el pago de una asignacion equivalente a (02) dos remuneraciones totales mensuales, por haber cumplido 25 años de servicio en la Administración Pública, beneficio que se otorga por única vez al trabajador, de acuerdo al siguiente detalle:",'0','L');
		$y=$this->GetY()+5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"Remuneración",'0','L');
		$y+=5;
		foreach($items["conceptos"] as $conc){
			$this->SetXY(15,$y);$this->MultiCell(100,5,$conc["concepto"]["nomb"],'0','L');
			$this->SetXY(115,$y);$this->MultiCell(20,5,number_format($conc["subtotal"],2),'0','R');
			$y+=5;
		}
		$this->Line(115, $y, 135, $y);
		$this->SetXY(115,$y);$this->MultiCell(20,5,number_format($items["total"],2),'0','R');
		$y+=10;
		$this->SetXY(115,$y);$this->MultiCell(30,5,"SUBTOTAL",'0','C');
		$this->SetXY(150,$y);$this->MultiCell(45,5,number_format($items["total"],2)."  X  2      S/. ".number_format($items["neto"],2),'0','R');
		$y+=5;
		$this->SetXY(135,$y);$this->MultiCell(30,5,"TOTAL",'0','C');
		$this->SetXY(165,$y);$this->MultiCell(30,5,"S/. ".number_format($items["neto"],2),'0','R');
		$y+=10;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"Nota.- No afecto a Leyes Sociales según Art. 2, inc. \"g\" DS 179-91-PCM e Inf. Legal Nº 139-2000-SBA-OEAL",'0','L');
		$y+=10;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"SON:".Number::lit($items["neto"]).' Y '.round((($items["neto"]-((int)$items["neto"]))*100),0).'/100 ','0','L');
		$y+=20;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"Elaborado: ".$items["autor"]["nomb"]." ".$items["autor"]["appat"]." ".$items["autor"]["apmat"],'0','L');
		$this->SetFont('arial','B',9);
		$this->Line(110, $y, 195, $y);
		$this->setXY(110,$y);$this->MultiCell(85,5,"Recibí Conforme\n ".$items["trabajador"]["nomb"]." ".$items["trabajador"]["appat"]." ".$items["trabajador"]["apmat"]."\nDNI Nº ".$dni,'0','C');	
	}
	function Footer(){
  
	} 
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filter($items);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>