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
		$this->SetXY(5,$y);$this->MultiCell(50,5,"SOCIEDAD DE BENEFICENCIA PÚBLICA DE AREQUIPA",'0','C');	
		$this->SetFont('Arial','B',16);
		$this->SetXY(10,$y);$this->MultiCell(195,5,"ORDEN DE COMPRA - GUIA DE INTERNAMIENTO",'0','R');
		$y=$y+10;
		//$this->SetFont('Arial','B',10);
		
		$this->SetFont('Arial','B',9);
		$this->SetXY(140,$y);$this->MultiCell(20,5,"Nº",'1','C');
		$this->SetXY(160,$y);$this->MultiCell(15,5,"DÍA",'1','C');
		$this->SetXY(175,$y);$this->MultiCell(15,5,"MES",'1','C');
		$this->SetXY(190,$y);$this->MultiCell(15,5,"AÑO",'1','C');
		$y=$y+5;
		$this->SetXY(100,$y);$this->MultiCell(40,5,$this->nro,'0','C');
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
		$this->SetFont('Arial','B',9);
		$this->Rect(5, $y, 200, 25);
		$this->SetXY(5,$y);$this->MultiCell(25,5,"Señor(es)",'0','L');
		$proveedor = $items["proveedor"]["nomb"];
		if($items["proveedor"]["tipo_enti"]=="P")$proveedor=$items["proveedor"]["nomb"]." ".$items["proveedor"]["appat"]." ".$items["proveedor"]["apmat"];
		$ruc = "--";
		if(isset($items["proveedor"]["docident"])){
			foreach($items["proveedor"]["docident"] as $doc){
				if($doc["tipo"]=="RUC")$ruc = $doc["num"];
			}
		}
		$this->SetFont('Arial','',9);
		$this->SetXY(25,$y);$this->MultiCell(175,5,$proveedor." - RUC: ".$ruc,'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(25,5,"Dirección",'0','L');
		$this->SetFont('Arial','',9);
		$direccion = '--';
		if(isset($items["proveedor"]["domicilios"])){
			$direccion = $items["proveedor"]["domicilios"][0]["direccion"];
		}
		$this->SetXY(30,$y);$this->MultiCell(175,5,$direccion,'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(80,5,"Le agradecemos enviar a nuestro almacén en",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(75,$y);$this->MultiCell(120,5,$items["almacen"]["nomb"],'0','L');
		$y=$y+5;
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(25,5,"Referencia",'0','L');
		$this->SetFont('Arial','',7);
		$requerimientos = array();
		if($items['cotizacion']!=null){
			print_r($items['cotizacion']);
			foreach($items['cotizacion']['requerimientos'] as $req){
				$requerimientos[] = $req['cod'];
			}
		}
		$requerimientos = implode(", ", $requerimientos);
		$this->SetXY(25,$y);$this->MultiCell(175,5,"REQ. Nº ".$requerimientos.", PROVEIDO Nº ".$items['solicitud']['cod'].", CERTIF. PRESUPUESTAL Nº ".$items['certificacion']['cod'],'0','L');
		$y=$y+5;
		
		$this->SetFont('Arial','B',9);
		$this->SetXY(5,$y);$this->MultiCell(175,5,"Facturar a nombre de: ",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(40,$y);$this->MultiCell(175,5,"SOCIEDAD DE BENEFICENCIA PUBLICA DE AREQUIPA",'0','L');
		$this->SetFont('Arial','B',9);
		$this->SetXY(174,$y);$this->MultiCell(175,5,"RUC: ",'0','L');
		$this->SetFont('Arial','',9);
		$this->SetXY(183,$y);$this->MultiCell(175,5,"20120958136",'0','L');
		$y=$y+5;
		/** Detalle de Orden de Compra */
		$this->SetFont('Arial','B',10);
		$this->SetXY(5,$y);$this->MultiCell(140,5,"1\nARTICULOS",'1','C');
		$this->SetXY(145,$y);$this->MultiCell(60,5,"2\nVALOR",'1','C');
		$y=$y+10;
		$this->SetXY(5,$y);$this->MultiCell(20,5,"A\nCódigo",'1','C');
		$this->SetXY(25,$y);$this->MultiCell(20,5,"B\nCantidad",'1','C');
		$this->Rect(45, $y, 20, 10);$this->SetXY(45,$y);$this->MultiCell(20,3.3,"C\nUnidad de Medida",'0','C');
		$this->SetXY(65,$y);$this->MultiCell(100,5,"d\nDESCRIPCION",'1','C');
		$this->SetXY(165,$y);$this->MultiCell(20,5,"A\nUnitario",'1','C');
		$this->SetXY(185,$y);$this->MultiCell(20,5,"B\nTOTAL",'1','C');
		$y=$y+10;
		$this->SetFont('Arial','',8);
		foreach($items["orden"]["productos"] as $item){
			if($y>$page_b){
				$this->AddPage();
				$y=$y_ini;
			}
			$alto = ceil(strlen($item["producto"]["nomb"])/50)*5;
			$this->Rect(5, $y, 20, $alto);$this->SetXY(5,$y);$this->MultiCell(20,5,$item["producto"]["cod"],'0','C');
			$this->Rect(25, $y, 20, $alto);$this->SetXY(25,$y);$this->MultiCell(20,5,$item["cant"],'0','C');
			$this->Rect(45, $y, 20, $alto);$this->SetXY(45,$y);$this->MultiCell(20,5,$item["producto"]["unidad"]["nomb"],'0','C');
			$this->Rect(65, $y, 100, $alto);$this->SetXY(65,$y);$this->MultiCell(60,5,$item["producto"]["nomb"],'0','L');
			$this->SetXY(125,$y);$this->MultiCell(20,5,$item["producto"]["clasif"]["cod"],'0','L');
			$this->SetXY(145,$y);$this->MultiCell(20,5,$item["producto"]["cuenta"]["cod"],'0','L');
			$this->Rect(165, $y, 20, $alto);$this->SetXY(165,$y);$this->MultiCell(20,5,number_format($item["precio"],2),'0','R');
			$this->Rect(185, $y, 20, $alto);$this->SetXY(185,$y);$this->MultiCell(20,5,number_format($item["subtotal"],2),'0','R');
			$y=$y+$alto;

		}
		/*foreach($items["clasificadores"] as $clasif){
			if($y>$page_b){
				$this->AddPage();
				$y=$y_ini;
			}
			$this->Rect(5, $y, 20, $alto);
			$this->Rect(25, $y, 20, $alto);
			$this->Rect(45, $y, 20, $alto);
			$this->Rect(65, $y, 80, $alto);$this->SetXY(65,$y);$this->MultiCell(40,5,$clasif["cod"],'0','L');
			//$this->SetXY(105,$y);$this->MultiCell(40,5,$clasif["cuenta"]["cod"],'0','L');
			$this->Rect(145, $y, 30, $alto);
			$this->Rect(175, $y, 30, $alto);
			$y+=5;
		}*/	
		$this->SetXY(65,$y);$this->MultiCell(100,5,"TOTAL",'0','R');
		$this->Rect(165, $y, 20, $alto);
		$this->SetXY(185,$y);$this->MultiCell(20,5,number_format($items["orden"]["precio_total"],2,".", ","),'1','R');
		$y+=5;
		$this->SetXY(5,$y);$this->MultiCell(200,5,"OBSERVACIONES.- ".$items["orden_observ"],'0','L');
		$y+=5;
		$this->SetXY(5,$y);$this->MultiCell(200,5,"FUENTE DE FINANCIAMIENTO.- ".$items["fuente"]["cod"]." ".$items["fuente"]["rubro"],'0','L');
		$y=$y+10;
		/** Firmas */
		/*if($y+60>$page_b){
			$this->AddPage();
			$y=$y_ini;
		}*/
		$y = 220;
		$this->SetXY(5,$y);$this->MultiCell(100,5,"ORDENACION DE LA COMPRA",'1','C');
		$this->SetXY(105,$y);$this->MultiCell(50,5,"Afectación Presupuestal",'1','C');
		$this->SetXY(155,$y);$this->MultiCell(50,5,"Distribución Contable",'1','C');
		$y=$y+5;
		$this->SetXY(5,$y);$this->MultiCell(50,5,"\n\n\n\n",'1','C');//\n\n__________________________\nJefe de Adquisiciones
		$this->SetXY(7,$y+9);$this->MultiCell(5,5,"1",'1','C');
		$this->SetXY(55,$y);$this->MultiCell(50,5,"\n\n__________________________\nJefe de Abastecimiento",'1','C');//\n\n__________________________\nJefe de Abastecimiento y Servicio
		$this->SetXY(57,$y+9);$this->MultiCell(5,5,"2",'1','C');
		$this->SetXY(105,$y);$this->MultiCell(50,10,"Programa\nSub Progra",'1','L');
		if(count($items["orden"]["afectacion"])==1){
			$this->SetXY(125,$y);$this->MultiCell(25,10,"A".$items["orden"]["afectacion"][0]["programa"]["actividad"]["cod"]."\nC".$items["orden"]["afectacion"][0]["programa"]["componente"]["cod"],'0','L');
		}else{
			$this->SetXY(125,$y);$this->MultiCell(25,10,"--\n--",'0','L');
		}
		//$this->SetXY(102,$y+9);$this->MultiCell(5,5,"2",'1','C');
		$this->SetXY(155,$y);$this->MultiCell(50,5,"CUENTAS POR PAGAR\n\n\n__________________________",'1','C');
		$this->SetXY(157,$y+10);$this->MultiCell(50,5,"S/. ".number_format($items["orden"]["precio_total"],2,".", ","),'0','C');
		$y=$y+20;
		$this->Rect(5, $y, 100, 40);
		$this->SetXY(5,$y);$this->MultiCell(20,10,"NOTA:",'0','L');
		$this->SetXY(15,$y+2.5);$this->MultiCell(90,5,"Cada orden de compra se debe facturar por separado en original y dos (2) copias y remitidas a la Dirección de Presupuesto y Contabilidad.
		Nos Reservamos el derecho de devolver la mercaderia que no está de acuerdo con nuestras especificaciones",'0','L');
		$this->Rect(105, $y, 100, 40);
		$this->SetXY(105,$y);$this->MultiCell(100,5,"RECIBI CONFORME:",'0','L');
		$this->SetXY(150,$y+5);$this->MultiCell(50,5,"\n\n__________________________\nJefe de Almacén",'0','C');
		$this->SetXY(152,$y+14);$this->MultiCell(5,5,"3",'1','C');
		
		$this->SetXY(105,$y+25);$this->MultiCell(15,8,"DIA",'1','C');
		$this->SetXY(120,$y+25);$this->MultiCell(15,8,"MES",'1','C');
		$this->SetXY(135,$y+25);$this->MultiCell(15,8,"AÑO",'1','C');
		$this->SetXY(105,$y+33);$this->MultiCell(15,7,"",'1','C');
		$this->SetXY(120,$y+33);$this->MultiCell(15,7,"",'1','C');
		$this->SetXY(135,$y+33);$this->MultiCell(15,7,"",'1','C');
		/** Afectaciones */
		$num_afec = count($items["orden"]["afectacion"]);
		if($num_afec>1){
			$this->AddPage();
			$y=$y_ini;
			$this->SetFont('Arial','B',10);
			$this->SetXY(5,$y);$this->MultiCell(195,5,"AFECTACION PRESUPUESTAL",'0','C');
			$y=$y+10;
			foreach($items["orden"]["afectacion"] as $afec){
				$monto = 0;
				foreach($afec["gasto"] as $gasto){
					$monto=$monto+$gasto["monto"];
				}	
				$this->SetFont('Arial','',9);
				$this->SetXY(35,$y);$this->MultiCell(80,5,"ACT. ".$afec["programa"]["actividad"]["cod"]." C. ".$afec["programa"]["componente"]["cod"]."...S/. ".number_format($monto,2,".", ","),'0','L');
				$this->SetFont('Arial','B',9);
				$this->SetXY(5,$y);$this->MultiCell(30,5,$afec["programa"]["nomb"],'0','L');
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