<?php
global $f;
$f->library('pdf');
class tras extends FPDF
{
	function Publicar($oper,$espa){
		$prop = $espa["propietario"]["nomb"];
		if($espa["propietario"]["tipo_enti"]=="P"){
			$prop .= " ".$espa["propietario"]["appat"]." ".$espa["propietario"]["apmat"];
		}	
		$y = 25;	
		//$this->Rect(146, $y, 10, 10);$this->SetXY(146,$y);$this->MultiCell(15,10,$item["espacio"]["fila"],'0','C');
		$this->SetFont('Arial','',8);
		$this->SetXY(10,5);$this->MultiCell(50,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(150,5);$this->MultiCell(50,5,"CEMENTERIO GENERAL DE LA APACHETA",'0','C');
		$this->SetFont('Arial','BU',12);
		$this->SetXY(0,$y);$this->MultiCell(0,5,"FICHA INVENTARIO DE MAUSOLEOS",'0','C');
		$y+=10;
		$this->SetFont('Arial','B',10);
		$this->SetXY(10,$y);$this->MultiCell(0,5,"I. NOMBRE DEL MAUSOLEO",'0','L');
		$y+=10;
		$this->SetFont('Arial','',9);
		$this->SetXY(10,$y);$this->MultiCell(100,5,$espa["mausoleo"]["denominacion"],'1','L');
		$y+=10;
		$this->SetFont('Arial','B',10);
		$this->SetXY(10,$y);$this->MultiCell(0,5,"II. MEDIDAS DEL MAUSOLEO POR CONCESION",'0','L');
		$y+=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(20,5,"LARGO (m)",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(35,$y);$this->MultiCell(20,5,$oper["construccion"]["largo"],'1','L');
		$this->SetFont('Arial','B',9);
		$this->SetXY(65,$y);$this->MultiCell(35,5,"ANCHO (m)",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(100,$y);$this->MultiCell(20,5,$oper["construccion"]["ancho"],'1','L');
		$this->SetFont('Arial','B',9);
		$this->SetXY(130,$y);$this->MultiCell(30,5,"AREA (m2)",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(160,$y);$this->MultiCell(20,5,$oper["construccion"]["largo"]*$oper["construccion"]["ancho"],'1','L');
		$y+=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(20,5,"h1 (m)",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(35,$y);$this->MultiCell(20,5,$oper["construccion"]["altura1"],'1','L');
		$this->SetFont('Arial','B',9);
		$this->SetXY(65,$y);$this->MultiCell(35,5,"h2 (m)",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(100,$y);$this->MultiCell(20,5,$oper["construccion"]["altura2"],'1','L');
		$y+=10;
		$this->SetFont('Arial','B',10);
		$this->SetXY(10,$y);$this->MultiCell(0,5,"III. MEDIDAS DEL MAUSOLEO EN LA EJECUCION DE LA CONSTRUCCION",'0','L');
		$y+=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(20,5,"LARGO (m)",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(35,$y);$this->MultiCell(20,5,$oper["construccion"]["finalizacion"]["largo"],'1','L');
		$this->SetFont('Arial','B',9);
		$this->SetXY(65,$y);$this->MultiCell(35,5,"ANCHO (m)",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(100,$y);$this->MultiCell(20,5,$oper["construccion"]["finalizacion"]["ancho"],'1','L');
		$this->SetFont('Arial','B',9);
		$this->SetXY(130,$y);$this->MultiCell(30,5,"AREA (m2)",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(160,$y);$this->MultiCell(20,5,$oper["construccion"]["finalizacion"]["largo"]*$oper["construccion"]["finalizacion"]["ancho"],'1','L');
		$y+=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(20,5,"h1 (m)",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(35,$y);$this->MultiCell(20,5,$oper["construccion"]["finalizacion"]["altura1"],'1','L');
		$this->SetFont('Arial','B',9);
		$this->SetXY(65,$y);$this->MultiCell(35,5,"h2 (m)",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(100,$y);$this->MultiCell(20,5,$oper["construccion"]["finalizacion"]["altura2"],'1','L');
		$y+=10;
		$this->SetFont('Arial','B',10);
		$this->SetXY(10,$y);$this->MultiCell(0,5,"IV. CAPACIDAD DEL MAUSOLEO",'0','L');
		$y+=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(45,5,"CAPACIDAD CONSTRUIDA",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(60,$y);$this->MultiCell(20,5,$oper["construccion"]["fcapacidad"],'1','L');
		$this->SetFont('Arial','B',9);
		$this->SetXY(90,$y);$this->MultiCell(25,5,"OCUPADOS",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(115,$y);$this->MultiCell(20,5,count($espa["ocupantes"]),'1','L');
		$y+=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(15,$y);$this->MultiCell(100,5,"PERSONAS INHUMADAS EN EL MAUSOLEO",'0','L');
		if(isset($espa["ocupantes"])){
			foreach($espa["ocupantes"] as $i=>$ocu){
				if($i<10){
					$this->SetXY(15,$y);$this->MultiCell(90,5,$iden = $ocu["nomb"]." ".$ocu["appat"]." ".$ocu["apmat"],'0','L');
				}							
			}
			foreach($espa["ocupantes"] as $i=>$ocu){
				if($i>10&&$i<21){
					$this->SetXY(110,$y);$this->MultiCell(90,5,$iden = $ocu["nomb"]." ".$ocu["appat"]." ".$ocu["apmat"],'0','L');
				}							
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
$pdf->AddPage();
$pdf->Publicar($oper,$espa);
$pdf->SetLeftMargin(25);
$pdf->Output();

?>