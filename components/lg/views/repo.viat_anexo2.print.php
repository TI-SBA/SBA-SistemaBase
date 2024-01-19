<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	function Header(){
		
	}		
	function Publicar($items){
		$this->SetFont('Arial','B',14);
		$this->SetXY(10,10);$this->MultiCell(190,5,"ANEXO Nº 2",'0','C');
		$this->SetFont('Arial','B',11);
		$this->SetXY(10,20);$this->MultiCell(190,5,"PLANILLA DE VIÁTICOS Nº ".$items["cod"],'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(190,5,date("d/m/Y")." - Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Logistica",'0','C');
		$y = 35;
		$this->SetFont('Arial','B',10);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"1. DOCUMENTO FUENTE",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(75,$y);$this->MultiCell(120,5,$items["fuente"],'0','L');
		$y=$this->GetY()+5;
		$this->SetFont('Arial','B',10);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"2. DEPENDENCIA",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(75,$y);$this->MultiCell(120,5,$items["dependencia"],'0','L');
		$y=$this->GetY()+5;
		$this->SetFont('Arial','B',10);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"3. NOMBRES Y APELLIDOS",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(75,$y);$this->MultiCell(120,5,$items["origen"]["nomb"]." ".$items["origen"]["appat"]." ".$items["origen"]["apmat"],'0','L');
		$y=$this->GetY()+5;
		$this->SetFont('Arial','B',10);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"4. CARGO",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(75,$y);$this->MultiCell(120,5,$items["origen"]["cargo"]["nomb"],'0','L');
		$y=$this->GetY()+5;
		$this->SetFont('Arial','B',10);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"5. LUGAR DE  DESTINO",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(75,$y);$this->MultiCell(120,5,$items["lugar"],'0','L');
		$y=$this->GetY()+5;
		$this->SetFont('Arial','B',10);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"6. MOTIVO DE LA COMISIÓN",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(75,$y);$this->MultiCell(120,5,$items["motivo"],'0','L');
		$y=$this->GetY()+5;
		$this->SetFont('Arial','B',10);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"7. PERIODO DE LA COMISIÓN",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(75,$y);$this->MultiCell(120,5,$items["periodo"],'0','L');
		$y=$this->GetY()+5;
		$this->SetFont('Arial','B',10);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"8. DÍAS DE COMISIÓN",'0','L');
		$this->SetFont('Arial','',10);
		$this->SetXY(75,$y);$this->MultiCell(120,5,$items["dias"],'0','L');
		$y=$this->GetY()+10;
		$this->SetXY(15,$y);$this->MultiCell(150,5,"CONCEPTO DEL GASTO",'1','C');
		$this->SetXY(165,$y);$this->MultiCell(30,5,"IMPORTE S/.",'1','C');
		$y+=5;
		$tot = 0;
		foreach($items["conceptos"] as $conc){
			$this->SetXY(15,$y);$this->MultiCell(150,5,$conc["descr"],'0','L');
			$this->SetXY(165,$y);$this->MultiCell(30,5,$conc["monto"],'0','R');
			$tot+=$conc["monto"];
			$y=$this->GetY();
		}
		$this->Line(15, $y, 195, $y);
		$this->SetXY(15,$y);$this->MultiCell(150,5,"TOTAL DEL GASTO",'1','L');
		$this->SetXY(165,$y);$this->MultiCell(30,5,number_format($tot,2),'1','R');
		$y+=10;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"SON:".Number::lit($tot).' Y '.round((($tot-((int)$tot))*100),0).'/100 ','0','L');
		$y+=10;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"Arequipa, ".strftime("%d de %B del %Y"),'0','L');
		$y+=20;
		$this->SetXY(15,$y);$this->MultiCell(90,5,"----------------------------------------------\nFirma del Comisionado",'0','C');
		$this->SetXY(105,$y);$this->MultiCell(90,5,"----------------------------------------------------------------\nFirma y Sello del Funcionario que autoriza la Comisión (Gerente General)",'0','C');
		$y+=25;
		$this->SetXY(15,$y);$this->MultiCell(60,5,"------------------------------------\nVºBº Jefe Of. Personal",'0','C');
		$this->SetXY(75,$y);$this->MultiCell(60,5,"------------------------------------\nVºBº Jefe Of. Logística",'0','C');
		$this->SetXY(135,$y);$this->MultiCell(60,5,"------------------------------------\nVºBº Gerente Administrativo",'0','C');
		$y+=25;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"------------------------------------\nVºBº Jefe Of. Planif y Pto.",'0','C');
	}
	function Footer()
	{
	} 
	 
}

$pdf=new repo('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>