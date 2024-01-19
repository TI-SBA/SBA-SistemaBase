<?php
global $f;
$f->library('pdf');
setlocale(LC_ALL,"esp");
class repo extends FPDF
{
	var $concepto;
	var $mes;
	var $ano;
	var $data;
	function Filter($filtros,$items){
		$this->concepto = $filtros["concepto"];
		$this->mes = $filtros["mes"];
		$this->ano = $filtros["ano"];
		$this->data = $items;
	}
	function Header(){
		$meses = array("Todos","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Setiembre","Octubre","Noviembre","Diciembre");
		//$this->Image(IndexPath.DS.'templates/pr/presupuestos.gif',15,15,180,267);	
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(180,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','B',11);
		$title = "";
		$title .="POR FALLECIMIENTO";
		if(!isset($this->data["sepelios"]["difunto"])){
			$title .= " DEL TRABAJADOR";
		}else{
			$title .= " DE UN FAMILIAR";
		}
		
		if($this->data["sepelios"]["sepelio"]!=false){
			$title .= " Y GASTOS DE SEPELIO";
		}
		$this->SetXY(10,20);$this->MultiCell(190,5,"LIQUIDACION DE SUBSIDIO ".$this->data["contrato"]["nomb"],'0','C');
		$this->SetXY(10,25);$this->MultiCell(190,5,$title,'0','C');
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
		$this->SetXY(75,$y);$this->MultiCell(120,5,$items["sepelios"]["beneficiario"]["nomb"],'0','L');
		$y+=5;
		$this->SetFont('arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(60,5,"DNI",'0','L');
		$this->SetFont('arial','',9);
		$this->SetXY(75,$y);$this->MultiCell(120,5,$items["sepelios"]["beneficiario"]["dni"],'0','L');
		$y+=5;
		$this->SetFont('arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(60,5,"PARENTESCO",'0','L');
		$this->SetFont('arial','',9);
		$this->SetXY(75,$y);$this->MultiCell(120,5,$items["sepelios"]["beneficiario"]["parentesco"],'0','L');
		$y+=10;
		$this->SetFont('arial','BU',10);
		$this->SetXY(15,$y);$this->MultiCell(60,5,"DATOS DEL DIFUNTO",'0','L');
		$y+=5;		
		if(isset($items["sepelios"]["difunto"])){
			$this->SetFont('arial','B',9);
			$this->SetXY(15,$y);$this->MultiCell(60,5,"NOMBRES Y APELLIDOS",'0','L');
			$this->SetFont('arial','',9);
			$this->SetXY(75,$y);$this->MultiCell(120,5,$items["sepelios"]["difunto"]["nomb"],'0','L');
			$y+=5;
			$this->SetFont('arial','B',9);
			$this->SetXY(15,$y);$this->MultiCell(60,5,"DNI",'0','L');
			$this->SetFont('arial','',9);
			$this->SetXY(75,$y);$this->MultiCell(120,5,$items["sepelios"]["difunto"]["dni"],'0','L');
			$y+=5;
			$this->SetFont('arial','B',9);
			$this->SetXY(15,$y);$this->MultiCell(60,5,"FECHA DE FALLECIMIENTO",'0','L');
			$this->SetFont('arial','',9);
			$this->SetXY(75,$y);$this->MultiCell(120,5,$items["sepelios"]["fecfall"],'0','L');
			$y+=5;
		}else{
			$trab = $items["trabajador"]["nomb"]." ".$items["trabajador"]["appat"]." ".$items["trabajador"]["apmat"];
			$this->SetFont('arial','B',9);
			$this->SetXY(15,$y);$this->MultiCell(60,5,"NOMBRES Y APELLIDOS",'0','L');
			$this->SetFont('arial','',9);
			$this->SetXY(75,$y);$this->MultiCell(120,5,$trab,'0','L');
			$y+=5;
			$this->SetFont('arial','B',9);
			$this->SetXY(15,$y);$this->MultiCell(60,5,"DNI",'0','L');
			$y+=5;
			$this->SetFont('arial','B',9);
			$this->SetXY(15,$y);$this->MultiCell(60,5,"NIVEL REMUNERATIVO",'0','L');
			$y+=5;
			$this->SetFont('arial','B',9);
			$this->SetXY(15,$y);$this->MultiCell(60,5,"FECHA DE FALLECIMIENTO",'0','L');
			$y+=5;
		}
		$this->Line(15, $y, 195, $y);
		$y+=5;
		$this->SetFont('arial','BU',9);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"1.- LIQUIDACION:",'0','L');
		$y+=5;
		$this->SetFont('arial','',9);
		$num_subs_fall = "03";
		$num_subs_fall_str = "Tres";
		if(isset($items["sepelios"]["difunto"])){
			 $num_subs_fall = "02";
			 $num_subs_fall_str = "Dos";
		}
		$num_subs_sepe = "02";
		$num_subs_sepe_str = "Dos";
		if($items["sepelios"]["sepelio"]==false){
			$num_subs_sepe = "0";
			$num_subs_sepe_str = "Cero";
		}
		$this->SetXY(15,$y);$this->MultiCell(180,5,"De confomidad con el D. Leg 276 y su reglamento D.S. 005-90-PCM Art. 144 y 155 a el(la) Sr(Sra) ".$items["sepelios"]["beneficiario"]["nomb"]." le corresponde percibir ".$num_subs_fall_str." (".$num_subs_fall.") Remuneraciones Totales por Subsidio por fallecimiento y ".$num_subs_sepe_str." (".$num_subs_sepe.") Remuneraciones Totales por gastos de Sepelio y Luto, por el deceso de su ".$items["sepelios"]["beneficiario"]["parentesco"],'0','L');
		$y = $this->GetY();
		$y+=5;
		$this->SetFont('arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"REMUNERACION TOTAL                      S/.          ".$items["sepelios"]["remuneracion"],'0','L');
		$y+=10;
		$this->SetFont('arial','',9);
		$this->SetXY(15,$y);$this->MultiCell(180,5,$num_subs_fall_str." remuneraciones mensuales totales por subsidio por fallecimiento",'0','L');
		$this->SetXY(15,$y);$this->MultiCell(180,5,$items["sepelios"]["pago_fall"],'0','R');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,$num_subs_sepe_str." remuneraciones mensuales totales por subsidio por gastos de sepelio",'0','L');
		$this->SetXY(15,$y);$this->MultiCell(180,5,$items["sepelios"]["pago_sepe"],'0','R');
		$y+=5;
		$this->Line(150, $y, 195, $y);
		$this->SetFont('arial','B',10);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"TOTAL        S/.    ".$items["neto"],'0','R');
		$y+=10;
		$this->SetFont('arial','',9);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"NOTA.- No afecto a leyes sociales según D.S. 179-91-PCM e Inf. Legal Nº 139-2000-SBA-OEAL
		*Según CI 73-2010-SBA-JC las dos asignaciones por fallecimiento de familiar directo están
		afectos al Dscto. Impuesto de Renta de 5ta Categoría, por ser de libre disponibilidad;por lo que se procederá a considerar en el calculo respectivo",'0','L');
		$y=$this->GetY()+10;
		$this->SetFont('arial','BU',9);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"SON:".Number::lit($items["neto"]).' Y '.round((($items["neto"]-((int)$items["neto"]))*100),0).'/100 ','0','L');
		$y=$this->GetY()+10;
		$this->SetFont('arial','',9);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"Declaro estar Conforme con la presente Liquidación, la misma que ha sido elaborada de acuerdo a Ley, por lo tanto no tengo reclamo alguno que formular a la Sociedad de Beneficencia Pública de Arequipa, al pie del presente firmo dando mi conformidad",'0','L');
		$y=$this->GetY()+5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"Arequipa, ".strftime("%d de %B del %Y"),'0','R');
		$y=$this->GetY()+5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"Elaborado: ".$items["autor"]["nomb"]." ".$items["autor"]["appat"]." ".$items["autor"]["apmat"],'0','L');
		$y=$this->GetY()+5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,$items["sepelios"]["beneficiario"]["nomb"]."\nD.N.I.    ".$items["sepelios"]["beneficiario"]["dni"],'0','C');
		$y=$this->GetY()+5;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"Aprobado",'0','L');
		
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
$pdf->Filter($filtros,$items);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>