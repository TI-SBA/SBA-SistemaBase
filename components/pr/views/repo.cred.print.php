<?php
global $f;
$f->library('pdf');

class presmodi extends FPDF
{
	var $periodo;
	var $num_credito;
	var $tipo;
	function Filter($filtros){
		$this->periodo = $filtros["periodo"];
		$this->num_credito = $filtros["num_credito"];
		$this->tipo = $filtros["tipo"];
	}
	function Header(){
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(190,5,"Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(5,15);$this->MultiCell(60,5,"Sistema SBPA - Módulo de Planificacion y Presupuestos",'0','C');
		$this->SetFont('arial','B',10);	
		$tipos = array("I"=>"INGRESOS","G"=>"GASTOS");
		$tipos_2 = array("I"=>"INGRESO","G"=>"GASTO");
		$y=15;
		$anexo = "A";
		if($this->tipo=="G"){
			$anexo = "B";
		}
		$nf = new NumberFormatter("es_ES", NumberFormatter::ORDINAL);
		$this->SetXY(15,$y);$this->MultiCell(180,5,"ANEXO ".$anexo." \n \n PRESUPUESTO AMPLIATORIO DE ".$tipos[$this->tipo]." AÑO FISCAL ".$this->periodo." \n CREDITO SUPLEMENTARIO Nº ".$this->num_credito." \n EN NUEVOS SOLES",'0','C');
		$this->SetFont('arial','B',8);	
		$y+=30;
		$this->SetXY(15,$y);$this->MultiCell(180,5,"ENTIDAD       SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA ",'0','L');
		$y+=5;
		$this->SetXY(15,$y);$this->MultiCell(40,10,"CODIGO",'1','C');
		$this->SetXY(55,$y);$this->MultiCell(110,10,"PARTIDAS DEL ".$tipos_2[$this->tipo],'1','C');
		$this->SetXY(165,$y);$this->MultiCell(30,10,"MONTO",'1','C');
	}		
	function Publicar($items){
		//$this->SetFont('arial','',8);
		$y=60;
		$this->Line(15, 60, 15, 275);
		$this->Line(55, 60, 55, 275);
		$this->Line(165, 60, 165, 275);
		$this->Line(195, 60, 195, 275);
		$this->Line(15, 275, 195, 275);
		$tot = 0;
		$tot_acti = 0;
		$tot_proy = 0;
		foreach($items as $fuentes){
			$this->SetFont('arial','B',8);
			$this->SetXY(15,$y);$this->MultiCell(40,5,$fuentes["fuente"]["cod"],'1','R');
			$this->SetXY(55,$y);$this->MultiCell(110,5,$fuentes["fuente"]["nomb"],'1','L');	
			$this->SetXY(165,$y);$this->MultiCell(30,5,number_format($fuentes["total"],2),'1','R');
			$y+=5;
			foreach($fuentes["items"] as $orga){
				$this->SetFont('arial','B',8);
				$this->SetXY(15,$y);$this->MultiCell(40,5,$orga["orga"]["actividad"]["cod"],'1','R');
				$this->SetXY(55,$y);$this->MultiCell(110,5,$orga["orga"]["actividad"]["nomb"],'1','L');
				$this->SetXY(165,$y);$this->MultiCell(30,5,number_format($orga["total"],2),'1','R');
				$y+=5;
				//$this->Rect(15, $y, 40, count($orga["items"])*5);
				//$this->Rect(55, $y, 110, count($orga["items"])*5);
				//$this->Rect(165, $y, 30, count($orga["items"])*5);
				foreach($orga["items"] as $clasi){
					if($y>270){
						$this->AddPage();
						$this->Line(15, 60, 15, 275);
						$this->Line(55, 60, 55, 275);
						$this->Line(165, 60, 165, 275);
						$this->Line(195, 60, 195, 275);
						$this->Line(15, 275, 195, 275);
						$y=60;
					}
					if(strlen($clasi["cod"])==3||strlen($clasi["cod"])==5||strlen($clasi["cod"])==7){
						$this->SetFont('arial','B',8);
					}else{
						$this->SetFont('arial','',8);
					}
					
					$this->SetXY(15,$y);$this->MultiCell(40,5,$clasi["cod"],'0','L');
					$this->SetXY(55,$y);$this->MultiCell(110,5,substr($clasi["nomb"],0,60),'0','L');
					$this->SetXY(165,$y);$this->MultiCell(30,5,number_format($clasi["importes"],2),'0','R');
					$y+=5;
					if(strlen($clasi["cod"])==3){
						$tot_acti+=$clasi["importes"];
					}
				}
				if(count($orga["proyectos"])>0){
					foreach($orga["proyectos"] as $meta){
						$this->SetFont('arial','BU',8);
						$this->SetXY(55,$y);$this->MultiCell(110,5,$meta["meta"]["cod"]." ".$meta["meta"]["nomb"],'0','L');
						$this->SetXY(165,$y);$this->MultiCell(30,5,number_format($meta["total"],2),'0','R');
						$y=$this->GetY();
						foreach($meta["items"] as $clasi){
							if($y>270){
								$this->AddPage();
								$this->Line(15, 60, 15, 275);
								$this->Line(55, 60, 55, 275);
								$this->Line(165, 60, 165, 275);
								$this->Line(195, 60, 195, 275);
								$this->Line(15, 275, 195, 275);
								$y=60;
							}
							if(strlen($clasi["cod"])==3||strlen($clasi["cod"])==5||strlen($clasi["cod"])==7){
								$this->SetFont('arial','B',8);
							}else{
								$this->SetFont('arial','',8);
							}
							$this->SetXY(15,$y);$this->MultiCell(40,5,$clasi["cod"],'0','L');
							$this->SetXY(55,$y);$this->MultiCell(110,5,substr($clasi["nomb"],0,60),'0','L');
							$this->SetXY(165,$y);$this->MultiCell(30,5,number_format($clasi["importes"],2),'0','R');
							$y+=5;
							if(strlen($clasi["cod"])==3){
								$tot_proy+=$clasi["importes"];
							}
						}
					}
				}
			}
			/*$tot+=$tot_fuen;
			$y+=5;*/
		}
		$y=275;
		$this->SetFont('arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(150,4,"TOTAL ACTIVIDADES",'1','L');
		$this->SetXY(165,$y);$this->MultiCell(30,4,number_format($tot_acti,2),'1','R');
		$y+=4;
		$this->SetXY(15,$y);$this->MultiCell(150,4,"TOTAL PROYECTOS",'1','L');
		$this->SetXY(165,$y);$this->MultiCell(30,4,number_format($tot_proy,2),'1','R');
		$y+=4;
		$this->SetXY(15,$y);$this->MultiCell(150,4,"TOTAL",'1','L');
		$this->SetXY(165,$y);$this->MultiCell(30,4,number_format($tot_proy+$tot_acti,2),'1','R');
		//$this->Rect(161, $y, 34, $alto);$this->SetXY(161,$y);$this->MultiCell(34,5,number_format($suma,2,".", " "),'0','R');
	}
	function Footer()
	{
    	
	} 
	 
}

$pdf=new presmodi('P','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte");
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filter($filtros);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
//header('Content-Type: application/pdf; charset=utf-8');
$pdf->Output();

?>