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
		$this->SetXY(5,$y);$this->MultiCell(50,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','C');	
		$this->SetFont('Arial','B',16);
		$this->SetXY(10,$y);$this->MultiCell(150,5,"ORDEN DE SERVICIO",'0','R');
		$y=$y+10;
		$this->SetFont('Arial','B',9);
		$this->SetXY(140,$y);$this->MultiCell(20,5,"Nº",'1','C');
		$this->SetXY(160,$y);$this->MultiCell(15,5,"DÍA",'1','C');
		$this->SetXY(175,$y);$this->MultiCell(15,5,"MES",'1','C');
		$this->SetXY(190,$y);$this->MultiCell(15,5,"AÑO",'1','C');
		$y=$y+5;
		$this->SetXY(90,$y);$this->MultiCell(50,8,$this->nro,'0','C');
		$this->SetXY(140,$y);$this->MultiCell(20,8,"",'1','C');
		$this->SetXY(160,$y);$this->MultiCell(15,8,Date::format($this->fecreg->sec, 'd'),'1','C');
		$this->SetXY(175,$y);$this->MultiCell(15,8,Date::format($this->fecreg->sec, 'm'),'1','C');
		$this->SetXY(190,$y);$this->MultiCell(15,8,Date::format($this->fecreg->sec, 'Y'),'1','C');
		$y=$y+5;
	}		
	function Publicar($items){
		$monedas = array(
			"S"=>array("simb"=>"S/.","nomb"=>"NUEVO SOL","plu"=>"NUEVOS SOLES"),
			"D"=>array("simb"=>"USSD $.","nomb"=>"DOLAR","plu"=>"DOLARES")
		);
		$x=5;
		$y=35;
		$y_ini = $y;
		$page_b = 275;
		$this->SetFont('Arial','',9);
		$proveedor = $items["proveedor"]["nomb"];
		if($items["proveedor"]["tipo_enti"]=="P")$proveedor=$items["proveedor"]["nomb"]." ".$items["proveedor"]["appat"]." ".$items["proveedor"]["apmat"];
		$ruc = "--";
		foreach($items["proveedor"]["docident"] as $doc){
			if($doc["tipo"]=="RUC")$ruc = $doc["num"];
		}
		$this->Rect(5, $y, 200, 25);
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(200,5,"Señor(es):",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(22,$y);$this->MultiCell(200,5,"".strtoupper($proveedor)."" ,'0','L');
		$this->SetFont('Arial','B',9);
		$this->SetXY(100,$y);$this->MultiCell(200,5,"RUC: ",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(110,$y);$this->MultiCell(200,5,$ruc,'0','L');
		$y=$y+5;
		$this->SetFont('Arial','',9);
		$this->SetXY(5,$y);$this->MultiCell(175,5,"Sirvanse aceptar esta orden, cargando su valor en cuenta de la Sociedad de Beneficencia Pública de Arequipa, acompañando el original de esta orden a su factura",'0','L');
		$y=$y+5;
		
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y+5);$this->MultiCell(175,5,"Dirección: ",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(22,$y+5);$this->MultiCell(175,5,"Av. Piérola 201 - Arequipa",'0','L');
		$this->SetFont('Arial','B',9);
		$this->SetXY(174,$y+5);$this->MultiCell(175,5,"RUC: ",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(183,$y+5);$this->MultiCell(175,5,"20120958136",'0','L');
		$y=$this->GetY();
		$this->SetFont('Arial','B',11);
		$this->SetXY(5,$y);$this->MultiCell(40,5,"CÓDIGO",'1','C');
		$this->SetXY(45,$y);$this->MultiCell(120,5,"DETALLE",'1','C');
		$this->SetXY(165,$y);$this->MultiCell(40,5,"TOTAL",'1','C');
		$y=$y+5;
		$this->SetFont('Arial','',10);
		foreach($items["orden_servicio"]["productos"] as $item){
			$alto = ceil(strlen($item["servicio"]["nomb"])/100)*5;
			$this->Rect(5, $y, 40, $alto);$this->SetXY(5,$y);$this->MultiCell(40,5,$item["servicio"]["clasif"]["cod"],'0','L');
			$this->Rect(45, $y, 120, $alto);$this->SetXY(45,$y);$this->MultiCell(120,5,$item["servicio"]["nomb"],'0','L');
			$this->Rect(165, $y, 40, $alto);$this->SetXY(165,$y);$this->MultiCell(40,5,"S/. ".number_format($item["subtotal"],2,".", ","),'0','R');
			$y=$y+$alto;
		}
		$this->Rect(5, $y, 40, 5);
		$this->Rect(45, $y, 120, 5);$this->SetXY(45,$y);$this->MultiCell(120,5,"TOTAL",'0','R');
		$this->Rect(165, $y, 40, 5);$this->SetXY(165,$y);$this->MultiCell(40,5,"S/. ".number_format($items["orden_servicio"]["precio_total"],2,".", ","),'0','R');
		$y+=5;
		$this->SetXY(5,$y);$this->MultiCell(200,5,"OBSERVACIONES.- ".$items["orden_servicio_observ"],'0','L');
		$y+=5;
		$this->SetXY(5,$y);$this->MultiCell(200,5,"FUENTE DE FINANCIAMIENTO.- ".$items["fuente"]["cod"]." ".$items["fuente"]["rubro"],'0','L');
		$y+=5;
		$this->SetXY(5,$y);$this->MultiCell(40,5,"PROGRAMA:",'0','L');
		$y = 220;
		$this->SetFont('Arial','',9);
		$this->SetXY(5,$y);$this->MultiCell(100,5,"ORDENACION DEL SERVICIO",'1','C');
		$this->SetXY(105,$y);$this->MultiCell(50,5,"Afectación Presupuestal",'1','C');
		$this->SetXY(155,$y);$this->MultiCell(50,5,"Distribución Contable",'1','C');
		$y=$y+5;
		$this->SetXY(5,$y);$this->MultiCell(50,5,"\n\n\n\n",'1','C');//\n\n__________________________\nJefe de Adquisiciones
		$this->SetXY(7,$y+9);$this->MultiCell(5,5,"1",'1','C');
		$this->SetXY(55,$y);$this->MultiCell(50,5,"\n\n__________________________\nJefe de Abastecimiento",'1','C');//\n\n__________________________\nJefe de Abastecimiento y Servicio
		$this->SetXY(57,$y+9);$this->MultiCell(5,5,"2",'1','C');
		$this->SetXY(105,$y);$this->MultiCell(50,10,"Programa\nSub Progra",'1','L');
		
		if(count($items["orden_servicio"]["afectacion"])==1){
			$this->SetXY(125,$y);$this->MultiCell(25,10,"A".$items["orden_servicio"]["afectacion"][0]["programa"]["actividad"]["cod"]."\nC".$items["orden_servicio"]["afectacion"][0]["programa"]["componente"]["cod"],'0','L');
			
			$this->SetXY(105,$y+4);$this->MultiCell(35,10,$items["orden_servicio"]["afectacion"][0]["programa"]["nomb"],'0','L');
		}else{
			$this->SetXY(125,$y);$this->MultiCell(25,10,"--\n--",'0','L');
		}
		/*if(count($items["afectacion"])==1){
			$this->SetXY(45,$y);$this->MultiCell(150,10,"ACT. ".$items["afectacion"][0]["organizacion"]["actividad"]["cod"]." C. ".$items["afectacion"][0]["organizacion"]["componente"]["cod"],'0','L');
			$y=$y+5;
			$this->SetXY(45,$y);$this->MultiCell(150,5,$items["afectacion"][0]["organizacion"]["nomb"],'0','L');
		}else{
			$this->SetXY(45,$y);$this->MultiCell(150,10,"-----",'0','L');
		}*/
		
		
		
		//$this->SetXY(102,$y+9);$this->MultiCell(5,5,"2",'1','C');
		$this->SetXY(155,$y);$this->MultiCell(50,5,"CUENTAS POR PAGAR\n\n\n__________________________",'1','C');
		$this->SetXY(157,$y+10);$this->MultiCell(50,5,"S/. ".number_format($items["orden_servicio"]["precio_total"],2,".", ","),'0','C');
		$y=$y+20;
		$this->Rect(5, $y, 140, 25);
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(20,10,"NOTA:",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(15,$y+2.5);$this->MultiCell(120,5,"Cada orden de servicio se debe facturar por separado en original y dos (2) copias y remitidas a la Dirección de Presupuesto y Contabilidad.
		Nos Reservamos el derecho de devolver la mercaderia que no está de acuerdo con nuestras especificaciones",'0','L');
		$this->Rect(145, $y, 60, 25);
		/*
		$this->SetXY(105,$y);$this->MultiCell(100,5,"RECIBI CONFORME:",'0','L');
		$this->SetXY(150,$y+5);$this->MultiCell(50,5,"\n\n__________________________\nJefe de Almacén",'0','C');
		$this->SetXY(152,$y+14);$this->MultiCell(5,5,"3",'1','C');
		*/
		$this->SetXY(160,$y+10);$this->MultiCell(15,8,"DIA",'1','C');
		$this->SetXY(175,$y+10);$this->MultiCell(15,8,"MES",'1','C');
		$this->SetXY(190,$y+10);$this->MultiCell(15,8,"AÑO",'1','C');
		$this->SetXY(160,$y+18);$this->MultiCell(15,7,"",'1','C');
		$this->SetXY(175,$y+18);$this->MultiCell(15,7,"",'1','C');
		$this->SetXY(190,$y+18);$this->MultiCell(15,7,"",'1','C');
		/** Afectaciones */
		$num_afec = count($items["orden_servicio"]["afectacion"]);
		if($num_afec>1){
			$this->AddPage();
			$y=$y_ini;
			$this->SetFont('Arial','B',10);
			$this->SetXY(5,$y);$this->MultiCell(195,5,"AFECTACION PRESUPUESTAL",'0','C');
			$y=$y+10;
			foreach($items["afectacion"] as $afec){
				$monto = 0;
				foreach($afec["gasto"] as $gasto){
					$monto=$monto+$gasto["monto"];
				}	
				$this->SetFont('Arial','',9);
				$this->SetXY(35,$y);$this->MultiCell(80,5,"ACT. ".$afec["organizacion"]["actividad"]["cod"]." C. ".$afec["organizacion"]["componente"]["cod"]."...S/. ".number_format($monto,2,".", ","),'0','L');
				$this->SetFont('Arial','B',9);
				$this->SetXY(5,$y);$this->MultiCell(30,5,$afec["organizacion"]["nomb"],'0','L');
				$y=$this->GetY()+5;
			}
		}
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