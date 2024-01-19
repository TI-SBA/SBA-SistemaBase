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
		$title = "";
		if(isset($this->filtros["vacaciones"]["bonificacion"])){
			$title = "LIQUIDACION BENEFICIOS SOCIALES Y COMPENSACION VACACIONAL";
		}else{
			$title = "LIQUIDACION COMPENSACION VACACIONAL";
		}
		$this->SetXY(10,25);$this->MultiCell(180,5,$title,'0','C');
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
		$this->SetXY(75,$y);$this->MultiCell(120,5,"30 AÑOS",'0','L');
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
		
		$cod=276;
		if($items["vacaciones"]["trabajador"]["roles"]["trabajador"]["contrato"]["cod"]== $cod){
			
		$this->SetFont('arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(60,5,"NOTA",'0','L');
			$this->SetFont('arial','',9);
		$this->SetXY(75,$y);$this->MultiCell(110,5,"De acuerdo a los dispuesto en el Decreto Supremo Nº 065-2011-PC, Articulo 8 numeral 8.6, modificatorias al D.L. Nº 1057 según Ley 29849 al servidor le corresponde:",'0','L');
		$y=$this->GetY()+5;
		$this->Line(15, $y, 195, $y);
		}	
		else{
			if(isset($items["vacaciones"]["bonificacion"])){
			$this->Line(15, $y, 195, $y);
			$y+=5;
			$this->SetFont('arial','BU',9);
			$this->SetXY(15,$y);$this->MultiCell(180,5,"BENEFICIOS SOCIALES:",'0','L');
			$y+=5;		
			$this->SetFont('arial','',9);
			$this->SetXY(15,$y);$this->MultiCell(180,5,"De conformidad con lo dispuesto en el D.Leg. 276 y su modificatoria D.L. 25224, al recurrente le corresponde percibir el 50% de su remuneración principal por cada año de servicios prestados en la Sociedad de Beneficencia Pública de Arequipa, por periodo mayor de 06 meses",'0','L');
			$y=$this->GetY()+5;
			$this->SetXY(15,$y);$this->MultiCell(180,5,"Remuneración Principal (Inafecto)                   ".$items["vacaciones"]["bonificacion"]["remuneracion_principal"]." / 2 X ".$items["vacaciones"]["bonificaciones"]["total_anos"]." años",'0','L');
			$this->SetXY(15,$y);$this->MultiCell(180,5,number_format($items["vacaciones"]["bonificacion"]["pagar"],2),'0','R');
			$y+=5;
		}else{
			$y+=5;
		}
		
	}
		$this->SetFont('arial','BU',9);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"VACACIONES TRUNCAS:",'0','L');
		$y+=5;
		
		$this->SetFont('arial','',9);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"De conformidad a lo dispuesto en el Art. 104 del D.Leg. Nº 276 y D.S.005-90-PCM e Inf.Nº 079-2006-SBA-OEAL al ex servidor le corresponde percibir de acuerdo al siguiente detalle:",'0','L');
			$y=$this->GetY()+5;

		

		
		
		
				
		$this->SetXY(15,$y);$this->MultiCell(30,5,"Periodo Vacacional",'0','L');
		$this->SetXY(45,$y);$this->MultiCell(30,5,$items["vacaciones"]["periodo"],'0','R');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(30,5,"Meses",'0','L');
		$this->SetXY(45,$y);$this->MultiCell(30,5,$items["vacaciones"]["meses"],'0','R');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(30,5,"Dias",'0','L');
		$this->SetXY(45,$y);$this->MultiCell(30,5,$items["vacaciones"]["dias"],'0','R');
		$y+=5;
		$this->SetXY(55,$y);$this->MultiCell(30,5,"(REFERENCIA)",'0','L');
		$y=$this->GetY()+5;
		$this->SetXY(15,$y);$this->MultiCell(40,5,"Remuner. Total Mensual",'0','L');
		$this->SetXY(50,$y);$this->MultiCell(145,5,number_format($items["vacaciones"]["ultima_remuneracion"],2)." X ".$items["vacaciones"]["meses"]."(meses) / 12                        ".$items["vacaciones"]["total_mes"],'0','R');
		$y+=5;
		$this->SetXY(50,$y);$this->MultiCell(145,5,number_format($items["vacaciones"]["ultima_remuneracion"],2)." X ".$items["vacaciones"]["dias"]."(dias) / 360                        ".$items["vacaciones"]["total_dia"],'0','R');
		$y+=5;
		$this->SetXY(135,$y);$this->MultiCell(30,5,"TOTAL S/.",'0','C');
		$this->SetXY(165,$y);$this->MultiCell(30,5,number_format($items["vacaciones"]["total"],2),'0','R');
		$y+=5;
		$this->SetFont('arial','BU',9);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"RETENCIONES",'0','L');
		$y+=5;
		$this->SetFont('arial','',9);
		foreach($items["vacaciones"]["descuentos"] as $desc){
			$this->SetXY(15,$y);$this->MultiCell(60,5,$desc["descr"],'0','L');
			$this->SetXY(75,$y);$this->MultiCell(30,5,number_format($desc["val"],2),'0','R');
			$this->SetXY(105,$y);$this->MultiCell(30,5,number_format($desc["val"]*$items["vacaciones"]["total"]/100,2),'0','R');
			$y+=5;
		}
		$this->SetXY(135,$y);$this->MultiCell(30,5,"TOTAL DSCTOS",'0','C');
		$this->SetXY(165,$y);$this->MultiCell(30,5,"(-) ".number_format($items["vacaciones"]["total_descuentos"],2),'0','R');
		$y+=5;
		$this->SetXY(135,$y);$this->MultiCell(30,5,"TOTAL A PAGAR",'0','C');
		$this->SetXY(165,$y);$this->MultiCell(30,5,number_format($items["vacaciones"]["total_pagar"],2),'0','R');
		$y+=5;
		$this->SetFont('arial','BU',9);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"APORTACIONES",'0','L');
		$y+=5;
		$this->SetFont('arial','',9);
		foreach($items["vacaciones"]["aportaciones"] as $desc){
			$this->SetXY(15,$y);$this->MultiCell(60,5,$desc["descr"],'0','L');
			$this->SetXY(75,$y);$this->MultiCell(30,5,number_format($desc["val"],2),'0','R');
			$this->SetXY(105,$y);$this->MultiCell(30,5,number_format($desc["val"]*$items["vacaciones"]["total"]/100,2),'0','R');
			$y+=5;
		}
		$y+=10;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"SON:".Number::lit($items["vacaciones"]["total_pagar"]).' Y '.round((($items["vacaciones"]["total_pagar"]-((int)$items["vacaciones"]["total_pagar"]))*100),0).'/100 ','0','L');
		$y+=10;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"Declaro estar conforme con la presente Liquidación la misma que ha sido elaborada de acuerdo a Ley, por lo tanto no tengo reclamo alguno que formular a la Sociedad de Beneficencia Pública de Arequipa, al pie de la cual firmo.",'0','L');
		$y=$this->GetY()+15;
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