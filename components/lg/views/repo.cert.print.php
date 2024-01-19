<?php
global $f;
$f->library('pdf');

class repo extends FPDF
{
	var $data;
	function Filtros($filtros){
		$this->data = $filtros;
	}
	function Header(){
		$estados = array(
			"P"=>"Pendiente",
			"A"=>"Aprobado",
			"X"=>"Anulado",
		);
		$meses = array("Todos","ENERO","FEBRERO","MARZO","ABRIL","MAYO","JUNIO","JULIO","AGOSTO","SETIEMBRE","OCTUBRE","NOVIEMBRE","DICIEMBRE");
		$x=0;
		$y=10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,5);$this->MultiCell(60,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');
		$this->SetXY(10,5);$this->MultiCell(277,5,date("d/m/Y H:i")." Página: ".$this->PageNo()."/{nb}",'0','R');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,15);$this->MultiCell(60,5,"Sistema SBPA - Integracion Planificacion y Presupuestos",'0','C');	
		$this->SetFont('Arial','B',10);
		$this->SetXY(10,15);$this->MultiCell(277,5,"Nro. ".$this->data["certificacion"]["cod"],'0','C');	
		//$this->SetTextColor($estados[$this->data["certificacion"]["estado"]]["color"]);
		if($this->data["certificacion"]["estado"]=="P"){
			$this->SetTextColor(34,139,34);
		}elseif($this->data["certificacion"]["estado"]=="X"){
			$this->SetTextColor(255,0,0);
		}else{
			$this->SetTextColor(204,204,204);
		}
		$this->SetXY(10,20);$this->MultiCell(277,5,$estados[$this->data["estado"]],'0','C');	
		$this->SetTextColor(0,0,0);
		$this->setXY(10,25);$this->MultiCell(277,5,"SOLICITUD DE CERTIFICACION DE CREDITO PRESUPUESTARIO - EJECUCION PRESUPUESTAL ".$meses[floatval(date('m',$this->data["certificacion"]["fecreg"]->sec))]." ".date('Y',$this->data["certificacion"]["fecreg"]->sec),'0','C');
		$this->SetFont('Arial','B',9);
		$this->SetXY(10,30);$this->MultiCell(277,5,"PERSONAL, BIENES, SERVICIOS, SUBVENCIONES, OTROS SERVICIOS Y ADQUISION DE ACTIVO NO FINANCIEROS \n (En Nuevos Soles)",'0','C');	
		
		$this->SetXY(30,160);$this->MultiCell(50,5,"JEFE DEL AREA SOLICITANTE FIRMA Y SELLO",'0','C');
		$this->SetXY(120,160);$this->MultiCell(60,5,"Vo. Bo. JEFE DE OFICINA PLANIFICACION Y PRESUPUESTO",'0','C');
		$this->SetXY(210,160);$this->MultiCell(60,5,"Vo. Bo. ORDENADOR DEL GASTO",'0','C');
		$this->SetFont('Arial','',7);
		$this->SetXY(10,180);$this->MultiCell(277,5,"1)La presente certificacion se otorga al amparo del articulo 13º de la Directiva Nº005-2010-EF/76.01 y solo confirma la existencia de los recursos en el presupuesto institucional en la especifica del gasto en el cual se solicita, siendo responsabilidad del solicitante los datos consignados en la presente, asi como su ejecucion del gasto",'0','L');
		$this->SetXY(10,190);$this->MultiCell(277,5,"2)No debe ser considerado como una autorizacion de contratacion, ni de pago según sea el caso. Valera por el cumplimiento de la normatividad presupuestaria de contrataciones y adquisisiones, de personal, de tesoreria, austeridad y racionalidad en el gasto publico y otras normas relacionadas que rigen para el año fiscal vigente",'0','L');	
	}		
	function Publicar($items){
		$y=55;		
		$y = 45;
		$this->SetFont('Arial','B',8);
		$this->SetXY(10,$y);$this->MultiCell(150,5,"DEPENDENCIA SOLICITANTE",'0','L');
		$this->SetFont('Arial','',8);
		$solicitante[] = array();
		if(isset($items['certificacion']['afectacion'])){
			foreach($items['certificacion']['afectacion'] as $afec){
				$solicitante[] = $afec['programa'];
			}
		}
		$solicitante_string = '--';
		if(count($solicitante)>1){
			$solicitante_string = 'Varios';
		}else{
			$solicitante_string = $solicitante[0]['nomb'];
		}
		$this->SetXY(80,$y);$this->MultiCell(210,5,$solicitante_string,'0','L');
		$y+=5;
		$this->SetFont('Arial','B',8);
		$this->SetXY(10,$y);$this->MultiCell(150,5,"ACTIVIDAD / OBRA / ACCION DE INVERSION",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(80,$y);$this->MultiCell(210,5,'--','0','L');
		$y+=5;
		$this->SetFont('Arial','B',8);
		$this->SetXY(10,$y);$this->MultiCell(150,5,"FUENTE DE FINANCIAMIENTO",'0','L');
		$this->SetFont('Arial','',8);
		$this->SetXY(80,$y);$this->MultiCell(210,5,$items["fuente"]["cod"]." ".$items["fuente"]["rubro"],'0','L');
		$y+=10;
		$this->SetFont('Arial','B',8);
		$this->setXY(10,$y);$this->MultiCell(15,5,"Nº",'1','C');
		$this->setXY(25,$y);$this->MultiCell(40,5,"CADENA DE GASTO",'1','C');
		$this->setXY(65,$y);$this->MultiCell(100,5,"DESCRIPCION",'1','C');
		$this->setXY(165,$y);$this->MultiCell(40,5,"CREDITO SOLICITADO",'1','C');
		$this->setXY(205,$y);$this->MultiCell(80,5,"JUSTIFICACION PAGO",'1','C');
		$y+=5;
		$this->SetFont('Arial','',8);		
		foreach($items['certificacion']["productos"] as $item){
			$str_descr = ceil(strlen($item["producto"]["nomb"])/55);
			$str_just = ceil(strlen($item["justificacion"])/40);
			if($str_descr>$str_just){
				$alto = $str_descr*5;
			}else{
				$alto = $str_just*5;
			}
			$this->Rect(10, $y, 15, $alto);$this->setXY(10,$y);$this->MultiCell(15,$alto,$item["item"],'0','L');
			$this->Rect(25, $y, 40, $alto);$this->setXY(25,$y);$this->MultiCell(40,$alto,$item["producto"]["clasif"]["cod"],'0','L');
			$this->Rect(65, $y, 100, $alto);$this->setXY(65,$y);$this->MultiCell(100,5,$item["cant"]." [".$item["producto"]["cod"]."] ".$item["producto"]["nomb"]." (".$item["producto"]["unidad"]["nomb"].")",'0','L');
			//$this->Rect(165, $y, 40, $alto);$this->setXY(165,$y);$this->MultiCell(40,$alto,$item["cred_solic"],'0','R');
			//$this->Rect(205, $y, 80, $alto);$this->setXY(205,$y);$this->MultiCell(80,5,$item["justificacion"],'0','L');
			$y+=$alto;
		}
	}
	function Footer()
	{
		
	} 
	 
}
$pdf=new repo('L','mm','A4');
$pdf->AliasNbPages();
$pdf->SetMargins(15,39.5,0); 
$pdf->SetTitle("reporte_certificacion_presupuestaria_".$items['certificacion']["cod"]."-".$items["certificacion"]["fecreg"]->sec);
$pdf->SetAutoPageBreak(false,0); 
$pdf->Open();
$pdf->Filtros($items);
$pdf->AddPage();
$pdf->Publicar($items);
$pdf->SetLeftMargin(25);
/*if($f->request->data["sendEmail"]){
	// email stuff (change data below)
	$to = "eflores@kunan.pe";
	$from = "sistema@sbpaarequipa.gob.pe"; 
	$subject = "Sistema SBPA - Reporte de Certificacion Presupuestal ".$items["cod"]."-".$items["periodo"]["ano"]; 
	$message = "<p>Ver Archivo Adjunto.</p>";
	
	// a random hash will be necessary to send mixed content
	$separator = md5(time());
	
	// carriage return type (we use a PHP end of line constant)
	$eol = PHP_EOL;
	
	// attachment name
	$filename = "test.pdf";
	
	// encode data (puts attachment in proper format)
	$pdfdoc = $pdf->Output("", "S");
	$attachment = chunk_split(base64_encode($pdfdoc));
	
	// main header
	$headers  = "From: ".$from.$eol;
	$headers .= "MIME-Version: 1.0".$eol; 
	$headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"";
	
	// no more headers after this, we start the body! //
	
	$body = "--".$separator.$eol;
	$body .= "Content-Transfer-Encoding: 7bit".$eol.$eol;
	$body .= "This is a MIME encoded message.".$eol;
	
	// message
	$body .= "--".$separator.$eol;
	$body .= "Content-Type: text/html; charset=\"iso-8859-1\"".$eol;
	$body .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
	$body .= $message.$eol;
	
	// attachment
	$body .= "--".$separator.$eol;
	$body .= "Content-Type: application/octet-stream; name=\"".$filename."\"".$eol; 
	$body .= "Content-Transfer-Encoding: base64".$eol;
	$body .= "Content-Disposition: attachment".$eol.$eol;
	$body .= $attachment.$eol;
	$body .= "--".$separator."--";
	
	// send message
	mail($to, $subject, $body, $headers) or die('Error');
}else{*/
	$pdf->Output();
//}
?>